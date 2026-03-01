<?php

namespace App\Repositories\Post;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\EntypeContent;
use App\Repositories\EloquentRepository;
use App\Models\UrlRewrite;
use App\Helpers\Category as CategoryHelper;
use App\Repositories\Category\CategoryEloquentRepository;
use Exception;

class PostEloquentRepository extends EloquentRepository implements PostRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    protected $cachePrefix = 'posts-';
    public function getModel()
    {
        return \App\Models\Post::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select('id', 'name', 'category_id', 'picture', 'sort', 'status', 'created_at')
                ->with("category")
                ->whereHas('category', function ($query) {
                    $query->where('status', 1);
                })
                ->orderBy('id', 'asc');
            if (isset($params['filter']) && count($params['filter']) > 0) {
                foreach ($params['filter'] as $key => $filter) {
                    if ($key == 'category_id') {
                        $query->whereHas('category', function ($query) use ($filter) {
                            $query->whereIn('categories.id', $filter);
                        });
                    } else {
                        $query->where($key, $filter);
                    }
                }
            }
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == "admin-list-items-sitemap") {
            $keycache = $this->cachePrefix . $options['task'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select('id', 'name', 'category_id', 'picture')
                    ->whereHas('category', function ($query) {
                        $query->whereNotIn('page', [1, 4]);
                    })
                    ->orderBy('id', 'asc');
                return $query->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-list-items") {
            $id_categories = $params['id_categories'];
            $params['page'] = isset($params['page']) ? $params['page'] : 1;
            $keycache = $this->cachePrefix . $options['task'] . Arr::join($id_categories, '-') . Arr::join($params['pagination'], '-') . $params['page'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select()
                    ->with("url", 'contents');

                if (isset($params['id_categories'])) {
                    $query->whereIn('category_id', $params['id_categories']);
                }
                $query->where('status', "=", 1)
                    ->orderBy('id', 'asc');
                return $query->paginate($params['pagination']['totalItemsPerPage']);
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
        }
        if ($options['task'] == "frontend-list-items-top-view") {
            $id_categories = $params['id_categories'];
            $keycache = $this->cachePrefix . $options['task'] . Arr::join($id_categories, '-');
            $result = Cache::store('custom')->get($keycache, function ()  use ($params) {
                $query = $this->_model->select()
                    ->with("url", 'contents');
                if (isset($params['id_categories'])) {
                    $query->whereIn('category_id', $params['id_categories']);
                }
                $query->where('status', 1)
                    ->orderBy('hit_viewer', 'DESC');
                return $query->limit(10)->get();
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
                ->with("category")
                ->with('contents')
                ->first();
        }
        if ($options['task'] == 'frontend-get-item') {
            $result = $this->_model->select()
                ->with('contents')
                ->where('id', $params['id'])
                ->first();
        }
        if ($options['task'] == 'frontend-get-item-w-category') {
            $result = $this->_model->select()
                ->with('contents')
                ->where('category_id', $params['id_category'])
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
                return ($result > 0) ? $result : FALSE;
            }
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->_model->where('id', $params['id'])->update(['status' => $status]);
            return ($result > 0) ? $result : FALSE;
        }
        if ($options['task'] == 'update-hit') {
            $this->_model->where('id', $params['id'])->update(['hit_viewer' => DB::raw('hit_viewer+1')]);
        }
        DB::beginTransaction();
        try {
            $alias = ($params['alias'] != "") ? $params['alias'] : \App\Helpers\Filter::setUrlKey($params['name']);
            if ($options['task'] == 'add-item') {
                $entype_id = Str::random(10);
                $row = $this->_model;
                $row->name = $params['name'];
                $row->alias = $alias;
                $row->category_id = $params['category_id'];
                $row->picture = @$params['image'];
                $row->status = isset($params['status']) ? 1 : 0;
                $row->entype_id = $entype_id;
                $row->save();
                $id = $row->id;
                $data_entype = array(
                    'entype_id' => $entype_id,
                    'sort_content' => $params['sort_content'],
                    'content' => $params['content'],
                    'title' => $params['title'],
                    'keyword' => $params['keyword'],
                    'description' => $params['description']
                );
                (new EntypeContent())->insert($data_entype);
                $this->setUrlRewrite($params, $params['category_id'], $id);
                DB::commit();
            }

            if ($options['task'] == 'edit-item') {
                $entype_id = $params['entype_id'];
                $row = $this->_model::where('id', $params['id'])->first();
                $row->name = $params['name'];
                $row->alias = $alias;
                $row->category_id = $params['category_id'];
                $row->picture = @$params['image'];
                $row->status = isset($params['status']) ? 1 : 0;
                $row->save();
                $id = $row->id;
                $data_entype = array(
                    'sort_content' => $params['sort_content'],
                    'content' => $params['content'],
                    'title' => $params['title'],
                    'keyword' => $params['keyword'],
                    'description' => $params['description']
                );

                (new EntypeContent())->updateOrCreate(['entype_id' => $entype_id], $data_entype);
                $this->setUrlRewrite($params, $params['category_id'], $id);
                DB::commit();
            }
            event(new \App\Events\PostResizeImage($params));
            return $id;
        } catch (\Exception $e) {
            DB::rollBack();
            return FALSE;
        }
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
        $pathConfig = config('image.path.post');
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
                            if ($item['picture'] != "") {
                                \App\Helpers\FileUpload::moveTrashImageProccess($item['picture'], $pathConfig['path'], $pathConfig['trash']);
                            }
                            $this->deleteUrlRewriteOfId($item['id']);
                        }
                    }
                    DB::commit();
                }
            }
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $item = $this->_model->select("id", 'entype_id', 'picture')
                        ->where("id", $params['id'])
                        ->first();
                    if ($item) {
                        $this->_model->where('id', $params['id'])->delete();
                        (new EntypeContent())->where("entype_id", $item['entype_id'])->delete();
                        if ($item['picture'] != "") {
                            \App\Helpers\FileUpload::moveTrashImageProccess($item['picture'], $pathConfig['path'], $pathConfig['trash']);
                        }
                        $this->deleteUrlRewriteOfId($params['id']);
                    }
                    DB::commit();
                }
            }
            return TRUE;
        } catch (Exception $exc) {
            DB::rollBack();
            return FALSE;
        }
    }

    public function setUrlRewrite($params, $category_id = 0, $post_id = 0)
    {
        if ($category_id > 0 && $post_id > 0) {
            $itemCategory = (new CategoryEloquentRepository())->getItem(['id' => $category_id], ['task' => 'get-item-main']);
            if ($itemCategory && $itemCategory['page'] == 3) {
                //$items = (new CategoryEloquentRepository())->listItems(null, ['task' => 'admin-list-items-selector']);
                $postAlias = ($params['alias'] != "") ? $params['alias'] : \App\Helpers\Filter::setUrlKey($params['name']);
                $aliasPath = $itemCategory['alias'] . "/" . $postAlias;
                //$aliasPath = (new CategoryHelper())->generateAlias($items, $category_id) . "/" . $postAlias;
                $route = "news/view/" . $post_id;
                $data_urlRewrite = array(
                    'path' => $aliasPath . ".html",
                    'route' => $route,
                    'category_id' => $category_id,
                    'post_id' => $post_id
                );
                (new UrlRewrite())->updateOrCreate(['category_id' => $category_id, 'post_id' => $post_id], $data_urlRewrite);
            } elseif ($itemCategory && $itemCategory['page'] != 3) {
                $this->deleteUrlRewriteOfId($post_id);
            }
        }
    }

    public function deleteUrlRewrite($category_id = 0, $post_id = 0)
    {
        if ($category_id > 0 && $post_id > 0) {
            (new UrlRewrite())::withTrashed()->where("category_id", $category_id)->where("post_id", $post_id)->forceDelete();
        }
    }

    public function deleteUrlRewriteOfId($post_id = 0)
    {
        if ($post_id > 0) {
            (new UrlRewrite())::withTrashed()->where("post_id", $post_id)->forceDelete();
        }
    }
}
