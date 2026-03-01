<?php

namespace App\Repositories\Province;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Repositories\EloquentRepository;

class ProvinceEloquentRepository extends EloquentRepository implements ProvinceRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Province::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select('id', 'name', 'status')
                ->orderBy('id', 'asc');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == "admin-list-items-selector") {
            $result = $this->_model->select('id', 'name')
                ->orderBy('id', 'asc')
                ->get()
                ->toArray();
        }
        if ($options['task'] == "frontend-list-items") {
            $query = $this->_model->select('id', 'name')
                ->orderBy('name', 'asc');
            $result = $query->get();
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select('id', 'name', 'status')->where('id', $params['id'])->first();
        }
        if ($options['task'] == 'frontent-get-item') {
            $dataKeyCache = [
                $options['task'],
                $params['city_id'],
                $params['district_id'],
                $params['ward_id']
            ];
            $keycache = Arr::join($dataKeyCache, "-");
            $result = Cache::get($keycache, function () use ($params) {
                $query = $this->_model->select('id', 'name')
                    ->with(array('districtOne' => function ($query) use ($params) {
                        if (isset($params['ward_id']) && $params['ward_id'] > 0) {
                            $query->with(array("ward" => function ($query) use ($params) {
                                $query->where("id", $params['ward_id']);
                            }));
                        }
                        $query->where("id", $params['district_id']);
                    }))
                    ->where('id', $params['city_id']);
                return $query->first();
            });
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
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->_model->where('id', $params['id'])->update(['status' => $status]);
        }
        if ($options['task'] == 'add-item') {
            $row = $this->_model;
            $row->name = $params['name'];
            $row->status = isset($params['status']) ? 1 : 0;
            //            $row->created = time();
            $row->save();
            $result = $row->id;
            // $result = $this->insert([
            //     'name' => $params['name'],
            //     'status' => isset($params['status']) ? 1 : 0,
            //     'created' => time()
            // ]);
        }

        if ($options['task'] == 'edit-item') {
            $row = $this->_model::where('id', $params['id'])->first();
            $row->name = $params['name'];
            $row->status = isset($params['status']) ? 1 : 0;
            $row->save();
            $result = $row->id;
            // $result = $this->where('id', $params['id'])->update([
            //     'name' => $params['name'],
            //     'status' => isset($params['status']) ? 1 : 0
            // ]);
        }
        return ($result > 0) ? $result : FALSE;
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
        $result = 0;
        DB::beginTransaction();
        try {
            $district = new \App\Models\District();
            if ($options['task'] == 'delete-item-multi') {
                if (isset($params['aid']) && count($params['aid']) > 0) {
                    $this->_model->whereIn('id', $params['aid'])->delete();
                    $district->whereIn('province_id', $params['aid'])->delete();
                    DB::commit();
                    return TRUE;
                }
            }
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $this->_model->where('id', $params['id'])->delete();
                    $district->where('province_id', $params['id'])->delete();

                    DB::commit();
                    return TRUE;
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return FALSE;
        }
    }
}
