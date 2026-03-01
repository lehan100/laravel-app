<?php

namespace App\Repositories\Product;

// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Repositories\EloquentRepository;
use App\Models\ProductofCategory;
use App\Models\ProductOfOptionEntries;
use App\Helpers\Price as Price;
use App\Models\EntypeContent;
use App\Models\ProductOption;
use App\Models\ProductOptionAttribute;
use App\Models\ProductAttributeSet;
use App\Models\Inventory;
use App\Models\UrlRewrite;
use App\Helpers\Category as CategoryHelper;
use App\Repositories\Category\CategoryEloquentRepository;
use Exception;

class ProductEloquentRepository extends EloquentRepository implements ProductRepositoryInterface
{

    protected $cachePrefix = 'products-';
    protected $dateNowDMY;
    protected $dateNow;

    public function __construct()
    {
        parent::__construct();
        $this->dateNowDMY = Carbon::now('Asia/Ho_Chi_Minh')->format("d-m-Y");
        $this->dateNow = Carbon::now('Asia/Ho_Chi_Minh');
    }
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Product::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select('id', 'name', 'sku', 'quantity', 'stock', 'weight', 'price', 'picture', 'status')
                ->with(array('category' => function ($query) {
                    $query->select('categories.id', 'categories.name')->where("parent_id", '!=', 0);
                }))
                ->orderBy('id', 'DESC');
            if (isset($params['filter']) && count($params['filter']) > 0) {
                foreach ($params['filter'] as $key => $filter) {
                    if ($key == 'category_id') {
                        if ($filter != 0) {
                            $query->whereHas('category', function ($query) use ($filter) {
                                $query->whereIn('categories.id', $filter);
                            });
                        }
                    } else if ($key == 'status' || $key == 'stock') {
                        $query->where($key, $filter);
                    } else {
                        $query->where($key, 'like', "%{$filter}%");
                    }
                }
            }
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == "admin-list-items-selector") {

            $keycache = $this->cachePrefix . $options['task'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select('id', 'name', 'alias', 'sku', 'stock', 'quantity', 'price', 'special_price', 'special_price_from', 'special_price_to', 'picture', 'status')
                    ->with(array('category' => function ($query) {
                        $query->select('categories.id', 'categories.name')->where("parent_id", '!=', 0);
                    }))
                    ->orderBy('id', 'DESC');
                return $query->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "admin-list-items-report") {
            $query = $this->_model->select('id', 'name', 'sku', 'quantity', 'stock', 'weight', 'price', "hit_viewer", "hit_order", 'picture', 'status')
                ->with("url")
                ->where("status", 1);
            if (isset($params['hit']) && $params['hit'] == 'hit_viewer') {
                $query->orderBy('hit_viewer', 'DESC');
            }
            if (isset($params['hit']) && $params['hit'] == 'hit_order') {
                $query->orderBy('hit_order', 'DESC');
            }

            $result = $query->take(20)->get();
        }
        if ($options['task'] == "admin-list-items-sitemap") {
            $keycache = $this->cachePrefix . $options['task'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select('id', 'name', 'picture')
                    ->with("url")
                    ->whereHas('category', function ($query) {
                        $query->where('status', 1);
                    })
                    ->where("status", 1);
                return $query->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }

        if ($options['task'] == "frontend-list-items") {
            $category_id = $params['id_categories'];
            $page = isset($params['page']) ? $params['page'] : 0;
            $autopage = isset($params['autopage']) ? $params['autopage'] : false;
            $strFilter = "";
            if ($params['filter_category'] != null) {
                $strFilter .= $params['filter_category'];
            }
            if ($params['filter_attribute'] != null) {
                foreach ($params['filter_attribute'] as $key => $val) {
                    $strFilter .= $key . Arr::join($val, '-');
                }
            }
            if ($params['filter_price'] != null) {
                $strFilter .= $params['filter_price'];
            }
            $dataKeyCache = [
                $this->cachePrefix,
                $options['task'],
                Arr::join($category_id, '-'),
                $strFilter,
                $params['sort'],
                $page,
                $autopage,
                $this->dateNowDMY
            ];
            $keycache = Arr::join($dataKeyCache, "-");
            $result = Cache::store('custom')->get($keycache, function () use ($params, $category_id) {
                $sort = $params['sort'];
                $query = $this->_model->select('id', 'name', 'quantity', 'price', 'special_price', 'special_price_from', 'special_price_to', 'picture', 'status')
                    ->with(array(
                        'url',
                        'sales' => function ($query) {
                            $query
                                ->with("info")
                                ->whereHas(
                                    "info",
                                    function ($query) {
                                        $query->whereDate('date_from', '<=', $this->dateNow)
                                            ->whereDate('date_to', '>=', $this->dateNow)
                                            ->where('status', '=', '1');
                                    }
                                );
                        },
                        'ratings' => function ($query) {
                            $query->select('product_id', DB::raw("SUM(rating) as sum_star"), DB::raw("Count(id) as total_rating"))->groupBy("product_id");
                        }
                    ))
                    ->where('status', '=', '1');

                if ($params['filter_attribute'] != null) {
                    $filter_attribute = $params['filter_attribute'];
                    foreach ($filter_attribute as $alias => $attribute) {
                        $query->whereHas('attibute_sets', function ($query) use ($alias, $attribute) {
                            $query->whereIn($alias,  $attribute);
                        });
                    }
                }
                if ($params['filter_category'] != null) {

                    $filter_category = explode(",", $params['filter_category']);

                    $query->whereHas('category', function ($query) use ($filter_category) {
                        $query->whereIn('categories.id', $filter_category);
                    });
                } elseif (count($category_id) > 0) {
                    $query->whereHas('category', function ($query) use ($category_id) {
                        $query->whereIn('categories.id', $category_id);
                    });
                }
                if ($params['filter_price'] != null) {
                    $filter_price = explode(",", $params['filter_price']);
                    $priceMin = 0;
                    $priceMax = 0;
                    foreach ($filter_price as $k => $filter) {
                        $arrFilterPrice = explode("-", $filter);
                        if ($k == 0) {
                            $priceMin = $arrFilterPrice[0];
                            $priceMax = $arrFilterPrice[1];
                        } else {
                            $priceMax = $arrFilterPrice[1];
                        }
                    }
                    $query->whereBetween('price', [$priceMin, $priceMax]);
                }

                if (isset($sort)) {
                    switch ($sort) {
                        case "position":
                            $query->orderBy('id', 'DESC');
                            break;
                        case "hot":
                            $query->orderBy('hit_viewer', 'DESC');
                            break;
                        case "best_seller":
                            $query->orderBy('hit_order', 'DESC');
                            break;
                        case "price_heigh_to_low":
                            $query->orderBy('price', 'DESC');
                            break;
                        case "price_low_to_high":
                            $query->orderBy('price', 'ASC');
                            break;
                    }
                } else {
                    $query->orderBy('id', 'DESC');
                }
                return $query->paginate($params['pagination']['totalItemsPerPage']);
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-list-items-search") {
            $page = isset($params['page']) ? $params['page'] : 0;
            $strFilter = "";
            if ($params['filter_category'] != null) {
                $strFilter .= $params['filter_category'];
            }
            if ($params['filter_attribute'] != null) {
                foreach ($params['filter_attribute'] as $key => $val) {
                    $strFilter .= $key . Arr::join($val, '-');
                }
            }
            if ($params['filter_price'] != null) {
                $strFilter .= $params['filter_price'];
            }
            $autopage = isset($params['autopage']) ? $params['autopage'] : false;
            $dataKeyCache = [
                $this->cachePrefix,
                $options['task'],
                Str::slug($params['keyword']),
                $strFilter,
                $params['sort'],
                $page,
                $autopage,
                $this->dateNowDMY
            ];
            $keycache = Arr::join($dataKeyCache, "-");
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $keyword = $params['keyword'];
                $sort = $params['sort'];
                $query = $this->_model->select('id', 'name', 'quantity', 'price', 'special_price', 'special_price_from', 'special_price_to', 'picture', 'status')
                    ->with(array(
                        'url',
                        'sales' => function ($query) {
                            $query
                                ->with("info")
                                ->whereHas(
                                    "info",
                                    function ($query) {
                                        $query->whereDate('date_from', '<=', $this->dateNow)
                                            ->whereDate('date_to', '>=', $this->dateNow)
                                            ->where('status', '=', '1');
                                    }
                                );
                        },
                        'ratings' => function ($query) {
                            $query->select('product_id', DB::raw("SUM(rating) as sum_star"), DB::raw("Count(id) as total_rating"))->groupBy("product_id");
                        }
                    ))
                    ->where('status', '=', '1')
                    ->where(function ($query) use ($keyword) {
                        return $query->where('name', 'like', "%{$keyword}%")->orWhere('name_ascii', 'like', "%{$keyword}%")->orWhere('sku', 'like', "%{$keyword}%");
                    });

                if ($params['filter_price'] != null) {
                    $filter_price = explode(",", $params['filter_price']);
                    $priceMin = 0;
                    $priceMax = 0;
                    foreach ($filter_price as $k => $filter) {
                        $arrFilterPrice = explode("-", $filter);
                        if ($k == 0) {
                            $priceMin = $arrFilterPrice[0];
                            $priceMax = $arrFilterPrice[1];
                        } else {
                            $priceMax = $arrFilterPrice[1];
                        }
                    }
                    $query->whereBetween('price', [$priceMin, $priceMax]);
                }
                if ($params['filter_attribute'] != null) {
                    $filter_attribute = $params['filter_attribute'];
                    foreach ($filter_attribute as $alias => $attribute) {
                        $query->whereHas('attibute_sets', function ($query) use ($alias, $attribute) {
                            $query->whereIn($alias,  $attribute);
                        });
                    }
                }
                if (isset($sort)) {
                    switch ($sort) {
                        case "position":
                            $query->orderBy('id', 'DESC');
                            break;
                        case "hot":
                            $query->orderBy('id', 'DESC');
                            break;
                        case "best_seller":
                            $query->orderBy('id', 'DESC');
                            break;
                        case "price_heigh_to_low":
                            $query->orderBy('price', 'DESC');
                            break;
                        case "price_low_to_high":
                            $query->orderBy('price', 'ASC');
                            break;
                    }
                } else {
                    $query->orderBy('id', 'DESC');
                }
                return $query->paginate($params['pagination']['totalItemsPerPage']);
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-list-items-id") {
            $page = isset($params['page']) ? $params['page'] : 0;
            $category_id = isset($params['id_categories']) ? $params['id_categories'] : null;
            $category_id_cache = isset($params['id_categories']) ? Arr::join($category_id, '-') : null;
            $keyword = isset($params['keyword']) ? $params['keyword'] : null;
            $strFilter = "";
            if (isset($params['filter_category']) && $params['filter_category'] != null) {
                $strFilter .= $params['filter_category'];
            }
            if (isset($params['filter_attribute']) && $params['filter_attribute'] != null) {
                foreach ($params['filter_attribute'] as $key => $val) {
                    $strFilter .= $key . Arr::join($val, '-');
                }
            }
            if (isset($params['filter_price']) && $params['filter_price'] != null) {
                $strFilter .= $params['filter_price'];
            }
            $autopage = isset($params['autopage']) ? $params['autopage'] : false;
            $sale_id = isset($params['sale_id']) ?  $params['sale_id'] : '';
            $dataKeyCache = [
                $this->cachePrefix,
                $options['task'],
                $sale_id,
                $strFilter,
                $keyword,
                $params['sort'],
                $category_id_cache,
                $page,
                $autopage
            ];
            $keycache = Arr::join($dataKeyCache, "_");
            //$keycache = $this->cachePrefix . $options['task'] . "-" . $category_id_cache . "_" . $keyword . "-page-" . $page;
            $result = Cache::store('custom')->get($keycache, function () use ($params, $category_id, $keyword, $sale_id) {
                $query = $this->_model->select('id')
                    ->where('status', '=', '1');
                // if ($category_id != null) {
                //     $query->whereHas('category', function ($query) use ($category_id) {
                //         $query->whereIn('categories.id', $category_id);
                //     });
                // }
                if ($keyword != null) {
                    $query->where(function ($query) use ($keyword) {
                        return $query->where('name', 'like', "%{$keyword}%")->orWhere('sku', 'like', "%{$keyword}%");
                    });
                }
                if ($sale_id != '') {
                    $query->whereHas('sales', function ($query) use ($sale_id) {
                        $query->where('product_sales_id',  $sale_id);
                    });
                }
                if (isset($params['filter_attribute']) && $params['filter_attribute'] != null) {
                    $filter_attribute = $params['filter_attribute'];
                    foreach ($filter_attribute as $alias => $attribute) {
                        $query->whereHas('attibute_sets', function ($query) use ($alias, $attribute) {
                            $query->whereIn($alias,  $attribute);
                        });
                    }
                }
                if (isset($params['filter_category']) && $params['filter_category'] != null) {

                    $filter_category = explode(",", $params['filter_category']);

                    $query->whereHas('category', function ($query) use ($filter_category) {
                        $query->whereIn('categories.id', $filter_category);
                    });
                } elseif ($category_id != null) {
                    $query->whereHas('category', function ($query) use ($category_id) {
                        $query->whereIn('categories.id', $category_id);
                    });
                }
                if (isset($params['filter_price']) && $params['filter_price'] != null) {
                    $filter_price = explode(",", $params['filter_price']);
                    $priceMin = 0;
                    $priceMax = 0;
                    foreach ($filter_price as $k => $filter) {
                        $arrFilterPrice = explode("-", $filter);
                        if ($k == 0) {
                            $priceMin = $arrFilterPrice[0];
                            $priceMax = $arrFilterPrice[1];
                        } else {
                            $priceMax = $arrFilterPrice[1];
                        }
                    }
                    $query->whereBetween('price', [$priceMin, $priceMax]);
                }
                $query->orderBy('id', 'asc');
                return $query->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result, Carbon::now()->addMinutes(10));
            }
        }
        if ($options['task'] == "frontend-main-list-items-limit") {
            $category_id = $params['category_id'];
            $keycache = $this->cachePrefix . $options['task'] . Arr::join($category_id, '_') . $this->dateNowDMY;
            $result = Cache::store('custom')->get($keycache, function () use ($params, $category_id) {
                $query = $this->_model->select('id', 'name', 'quantity', 'price', 'special_price', 'special_price_from', 'special_price_to', 'picture', 'status')
                    ->with(array(
                        'url',
                        'sales' => function ($query) {
                            $query
                                ->with("info")
                                ->whereHas(
                                    "info",
                                    function ($query) {
                                        $query->whereDate('date_from', '<=', $this->dateNow)
                                            ->whereDate('date_to', '>=', $this->dateNow)
                                            ->where('status', '=', '1');
                                    }
                                );
                        },
                        'ratings' => function ($query) {
                            $query->select('product_id', DB::raw("SUM(rating) as sum_star"), DB::raw("Count(id) as total_rating"))->groupBy("product_id");
                        }
                    ))
                    ->where('status', '=', '1')
                    ->whereHas('category', function ($query) use ($category_id) {
                        $query->whereIn('categories.id', $category_id);
                    })
                    ->orderBy('id', 'DESC');
                return $query->limit(10)->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-list-items-sale") {
            $page = isset($params['page']) ? $params['page'] : 0;
            $strFilter = "";
            if ($params['filter_category'] != null) {
                $strFilter .= $params['filter_category'];
            }
            if ($params['filter_attribute'] != null) {
                foreach ($params['filter_attribute'] as $key => $val) {
                    $strFilter .= $key . Arr::join($val, '-');
                }
            }
            if ($params['filter_price'] != null) {
                $strFilter .= $params['filter_price'];
            }
            $autopage = isset($params['autopage']) ? $params['autopage'] : false;
            $dataKeyCache = [
                $this->cachePrefix,
                $options['task'],
                $params['id'],
                $strFilter,
                $params['sort'],
                $page,
                $autopage
            ];
            $keycache = Arr::join($dataKeyCache, "-");
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $sort = $params['sort'];
                $query = $this->_model->select('id', 'name', 'quantity', 'price', 'special_price', 'special_price_from', 'special_price_to', 'picture', 'status')
                    ->with('url')
                    ->with(array('ratings' => function ($query) {
                        $query->select('product_id', DB::raw("SUM(rating) as sum_star"), DB::raw("Count(id) as total_rating"))->groupBy("product_id");
                    }))
                    ->whereHas('sales', function ($query) use ($params) {
                        $query->where('product_sales_id',  $params['sale_id']);
                    })
                    ->where('status', '=', '1');

                if ($params['filter_price'] != null) {
                    $filter_price = explode(",", $params['filter_price']);
                    $priceMin = 0;
                    $priceMax = 0;
                    foreach ($filter_price as $k => $filter) {
                        $arrFilterPrice = explode("-", $filter);
                        if ($k == 0) {
                            $priceMin = $arrFilterPrice[0];
                            $priceMax = $arrFilterPrice[1];
                        } else {
                            $priceMax = $arrFilterPrice[1];
                        }
                    }
                    $query->whereBetween('price', [$priceMin, $priceMax]);
                }
                if ($params['filter_attribute'] != null) {
                    $filter_attribute = $params['filter_attribute'];
                    foreach ($filter_attribute as $alias => $attribute) {
                        $query->whereHas('attibute_sets', function ($query) use ($alias, $attribute) {
                            $query->whereIn($alias,  $attribute);
                        });
                    }
                }
                if (isset($sort)) {
                    switch ($sort) {
                        case "position":
                            $query->orderBy('id', 'DESC');
                            break;
                        case "hot":
                            $query->orderBy('id', 'DESC');
                            break;
                        case "best_seller":
                            $query->orderBy('id', 'DESC');
                            break;
                        case "price_heigh_to_low":
                            $query->orderBy('price', 'DESC');
                            break;
                        case "price_low_to_high":
                            $query->orderBy('price', 'ASC');
                            break;
                    }
                } else {
                    $query->orderBy('id', 'DESC');
                }
                return $query->paginate($params['pagination']['totalItemsPerPage']);
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "fronend-get-all") {
            $keycache = $this->cachePrefix . $options['task'] . $this->dateNowDMY;
            $result = Cache::store('custom')->get($keycache, function () {
                $query = $this->_model->select('id', 'name', 'name_ascii', 'sku', 'price', 'special_price', 'special_price_from', 'special_price_to', 'picture')
                    ->with(array(
                        'url',
                        'sales' => function ($query) {
                            $query
                                ->with("info")
                                ->whereHas(
                                    "info",
                                    function ($query) {
                                        $query->whereDate('date_from', '<=', $this->dateNow)
                                            ->whereDate('date_to', '>=', $this->dateNow)
                                            ->where('status', '=', '1');
                                    }
                                );
                        }
                    ))
                    ->where('status', '=', '1')
                    ->orderBy('id', 'asc');
                return $query->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-get-items-gift-buySku") {
            $skus = explode(",", $params['sku']);
            $query = $this->_model->select('id', 'name', 'sku', 'quantity', 'stock', 'picture')
                ->where('status', '=', '1')
                ->whereIn('sku', $skus);
            return $query->get();
        }
        if ($options['task'] == "frontend-check-stocks") {
            $query = $this->_model->select('id', 'name', 'quantity', 'stock')
                ->where('status', '=', '1')
                ->where("quantity", 0)
                ->orwhere("stock", 0)
                ->whereIn('id', $params['ids']);
            return $query->get();
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select()->where('id', $params['id'])
                ->with(array(
                    'category' => function ($query) {
                        $query->select('categories.id', 'categories.name');
                    },
                    'option_entries' => function ($query) {
                        $query->select('product_option_entries.id')->orderBy('product_of_option_entries.order', 'asc');
                    },
                    'options' => function ($query) {
                        $query->with(array('attributes' => function ($query) {
                            $query->orderBy('sort', 'asc');
                        }))
                            ->orderBy('sort', 'asc');
                    },
                    'attibute_sets',
                    'contents'
                ))
                ->first();
            //echo "<pre>"; print_r($result->toArray());die();
        }
        if ($options['task'] == 'frontend-get-item') {
            $result = $this->_model->select()->where('id', $params['id'])
                ->with(array(
                    'category' => function ($query) {
                        $query->select('categories.id', 'categories.name');
                    },
                    'options' => function ($query) {
                        $query->with(array('attributes' => function ($query) {
                            $query->orderBy('sort', 'asc');
                        }))
                            ->orderBy('sort', 'asc');
                    },
                    'option_entries' => function ($query) {
                        $query->with('attributes')->where("status", 1)->orderBy('product_of_option_entries.order', 'asc');
                    },
                    'attibute_sets' => function ($query) {
                        $query->with('label')->with('value')->whereIn("alias", ['brand']);
                    },
                    'sales' => function ($query) {
                        $query
                            ->with("info")
                            ->whereHas(
                                "info",
                                function ($query) {
                                    $query->whereDate('date_from', '<=', $this->dateNow)
                                        ->whereDate('date_to', '>=', $this->dateNow)
                                        ->where('status', '=', '1');
                                }
                            );
                    },
                    'tier_prices' => function ($query) {
                        $query
                            ->with(array("tier_price" => function ($query) {
                                $query->with("items");
                            }))
                            ->whereHas(
                                "tier_price",
                                function ($query) {
                                    $query->whereDate('date_from', '<=', $this->dateNow)
                                        ->whereDate('date_to', '>=', $this->dateNow)
                                        ->where('status', '=', '1');
                                }
                            );
                    },
                    'inventory',
                    'contents'
                ))
                ->first();
            // echo "<pre>";
            // print_r($result->toArray());
            // die();
        }
        if ($options['task'] == 'item-in-cart') {
            $query = $this->_model->select()
                ->where('id', $params['id'])
                ->with(array(
                    'url',
                    'sales' => function ($query) use ($params) {
                        $query
                            ->with("info")
                            ->whereHas(
                                "info",
                                function ($query) {
                                    $query->whereDate('date_from', '<=', $this->dateNow)
                                        ->whereDate('date_to', '>=', $this->dateNow)
                                        ->where('status', '=', '1');
                                }
                            );
                        if ($params['sale_id'] != null) {
                            $query->where("id", $params['sale_id']);
                        }
                    },
                    'tier_prices' => function ($query) {
                        $query
                            ->with(array("tier_price" => function ($query) {
                                $query->with("items");
                            }))
                            ->whereHas(
                                "tier_price",
                                function ($query) {
                                    $query->whereDate('date_from', '<=', $this->dateNow)
                                        ->whereDate('date_to', '>=', $this->dateNow)
                                        ->where('status', '=', '1');
                                }
                            );
                    },
                ));
            if (isset($params['options']) && count($params['options']) > 0) {
                $query->with(array('options' => function ($query) use ($params) {
                    $query->with(array('attributes' => function ($query) use ($params) {
                        $query->whereIn('product_option_attributes.id', $params['options'])
                            ->orderBy('sort', 'asc');
                    }))
                        ->orderBy('sort', 'asc');
                }));
            }
            if (isset($params['option_entries']) && count($params['option_entries']) > 0) {
                $query->with(array('option_entries' => function ($query) use ($params) {
                    $query->with(array('attributes' => function ($query) use ($params) {
                        $query->whereIn('id', $params['option_entries']);
                    }));
                }));
            }
            $result = $query->first("name");
        }
            //    echo '<pre>';
            //    print_r($result->toArray());
            //    die();
        return $result;
    }

    // @Override
    public function saveItem($params = null, $options = null)
    {
        $result = 0;
        if ($options['task'] == "admin-update-multi-status") {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $result = $this->_model->whereIn('id', $params['aid'])->update(['status' => $params['value']]);
            }
            return ($result > 0) ? $result : FALSE;
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->_model->where('id', $params['id'])->update(['status' => $status]);
            return ($result > 0) ? $result : FALSE;
        }
        if ($options['task'] == 'update-hit') {
            $this->_model->where('id', $params['id'])->update(['hit_viewer' => DB::raw('hit_viewer+1')]);
        }
        if ($options['task'] == 'update-hit-order') {
            $this->_model->where('id', $params['id'])->update(['hit_order' => DB::raw('hit_order+' . $params['qty'])]);
        }
        if ($options['task'] == 'update-qty') {
            $this->_model->where('id', $params['id'])->update(['quantity' =>  $params['qty']]);
        }
        // DB::beginTransaction();
        // try {
        if ($options['task'] == 'add-item' || $options['task'] == 'edit-item') {
            $alias = (isset($params['alias']) && $params['alias'] != "") ? $params['alias'] : \App\Helpers\Filter::setUrlKey($params['name']);
            $special_price = (isset($params['special_price']) && $params['special_price'] != "") ? $params['special_price'] : 0;
        }

        if ($options['task'] == 'add-item') {
            $entype_id = Str::random(10);
            $row = $this->_model;
            $row->name = $params['name'];
            $row->name_ascii = Str::of($params['name'])->ascii();
            $row->sku = $params['sku'];
            $row->alias = $alias;
            $row->quantity = $params['quantity'];
            $row->stock = $params['stock'];
            $row->weight = $params['weight'];
            $row->price = Price::getPrice($params['price']);
            $row->special_price = Price::getPrice($special_price);

            if ($params['special_date'] != "") {
                $special_date = explode('-', $params['special_date']);
                $row->special_price_from = Carbon::parse($special_date[0]);
                $row->special_price_to = Carbon::parse($special_date[1]);
            } else {
                $row->special_price_from = null;
                $row->special_price_to = null;
            }
            $row->picture = (isset($params['images']) && count($params['images']) > 0) ? json_encode($params['images']) : null;
            $row->status = isset($params['status']) ? 1 : 0;
            $row->use_coupon = isset($params['use_coupon']) ? 1 : 0;
            $row->entype_id = $entype_id;
            $row->save();
            $id = $row->id;
            //Save Content
            $data_entype = array(
                'entype_id' => $entype_id,
                'sort_content' => $params['sort_content'],
                'content' => $params['content'],
                'title' => $params['title'],
                'keyword' => $params['keyword'],
                'description' => $params['description']
            );
            (new EntypeContent())->insert($data_entype);
            //Save Table Join category
            if (isset($params['cat_id']) && count($params['cat_id']) > 0) {
                $productOfCategory = new ProductofCategory();
                foreach ($params['cat_id'] as $val) {
                    $productOfCategory->insert(['category_id' => $val, 'product_id' => $id]);
                    $this->setUrlRewrite($params, $val, $id);
                }
            }
            //Save Table join option Entries
            if (isset($params['optionEntriesId']) && count($params['optionEntriesId']) > 0) {
                $ProductOfOptionEntries = new ProductOfOptionEntries();
                foreach ($params['optionEntriesId'] as $k => $val) {
                    $ProductOfOptionEntries->updateOrCreate(['product_option_entries_id' => (int) $val, 'product_id' => $id, 'order' => $k]);
                }
            }
            //Save Attribute Set
            $this->saveAttributesSet($params, $id);
            //Save Picture
            event(new \App\Events\ProductResizeImage($params));
            //Save Options
            //Code
            DB::commit();
            return $id;
        }

        if ($options['task'] == 'edit-item') {
            $entype_id = $params['entype_id'];
            $row = $this->_model::where('id', $params['id'])->first();
            $row->sku = $params['sku'];
            $row->name = $params['name'];
            $row->name_ascii = Str::of($params['name'])->ascii();
            $row->alias = $alias;
            $row->quantity = $params['quantity'];
            $row->stock = $params['stock'];
            $row->weight = $params['weight'];
            $row->price = Price::getPrice($params['price']);
            $row->special_price = Price::getPrice($special_price);
            if ($params['special_date'] != "") {
                $special_date = explode('-', $params['special_date']);
                $row->special_price_from = Carbon::parse($special_date[0]);
                $row->special_price_to = Carbon::parse($special_date[1]);
            } else {
                $row->special_price_from = null;
                $row->special_price_to = null;
            }
            $row->picture = (isset($params['images']) && count($params['images']) > 0) ? json_encode($params['images']) : null;
            $row->status = isset($params['status']) ? 1 : 0;
            $row->use_coupon = isset($params['use_coupon']) ? 1 : 0;
            $row->save();
            $id = $row->id;
            //Save Content
            $data_entype = array(
                'sort_content' => $params['sort_content'],
                'content' => $params['content'],
                'title' => $params['title'],
                'keyword' => $params['keyword'],
                'description' => $params['description']
            );
            (new EntypeContent())->updateOrCreate(['entype_id' => $entype_id], $data_entype);

            //Save Table Join category
            if (isset($params['cat_id']) && count($params['cat_id']) > 0) {
                $productOfCategory = new ProductofCategory();
                foreach ($params['cat_id'] as $val) {
                    $list = $productOfCategory->select("id")->where('category_id', $val)->where("product_id", $params['id'])->first();
                    if (!$list) {
                        $productOfCategory->insert(['category_id' => $val, 'product_id' => $id]);
                    }
                    $this->setUrlRewrite($params, $val, $params['id']);
                }
                $category_old = json_decode($params['category_old'], true);
                if (count($category_old) > 0) {
                    foreach ($category_old as $item) {
                        if (!in_array($item['id'], $params['cat_id'])) {
                            $productOfCategory->where('category_id', $item['id'])->where("product_id", $id)->delete();
                            $this->deleteUrlRewrite($item['id'], $id);
                        }
                    }
                }
            }
            //Save Table join option Entries
            if (isset($params['optionEntriesId']) && count($params['optionEntriesId']) > 0) {
                $ProductOfOptionEntries = new ProductOfOptionEntries();
                // print_r($params['optionEntriesId']);die();
                foreach ($params['optionEntriesId'] as $k => $val) {
                    $ProductOfOptionEntries->updateOrCreate(['product_option_entries_id' => (int) $val, 'product_id' => $id], ['product_option_entries_id' => (int) $val, 'product_id' => $id, 'order' => $k]);
                }
                $ProductOfOptionEntries->where('product_id', $id)->whereNotIn('product_option_entries_id', $params['optionEntriesId'])->delete();
            }
            //Save Attribute Set
            $this->saveAttributesSet($params, $id);
            //Save Picture
            event(new \App\Events\ProductResizeImage($params));

            //Code
            DB::commit();
            return $id;
        }
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return FALSE;
        // }
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
        DB::beginTransaction();
        $tbProductOfCategory = new ProductofCategory();
        $tbEntypeContent = new EntypeContent();
        $tbProductOption = new ProductOption();
        $tbInventory = new Inventory();
        try {
            //            $result = 0;
            if ($options['task'] == 'delete-item-multi') {
                if (isset($params['aid']) && count($params['aid']) > 0) {
                    $items = $this->_model->select("id", 'entype_id', 'picture')
                        ->with(array('options' => function ($query) {
                            $query->with('attributes')->orderBy('sort', 'asc');
                        }))
                        ->whereIn('id', $params['aid'])
                        ->get();
                    if (count($items) > 0) {
                        $this->_model->whereIn('id', $params['aid'])->delete();
                        $tbProductOfCategory->whereIn("product_id", $params['aid'])->delete();
                        $tbProductOption->whereIn("product_id", $params['aid'])->delete();
                        $tbInventory->whereIn("product_id", $params['aid'])->delete();
                        foreach ($items as $item) {
                            $tbEntypeContent->where("entype_id", $item['entype_id'])->delete();
                            $this->deleteUrlRewriteOfId($item['id']);
                            if ($item['picture'] != "") {
                                $picture = json_decode($item['picture'], true);
                                if (count($picture) > 0) {
                                    foreach ($picture as $val) {
                                        \App\Helpers\FileUpload::moveTrash($val);
                                    }
                                }
                            }
                            $productOptions = $item->options;
                            if (count($productOptions) > 0) {
                                foreach ($productOptions as $option) {
                                    (new ProductOptionAttribute())->whereIn('option_id', [$option->id])->delete();
                                    event(new \App\Events\ProductTrashImageOption($option->attributes));
                                    //                                    $this->deletePictureOption($option->attributes);
                                }
                            }
                        }

                        DB::commit();
                    }
                }
            }
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $item = $this->_model->select("id", 'entype_id', 'picture')
                        ->with(array('options' => function ($query) {
                            $query->with('attributes')->orderBy('sort', 'asc');
                        }))
                        ->where("id", $params['id'])
                        ->first();
                    if ($item) {
                        $tbEntypeContent->where("entype_id", $item['entype_id'])->delete();
                        $tbProductOfCategory->where("product_id", $params['id'])->delete();
                        $tbProductOption->where("product_id", $params['id'])->delete();
                        $tbInventory->where("product_id", $params['id'])->delete();
                        $this->deleteUrlRewriteOfId($params['id']);
                        $this->_model->where('id', $params['id'])->delete();
                        $productOptions = $item->options;
                        if (count($productOptions) > 0) {
                            foreach ($productOptions as $option) {
                                (new ProductOptionAttribute())->whereIn('option_id', [$option->id])->delete();
                                event(new \App\Events\ProductTrashImageOption($option->attributes));
                                //                                $this->deletePictureOption($option->attributes);
                            }
                        }
                        DB::commit();
                        if ($item['picture'] != "") {
                            $picture = json_decode($item['picture'], true);
                            foreach ($picture as $val) {
                                \App\Helpers\FileUpload::moveTrash($val);
                            }
                        }
                    }
                }
            }
            return TRUE;
        } catch (Exception $exc) {
            DB::rollBack();
            return FALSE;
        }
    }

    public function setUrlRewrite($params, $category_id = 0, $product_id = 0)
    {
        if ($category_id > 0 && $product_id > 0) {
            $itemCategory = (new CategoryEloquentRepository())->getItem(['id' => $category_id], ['task' => 'get-item-main']);
            $productAlias = ($params['alias'] != "") ? $params['alias'] : \App\Helpers\Filter::setUrlKey($params['name']);
            $aliasPath = $itemCategory['alias'] . "/" . $productAlias . ".html";
            $route = "product/view/" . $product_id;
            $dataUpdate = array(
                'path' => $aliasPath,
                'route' => $route,
                'category_id' => $category_id,
                'product_id' => $product_id,
            );
            (new UrlRewrite())->updateOrCreate(['category_id' => $category_id, 'product_id' => $product_id], $dataUpdate);
        }
    }

    /* public function setUrlRewrite($params, $category_id = 0, $product_id = 0) {
      if ($category_id > 0 && $product_id > 0) {
      $items = (new CategoryEloquentRepository())->listItems(null, ['task' => 'admin-list-items-selector']);
      $productAlias = ($params['alias'] != "") ? $params['alias'] : \App\Helpers\Filter::setUrlKey($params['name']);
      $aliasPath = (new CategoryHelper())->generateAlias($items, $category_id) . "/" . $productAlias;
      $route = "product/view/" . $product_id;
      $data_urlRewrite = array(
      'path' => $aliasPath . ".html",
      'route' => $route,
      'category_id' => $category_id,
      'product_id' => $product_id,
      );
      (new UrlRewrite())->updateOrCreate(['category_id' => $category_id, 'product_id' => $product_id], $data_urlRewrite);
      }
      } */

    public function deleteUrlRewrite($category_id = 0, $product_id = 0)
    {
        if ($category_id > 0 && $product_id > 0) {
            (new UrlRewrite())::withTrashed()->where("category_id", $category_id)->where("product_id", $product_id)->forceDelete();
        }
    }

    public function deleteUrlRewriteOfId($product_id = 0)
    {
        if ($product_id > 0) {
            (new UrlRewrite())::withTrashed()->where("product_id", $product_id)->forceDelete();
        }
    }

    public function saveAttributesSet($params, $product_id)
    {
        //Save Attribute Set
        if (isset($params['attribute_sets']) && count($params['attribute_sets']) > 0) {
            $tbProductAttributeSet = (new ProductAttributeSet());
            $data_attribute_sets_id = [];
            foreach ($params['attribute_sets'] as $alias => $attribute_set) {
                $tbProductAttributeSet->addFillable($alias);
                foreach ($attribute_set as $val) {
                    $data_attribute_sets_id[] =  $val;
                    $data_attribute_sets_update = array(
                        'alias' => $alias,
                        'product_id' => $product_id,
                        'attribute_set_ids' => $val,
                        $alias => $val
                    );
                    $tbProductAttributeSet->updateOrCreate(['product_id' => $product_id, 'alias' => $alias, 'attribute_set_ids' => $val], $data_attribute_sets_update);
                    $tbProductAttributeSet->updateOrCreate(['product_id' => $product_id, 'alias' => $alias, 'attribute_set_ids' => $val], $data_attribute_sets_update);
                }

                //                $data_attribute_sets = array(
                //                    'alias' => $alias,
                //                    'product_id' => $product_id,
                //                    'attribute_set_ids' => \Illuminate\Support\Arr::join($attribute_set, ',')
                //                );
                //                (new ProductAttributeSet())->updateOrCreate(['product_id' => $product_id, 'alias' => $alias], $data_attribute_sets);
            }
            $tbProductAttributeSet->whereNotIn('attribute_set_ids', $data_attribute_sets_id)
                // ->where('alias', $alias)
                ->where('product_id', $product_id)
                ->forceDelete();
        }
    }

    //    public function deletePictureOption($attributes = null) {
    //        if (count($attributes) > 0) {
    //            foreach ($attributes as $item) {
    //                $picture = $item->picture;
    //                if ($picture != "") {
    //                    \App\Helpers\FileUpload::moveTrash($picture);
    //                }
    //            }
    //        }
    //    }
}
