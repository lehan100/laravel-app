<?php

namespace App\Repositories\Category;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Repositories\EloquentRepository;
use App\Models\EntypeContent;
use App\Models\UrlRewrite;
use App\Helpers\Category as CategoryHelper;
use Exception;

class CategoryEloquentRepository extends EloquentRepository implements CategoryRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    protected $cachePrefix = 'categories-';

    public function getModel()
    {
        return \App\Models\Category::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select('id', 'name', 'parent_id', 'picture', 'page', 'position_menu', 'position_top', 'position_main', 'position_footer_a', 'position_footer_b', 'sort', 'status', 'created_at')
                //                    ->orderBy('id', 'asc')
                ->orderBy('sort', 'asc');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == "admin-list-items-selector") {
            $query = $this->_model->select('id', 'name', 'alias', 'parent_id', 'status');
            if (isset($params['page'])) {
                $query->whereIn('page', $params['page']);
            }
            $result = $query->orderBy('id', 'asc')->get();
        }
        if ($options['task'] == "admin-list-items-by-ids") {
            $query = $this->_model->select('id', 'name', 'alias', 'parent_id', 'status');
            if (isset($params['ids'])) {
                $query->whereIn('id', $params['ids']);
            }
            $result = $query->orderBy('id', 'asc')->get();
        }
        if ($options['task'] == "admin-list-items-sitemap") {
            $keycache = $this->cachePrefix . $options['task'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select('id', 'name', 'alias', 'parent_id', 'picture')
                    ->with('url')
                    ->where('status',  1)
                    ->orderBy('id', 'asc');
                return $query->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-list-items-breadcrumbs") {
            $keycache = $this->cachePrefix . $options['task'];
            if (isset($params['page'])) {
                $keycache .=  Arr::join($params['page'], '-');
            }
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select('id', 'name', 'alias', 'parent_id')
                    ->with("url");
                if (isset($params['page'])) {
                    $query->whereIn('page', $params['page']);
                }
                return $query->orderBy('sort', 'asc')->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-list-items-filter") {
            $keycache = $this->cachePrefix . $options['task'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select('id', 'name')->withCount('products');
                if (isset($params['id_category'])) {
                    $query->where('parent_id', $params['id_category']);
                }
                return $query->orderBy('id', 'asc')->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                //Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-list-items-by-id") {
            $keycache = $this->cachePrefix . $options['task'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select('id', 'name');
                if (isset($params['id_categories'])) {
                    $query->whereIn('id', $params['id_categories']);
                }
                $query->with('url')
                    ->where('status', "=", 1);
                return $query->orderBy('id', 'asc')->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-list-items-positions") {
            $keycache = $this->cachePrefix . $options['task'] . $params['position'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select('id', 'name', 'parent_id', 'picture')
                    ->with('url')
                    //                                ->withCount('products')
                    ->where('status', "=", 1)
                    ->orderBy('parent_id', 'asc');
                if (isset($params['position'])) {
                    $query->where($params['position'], '=', 1);
                }
                if (isset($params['position']) && $params['position'] == 'position_main') {
                    $query->with(array('category_parents' => function ($query) {
                        $query->select('categories.id', 'categories.name', 'categories.parent_id', 'categories.picture')
                            ->with('url')
                            ->withCount('products')
                            ->where('status', "=", 1);
                    }));
                }
                return $query->orderBy('sort', 'asc')->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select()->where('id', $params['id'])
                ->with('contents')
                ->first();
        }
        if ($options['task'] == 'get-item-main') {
            $result = $this->_model->select()->where('id', $params['id'])
                ->first();
        }
        if ($options['task'] == 'frontend-get-item') {
            $result = $this->_model->select()->with('url')->where('id', $params['id_category'])
                ->first();
        }

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
        if ($options['task'] == 'sort-items') {
            foreach ($params['listorderid'] as $key => $val) {
                $this->_model->where('id', $val)->update(['sort' => $params['listorder'][$key]]);
            }
            return TRUE;
        }
        if ($options['task'] == 'sort-item') {
            $item = $this->getItem($params, ['task' => 'get-item-main']);
            //            print_r($item->toArray());
            if ($item) {
                DB::beginTransaction();
                try {
                    $params['pos'] = ($params['pos'] >= 0) ? $params['pos'] : 0;
                    $dataUpdate = ['sort' => $params['pos'], 'parent_id' => $params['parent_id']];
                    $this->_model->where('id', $params['id'])->update($dataUpdate);
                    $this->setUrlRewrite($item, $params['id']);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return FALSE;
                }
            }
        }

        DB::beginTransaction();
        try {
            $alias = ($params['alias'] != "") ? $params['alias'] : \App\Helpers\Filter::setUrlKey($params['name']);
            if ($options['task'] == 'add-item') {
                $entype_id = Str::random(10);
                $row = $this->_model;
                $row->name = $params['name'];
                $row->entype_id = $entype_id;
                $row->alias = $alias;
                $row->parent_id = $params['parent_id'];
                $row->page = $params['page'];
                $row->picture = @$params['image'];

                //                $row->title = $params['title'];
                //                $row->keyword = $params['keyword'];
                //                $row->description = $params['description'];
                $row->status = isset($params['status']) ? 1 : 0;
                $row->position_menu = isset($params['position_menu']) ? 1 : 0;
                $row->position_top = isset($params['position_top']) ? 1 : 0;
                $row->position_main = isset($params['position_main']) ? 1 : 0;
                $row->position_footer_a = isset($params['position_footer_a']) ? 1 : 0;
                $row->position_footer_b = isset($params['position_footer_b']) ? 1 : 0;
                $row->save();
                $id = $row->id;
                $this->_model->where("id", $result)->update(['sort' => $result]);
                // Entype
                $data_entype = array(
                    'entype_id' => $entype_id,
                    'title' => $params['title'],
                    'keyword' => $params['keyword'],
                    'description' => $params['description']
                );
                (new EntypeContent())->insert($data_entype);
                // Route
                $this->setUrlRewrite($params, $id);
                DB::commit();
            }

            if ($options['task'] == 'edit-item') {
                $entype_id = $params['entype_id'];
                $row = $this->_model::where('id', $params['id'])->first();
                $row->name = $params['name'];
                $row->alias = $alias;
                $row->parent_id = $params['parent_id'];
                $row->page = $params['page'];
                $row->picture = @$params['image'];
                $row->status = isset($params['status']) ? 1 : 0;
                $row->position_menu = isset($params['position_menu']) ? 1 : 0;
                $row->position_top = isset($params['position_top']) ? 1 : 0;
                $row->position_main = isset($params['position_main']) ? 1 : 0;
                $row->position_footer_a = isset($params['position_footer_a']) ? 1 : 0;
                $row->position_footer_b = isset($params['position_footer_b']) ? 1 : 0;
                $row->save();
                $id = $row->id;
                // Entype
                $data_entype = array(
                    'title' => $params['title'],
                    'keyword' => $params['keyword'],
                    'description' => $params['description']
                );

                (new EntypeContent())->updateOrCreate(['entype_id' => $entype_id], $data_entype);
                // Route
                // Route
                $this->setUrlRewrite($params, $id);
                DB::commit();
            }
            event(new \App\Events\CategoryResizeImage($params));
            return $id;
        } catch (\Exception $e) {
            DB::rollBack();
            return FALSE;
        }
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
        DB::beginTransaction();
        try {
            if ($options['task'] == 'delete-item-multi') {
                if (isset($params['aid']) && count($params['aid']) > 0) {
                    $items = $this->_model->select("id", 'entype_id', 'picture')
                        ->whereIn('id', $params['aid'])
                        ->get();
                    if (count($items) > 0) {
                        $this->_model->whereIn('id', $params['aid'])->delete();
                        foreach ($items as $item) {
                            (new EntypeContent())->where("entype_id", $item['entype_id'])->delete();
                            $this->deleteUrlRewrite($item['id']);
                        }
                    }
                    DB::commit();
                }
            }
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $items = $this->listItems(null, ['task' => 'admin-list-items-selector']);
                    $data = (new CategoryHelper())->generateDataId($items, $params['id']);
                    if ($data) {
                        foreach ($data as $id) {
                            $item = $this->_model->select("id", 'entype_id', 'picture')
                                ->where("id", $id)
                                ->first();
                            if ($item) {
                                $this->_model->where('id', $id)->delete();
                                (new EntypeContent())->where("entype_id", $item['entype_id'])->delete();
                            }
                        }
                    }
                    $this->deleteUrlRewrites($data);
                    DB::commit();
                }
            }
            return TRUE;
        } catch (Exception $exc) {
            DB::rollBack();
            return FALSE;
        }
    }

    public function setUrlRewrite($params, $id)
    {
        $items = $this->listItems(null, ['task' => 'admin-list-items-selector']);
        $aliasPath = (new CategoryHelper())->generateAlias($items, $id);
        $route = (new CategoryHelper())::getRoute($params['page'], $id);
        $data_urlRewrite = array(
            'path' => $aliasPath . ".html",
            'route' => $route,
            'category_id' => $id
        );
        (new UrlRewrite())->updateOrCreate(['category_id' => $id], $data_urlRewrite);
    }

    public function deleteUrlRewrite($id)
    {
        $items = $this->listItems(null, ['task' => 'admin-list-items-selector']);
        $data = (new CategoryHelper())->generateDataId($items, $id);
        if ($data) {
            foreach ($data as $id) {
                (new UrlRewrite())::withTrashed()->where("category_id", $id)->forceDelete();
            }
        }
    }

    public function deleteUrlRewrites($data)
    {
        if (count($data) > 0) {
            foreach ($data as $id) {
                (new UrlRewrite())::withTrashed()->where("category_id", $id)->forceDelete();
            }
        }
    }
}
