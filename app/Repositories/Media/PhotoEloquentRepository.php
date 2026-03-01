<?php

namespace App\Repositories\Media;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Repositories\EloquentRepository;
use Exception;
class PhotoEloquentRepository extends EloquentRepository implements PhotoRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    protected $cachePrefix = 'photo-';

    public function getModel()
    {
        return \App\Models\MediaBanner::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select('id', 'name', 'position_id', 'alias_link', 'picture', 'sort', 'status', 'created_at')
                ->with('position')
                ->whereHas('position', function ($query) {
                    $query->where('status', 1);
                })
                ->orderBy('id', 'asc');
            if (isset($params['filter']) && count($params['filter']) > 0) {
                foreach ($params['filter'] as $key => $filter) {
                    $query->where($key, $filter);
                }
            }
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == 'frontend-list-items') {
            $keycache = $this->cachePrefix . $options['task'] . $params['code'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select()
                    ->whereHas('position', function ($query) use ($params) {
                        $query->where('code', $params['code'])->where("status", 1);
                    })
                    ->where("status", 1)
                    ->orderBy('id', 'asc');
                return $query->get();
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
                ->with("position")
                ->first();
        }
        if ($options['task'] == 'frontend-get-item') {
            $keycache = $this->cachePrefix . $options['task'] . $params['code'];
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select()
                    ->whereHas('position', function ($query) use ($params) {
                        $query->where('code', $params['code'])->where("status", 1);
                    })
                    ->where("status", 1);
                return $query->first();
            });
            if (Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result);
            }
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
        DB::beginTransaction();
        try {
            if ($options['task'] == 'add-item') {
                $entype_id = Str::random(10);
                $row = $this->_model;
                $row->name = $params['name'];
                $row->alias_link = $params['alias_link'];
                $row->position_id = $params['position_id'];
                $row->category_id = $params['category_id'];
                $row->picture = @$params['image'];
                $row->status = isset($params['status']) ? 1 : 0;
                $row->save();
                $id = $row->id;
                DB::commit();
            }

            if ($options['task'] == 'edit-item') {
                $row = $this->_model::where('id', $params['id'])->first();
                $row->name = $params['name'];
                $row->alias_link = $params['alias_link'];
                $row->position_id = $params['position_id'];
                $row->category_id = $params['category_id'];
                $row->picture = @$params['image'];
                $row->status = isset($params['status']) ? 1 : 0;
                $row->save();
                $id = $row->id;
                //                echo $entype_id;
                //                 print_r($data_entype);die();
                DB::commit();
            }
            event(new \App\Events\MediaResizeImage($params));
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
                    $items = $this->_model->select("id", 'picture')
                        ->whereIn('id', $params['aid'])
                        ->get();
                    if (count($items) > 0) {
                        $this->_model->whereIn('id', $params['aid'])->delete();
                        foreach ($items as $item) {
                            if ($item['picture'] != "") {
                                \App\Helpers\FileUpload::moveTrashImageProccess($item['picture'], $pathConfig['path'], $pathConfig['trash']);
                            }
                        }
                    }
                    DB::commit();
                }
            }
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $item = $this->_model->select("id", 'picture')
                        ->where("id", $params['id'])
                        ->first();
                    if ($item) {
                        $this->_model->where('id', $params['id'])->delete();
                        if ($item['picture'] != "") {
                            \App\Helpers\FileUpload::moveTrashImageProccess($item['picture'], $pathConfig['path'], $pathConfig['trash']);
                        }
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
}
