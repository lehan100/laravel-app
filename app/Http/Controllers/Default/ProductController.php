<?php

namespace App\Http\Controllers\Default;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Helpers\Category as CategoryHelper;
use App\Helpers\Content;
use App\Helpers\Product\Search as SearchHelper;
use App\Helpers\Seo as SEO;
use App\Helpers\Product\Attribute;
use App\Services\ProductHit;
use App\Http\Controllers\FrontendController;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Sales\SalesRepositoryInterface;
use App\Repositories\Store\AttributeSetRepositoryInterface;
use App\Repositories\Rating\RatingRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Http\Requests\Default\RatingPostRequest;

class ProductController extends FrontendController
{

    protected $controllerView = 'default.pages.product.';
    protected $controllerViewLayout = 'default.layouts.';
    protected $controllerName = 'product';
    protected $categoryModel;
    protected $productModel;
    protected $salesModel;
    protected $attributeSetModel;
    protected $ratingModel;
    protected $orderModel;
    protected $SEO;
    protected $HIT;

    public function __construct(
        CategoryRepositoryInterface $categoryModel,
        ProductRepositoryInterface $productModel,
        SalesRepositoryInterface $salesModel,
        AttributeSetRepositoryInterface $attributeSetModel,
        RatingRepositoryInterface $ratingModel,
        OrderRepositoryInterface $orderModel,
        ProductHit $HIT,
    ) {
        parent::__construct();
        $this->categoryModel = $categoryModel;
        $this->productModel = $productModel;
        $this->salesModel = $salesModel;
        $this->attributeSetModel = $attributeSetModel;
        $this->ratingModel = $ratingModel;
        $this->orderModel = $orderModel;
        $this->HIT = $HIT;
        $this->SEO = new SEO($this->head);
        session(['sort' => 'position']);
        view()->share(['params' => $this->params]);
    }

    public function list(Request $request)
    {
        if ($request->id !== null) {
            $id_category = $request->id;
            $this->params['id_category'] = $id_category;
            $itemsCategory = $this->categoryModel->listItems(null, ['task' => 'frontend-list-items-breadcrumbs']);
            $itemsBreadcrumbs = (new CategoryHelper())->generateDataBreadcrumb($itemsCategory, $id_category);
            if (!$itemsBreadcrumbs) {
                abort(404);
            }
            // META

            $categoryDetail = $this->categoryModel->getItem($this->params, ['task' => 'frontend-get-item']);
            $picture = $categoryDetail->picture;
            $image_src = "";
            if ($picture != "") {
                $image_src = asset($this->configPath['category']['path'] . "/" . $picture);
                $this->SEO->setMetaProperty('og:image', $image_src);
            }
            if ($categoryDetail->contents->title == "") {
                // foreach ($itemsBreadcrumbs as $val) {
                //     $title[] = $val->name;
                // }
                $array  = Arr::map($itemsBreadcrumbs, function ($item) {
                    return $item->name;
                });
                $title = Arr::join(array_reverse($array), ' - ');
                $categoryDetail->contents->title = $title;
            }
            $this->SEO->metaTags($categoryDetail);
            // end META
            //Breadcrumbs
            $breadcrumbs = view($this->controllerViewLayout . 'elements/breadcrumb_home', [
                'itemsBreadcrumbs' => $itemsBreadcrumbs,
            ])->render();
            //end Breadcrumbs
            $category_ids = (new CategoryHelper)->generateDataId($itemsCategory, $id_category);
            $this->params['id_categories'] = $category_ids;
            //Content
            $this->params['sort'] = isset($request->mode) ? $request->mode : session('sort');
            // Filter
            $listAttribute = $this->attributeSetModel->listItems($this->params, ['task' => 'frontend-list-filters']);
            $arrParamFilters = (new Attribute($listAttribute))->getParams($request->all());
            $listCategoryFilter = $this->categoryModel->listItems($this->params, ['task' => 'frontend-list-items-filter']);
            // Data Filter
            $this->params['filter_attribute'] = $arrParamFilters;
            $this->params['filter_price'] = ($request->price) ? $request->price : null;
            $this->params['filter_category'] = ($request->category) ? $request->category : null;
            $this->params['id_products'] = $this->productModel->listItems($this->params, ['task' => 'frontend-list-items-id']);

            $listFilters = $this->attributeSetModel->listItems($this->params, ['task' => 'frontend-list-filters']);

            //Load Auto Page
            $autopage = isset($request->autopage) ? true : false;
            $page = isset($request->page) ? $request->page : 1;
            $this->params['page'] = $page;
            $this->params['autopage'] = $autopage;
            // if (!$autopage) {
            //     $currentPage = 1;
            //     $this->params['pagination']['totalItemsPerPage'] = $this->params['pagination']['totalItemsPerPage'] * $page;
            //     \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            //         return $currentPage;
            //     });
            // }
            $listItems = $this->productModel->listItems($this->params, ['task' => 'frontend-list-items']);
            // echo "<pre>";
            // print_r($listItems->toArray());die();
            if (isset($request->lazyload) && $request->lazyload == true) {
                // $htmlPagination = view($this->controllerView . 'ajax/pagination', [
                //     'listItems' => $listItems
                // ])->render();
                $dataLoading = view($this->controllerView . 'ajax/list', [
                    'listItems' => $listItems,
                    'autopage' => $autopage,

                ])->render();
                $sitebar = view($this->controllerView . 'blocks/sitebar', [
                    'listFilters' => $listFilters,
                    'listCategoryFilter' => $listCategoryFilter
                ])->render();
                return response()->json([
                    'status' => true,
                    'html' => \App\Helpers\Content::minifyContent($dataLoading),
                    'sitebar' => \App\Helpers\Content::minifyContent($sitebar),
                    // 'pagination' => \App\Helpers\Content::minifyContent($htmlPagination)
                ]);
            }
            return view($this->controllerView . 'list', [
                'breadcrumbs' => $breadcrumbs,
                'listFilters' => $listFilters,
                'listCategoryFilter' => $listCategoryFilter,
                'listItems' => $listItems,
                'sort' => $this->params['sort']
            ]);
        }
        abort(404);
    }
    public function search(Request $request)
    {
        $this->SEO->setMeta(name: "title", content: "Tìm kiếm sản phẩm")
            ->setMeta(name: "keywords", content: "Tìm kiếm sản phẩm")
            ->setMeta(name: "description", content: "Tìm kiếm sản phẩm")
            ->setMetaProperty(property: "og:title", content: "Tìm kiếm sản phẩm")
            ->setMetaProperty(property: "og:keywords", content: "Tìm kiếm sản phẩm")
            ->setMetaProperty(property: "og:description", content: "Tìm kiếm sản phẩm");
        if ($request->keyword != "") {
            $this->params['sort'] = isset($request->mode) ? $request->mode : session('sort');
            $this->params['keyword'] = $request->keyword;

            $listAttribute = $this->attributeSetModel->listItems($this->params, ['task' => 'frontend-list-filters']);
            $arrParamFilters = (new Attribute($listAttribute))->getParams($request->all());
            $this->params['filter_attribute'] = $arrParamFilters;
            $this->params['filter_price'] = ($request->price) ? $request->price : null;
            $this->params['filter_category'] = ($request->category) ? $request->category : null;
            $this->params['id_products'] = $this->productModel->listItems($this->params, ['task' => 'frontend-list-items-id']);
            $listFilters = $this->attributeSetModel->listItems($this->params, ['task' => 'frontend-list-filters']);

            $breadcrumbs = view($this->controllerView . 'blocks/breadcrumb_search', [
                'keyword' => $request->keyword,
            ])->render();

            $autopage = isset($request->autopage) ? true : false;
            $this->params['autopage'] = $autopage;
            $page = isset($request->page) ? $request->page : 1;
            $this->params['page'] = $page;
            // if (!$autopage) {
            //     $currentPage = 1;
            //     $page = isset($request->page) ? $request->page : 1;
            //     $this->params['pagination']['totalItemsPerPage'] = $this->params['pagination']['totalItemsPerPage'] * $page;
            //     \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            //         return $currentPage;
            //     });
            // }
            $listItems = $this->productModel->listItems($this->params, ['task' => 'frontend-list-items-search']);
            if (isset($request->lazyload) && $request->lazyload == true) {
                $dataLoading = view($this->controllerView . 'ajax/list', [
                    'listItems' => $listItems,
                    'autopage' => $autopage
                ])->render();
                $sitebar = view($this->controllerView . 'blocks/sitebar', [
                    'listFilters' => $listFilters
                ])->render();
                return response()->json([
                    'status' => true,
                    'html' => \App\Helpers\Content::minifyContent($dataLoading),
                    'sitebar' => \App\Helpers\Content::minifyContent($sitebar)
                ]);
            }
            // echo "<pre>";
            // echo $listItems->total();
            // print_r($listItems->toArray());
            // die();
            return view($this->controllerView . 'search', [
                'breadcrumbs' => $breadcrumbs,
                'listFilters' => $listFilters,
                'sort' => $this->params['sort'],
                'keyword' => $request->keyword,
                'listItems' => $listItems
            ]);
        } else {
            abort(404);
        }
    }

    public function sale(Request $request)
    {
        if ($request->id !== null) {
            $sale_id = $request->id;
            $this->params['id'] = $sale_id;
            $this->params['sale_id'] = $sale_id;
            $sale_info = $this->salesModel->getItem($this->params, ['task' => 'get-item']);
            if ($sale_info) {
                $title = $sale_info->name;
                $description = $sale_info->description;
                if ($description == '') {
                    $description = $title;
                }
                $this->SEO->setMeta(name: "title", content: $title)
                    ->setMeta(name: "keywords", content: $title)
                    ->setMeta(name: "description", content: $description)
                    ->setMetaProperty(property: "og:title", content: $title)
                    ->setMetaProperty(property: "og:keywords", content: $title)
                    ->setMetaProperty(property: "og:description", content: $description);
                $this->params['sort'] = isset($request->mode) ? $request->mode : session('sort');

                $listAttribute = $this->attributeSetModel->listItems($this->params, ['task' => 'frontend-list-filters']);
                $arrParamFilters = (new Attribute($listAttribute))->getParams($request->all());
                $this->params['filter_attribute'] = $arrParamFilters;
                $this->params['filter_price'] = ($request->price) ? $request->price : null;
                $this->params['filter_category'] = ($request->category) ? $request->category : null;
                $this->params['id_products'] = $this->productModel->listItems($this->params, ['task' => 'frontend-list-items-id']);
                $listFilters = $this->attributeSetModel->listItems($this->params, ['task' => 'frontend-list-filters']);

                $breadcrumbs = view($this->controllerView . 'blocks/breadcrumb_sale', [
                    'sale_info' => $sale_info
                ])->render();

                $autopage = isset($request->autopage) ? true : false;
                $this->params['autopage'] = $autopage;
                $page = isset($request->page) ? $request->page : 1;
                $this->params['page'] = $page;
                // if (!$autopage) {
                //     $currentPage = 1;
                //     $page = isset($request->page) ? $request->page : 1;
                //     $this->params['pagination']['totalItemsPerPage'] = $this->params['pagination']['totalItemsPerPage'] * $page;
                //     \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
                //         return $currentPage;
                //     });
                // }
                $listItems = $this->productModel->listItems($this->params, ['task' => 'frontend-list-items-sale']);
				//echo "<pre>";print_r($listItems->toArray());die();
                if (isset($request->lazyload) && $request->lazyload == true) {
                    $dataLoading = view($this->controllerView . 'ajax/list', [
                        'listItems' => $listItems,
                        'autopage' => $autopage
                    ])->render();
                    $sitebar = view($this->controllerView . 'blocks/sitebar', [
                        'listFilters' => $listFilters
                    ])->render();
                    return response()->json([
                        'status' => true,
                        'html' => \App\Helpers\Content::minifyContent($dataLoading),
                        'sitebar' => \App\Helpers\Content::minifyContent($sitebar)
                    ]);
                }
                return view($this->controllerView . 'sale', [
                    'breadcrumbs' => $breadcrumbs,
                    'listFilters' => $listFilters,
                    'sort' => $this->params['sort'],
                    'keyword' => $request->keyword,
                    'listItems' => $listItems
                ]);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function view(Request $request)
    {
        if ($request->id !== null) {
            $params["id"] = $request->id;
            $item = $this->productModel->getItem($params, ['task' => 'frontend-get-item']);
           //echo "<pre>"; print_r($item->toArray());die();
            if (!$item) {
                abort(404);
            }
            $this->params['id'] = $params['id'];
            $itemsCategory = $this->categoryModel->listItems(null, ['task' => 'frontend-list-items-breadcrumbs']);
            $itemsBreadcrumbs = (new CategoryHelper())->generateDataBreadcrumb($itemsCategory, $item->category[0]->id);
            $breadcrumbs = view($this->controllerViewLayout . 'elements/breadcrumb_home', [
                'itemsBreadcrumbs' => $itemsBreadcrumbs,
            ])->render();
            $picture = $item->picture;
            $image_src = "";
            if ($picture != "") {
                $picture = json_decode($picture);
                $image_src = asset($this->configPath['product']['path'] . "/large/" . $picture[0]);
                $this->SEO->setMetaProperty('og:image', $image_src);
            }

            $this->SEO->metaTags($item);
            $description = $this->SEO->getDescription($item);
            // Other Products
            $otherProducts = $this->productModel->listItems(['category_id' => [$item->category[0]->id]], ['task' => "frontend-main-list-items-limit"]);
            // Rating
            $this->params['pagination']['totalItemsPerPage'] = 10;
            $listRatings = $this->ratingModel->listItems($this->params, ['task' => 'frontend-list-item']);
            $ratingCalucator = $this->ratingModel->listItems($this->params, ['task' => 'frontend-list-item-caculator']);
            $starWithRating = $this->ratingModel->listItems($this->params, ['task' => 'frontend-list-item-with-rating']);
            $ratingObject = (new \App\Helpers\Product\Rating());
            if (count($ratingCalucator) > 0) {
                $ratingObject->setTotalStar($ratingCalucator[0]->sum_star);
                $ratingObject->setTotalRating($ratingCalucator[0]->total_rating);
            } else {
                $ratingObject->setTotalStar(0);
                $ratingObject->setTotalRating(0);
            }

            if (count($starWithRating) > 0) {
                foreach ($starWithRating as $rating) {
                    $ratingObject->setDataRatingToStar($rating->rating, $rating->total_rating);
                }
            }
           
            return view($this->controllerView . 'view', [
                'breadcrumbs' => $breadcrumbs,
                'item' => $item,
                'otherProducts' => $otherProducts,
                'listRatings' => $listRatings,
                'ratingObject' => $ratingObject,
				'description'=> $description
            ]);
        } else {
            abort(404);
        }
    }

    public function Rating(Request $request)
    {
        try {
            $this->params['pagination']['totalItemsPerPage'] = 10;
            $this->params['id'] = $request->product_id;
            $this->params['page'] = $request->page;
            $this->params['star'] = $request->filter;
            $listRatings = $this->ratingModel->listItems($this->params, ['task' => 'frontend-list-item']);
            $htmlListRatings = view($this->controllerView . 'ajax/rating', [
                'listRatings' => $listRatings
            ])->render();
            $htmlListRatingPagination = view($this->controllerView . 'ajax/rating_pagination', [
                'listRatings' => $listRatings
            ])->render();
            return response()->json(['status' => true, 'html' => Content::minifyContent($htmlListRatings), 'pagination' => Content::minifyContent($htmlListRatingPagination)]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false]);
        }
    }
    public function ratingImage(Request $request)
    {
        try {
            if ($request->product_id !== null) {
                $this->params['id'] = $request->product_id;
                $listRatings = $this->ratingModel->listItems($this->params, ['task' => 'frontend-list-item-images']);
                $configPathRating = config('image.path.rating');
                foreach ($listRatings as $rating) {
                    $ratingImages = $rating->images;
                    $ratingName = $rating->name;
                    $ratingContent = $rating->content;
                    $rating_star = \App\Helpers\Product\Rating::ratingCustomerReview($rating->rating);
                    $caption = sprintf('<div class="row justify-content-center text-center py-2"><div class="col-12 col-md-6"><p class="mb-1"><b>%s</b></p><p class="mb-1 text-warning">%s</p><p class="mb-0">%s</p></div></div>', $ratingName, $rating_star, $ratingContent);
                    if ($ratingImages != "") {
                        $ratingImages = explode(",", $ratingImages);

                        foreach ($ratingImages as $image) {
                            $image_src_zoom = asset($configPathRating['path'] . "/" . $image);
                            $image_src = asset($configPathRating['thumb'] . "/" . $image);
                            $data[] = [
                                'src' => $image_src_zoom,
                                'thumb' => $image_src,
                                'caption' => $caption,
                                'name' => $ratingName,
                                'image' => $image
                            ];
                        }
                    }
                }
                return response()->json(['status' => true, 'data' => $data]);
            }
            return response()->json(['status' => false]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false]);
        }
    }
    public function viewer(Request $request)
    {
        if ($request->id !== null) {
            if ($this->HIT->setViewer($request->id)) {
                $this->productModel->saveItem(['id' => $request->id], ['task' => 'update-hit']);
                return response()->json(['status' => true]);
            }
        }
        return response()->json(['status' => false]);
    }

    public function searchTerm(Request $request)
    {
        if ($request->query_text !== "") {
            if ($this->HIT->setViewer($request->query_text)) {
                $query_text = \Illuminate\Support\Str::lower($request->query_text);
                (new \App\Models\SearchTerms())->updateOrCreate(
                    ['query_text' => $query_text],
                    [
                        'query_text' => $query_text,
                        'num_results' => $request->num_results,
                        'popularity' => \Illuminate\Support\Facades\DB::raw('popularity+1')
                    ]
                );
                return response()->json(['status' => true]);
            }
        }
        return response()->json(['status' => false]);
    }



    public function ajaxSearch(Request $request)
    {
        $dataSearch = [];
        if ($request->keyword != "") {
            $params['keyword'] = $request->keyword;
            $listAllProducts = $this->productModel->listItems($params, ['task' => 'fronend-get-all']);
            $dataSearch = (new SearchHelper($listAllProducts))->search($request->keyword);
        }
        $searchResult = view($this->controllerView . 'ajax/search', [
            'dataSearch' => $dataSearch,
            'keyword' => $request->keyword
        ])->render();
        return response()->json(['status' => true, 'html' => \App\Helpers\Content::minifyContent($searchResult), 'link' => route('product/search', ['keyword' => $request->keyword])]);
    }

    public function postRanting(RatingPostRequest $request)
    {
        if ($request->product_id > 0) {
            $params = $request->all();
            if ($this->ratingModel->getItem($params, ['task' => 'get-item'])) {
                return response()->json(['status' => false, 'code' => 400]);
            }
            $params['is_purchase'] = $this->orderModel->getItem($params, ['task' => 'is_purchase']);
            if ($this->ratingModel->saveItem($params, ['task' => 'add-item'])) {
                return response()->json(['status' => true]);
            } else {
                return response()->json(['status' => false, 'code' => 403]);
            }
        }
        return response()->json(['status' => false, 'code' => 403]);
    }
}
