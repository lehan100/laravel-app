<?php

namespace App\Repositories\Ward;

use App\Repositories\EloquentRepository;

class WardEloquentRepository extends EloquentRepository implements WardRepositoryInterface {

    /**
     * get model
     * @return string
     */
    public function getModel() {
        return \App\Models\Ward::class;
    }

    public function listItems($params = null, $options = null) {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->with(array('district' => function ($query) {
                            $query->with(array('province' => function ($query) {
                                    $query->select('id', 'name');
                                }));
                        }))
                    ->whereHas('district', function ($query) use ($params) {
                        $query->where('status', 1);
                        if (isset($params['filter']['districts.province_id'])) {
                            $query->where("districts.province_id", $params['filter']['districts.province_id']);
                        }
                    })
                    ->orderBy('district_id', 'asc')
            ;
            //$filter = $params['filter'];

            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == "frontend-list-items") {
            $result = $this->_model->select('id', 'name')
                    ->orderBy('name', 'asc')
                    ->where("district_id", $params['id'])
                    ->where("status", 1)
                    ->get();
        }

        return $result;
    }

    public function getItem($params = null, $options = null) {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select('id', 'name', 'district_id', 'status')->where('id', $params['id'])->first();
        }

        return $result;
    }

    // @Override
    public function saveItem($params = null, $options = null) {
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
            $row = new $this->_model;
            $row->name = $params['name'];
            $row->district_id = $params['district_id'];
            $row->status = isset($params['status']) ? 1 : 0;
//            $row->created = time();
            $row->save();
            $result = $row->id;
            // $result = $this->insert([
            //     'name' => $params['name'],
            //     'id_city' => $params['id_city'],
            //     'status' => isset($params['status']) ? 1 : 0,
            //     'created' => time()
            // ]);
        }

        if ($options['task'] == 'edit-item') {
            $row = $this->_model->where('id', $params['id'])->first();
            $row->name = $params['name'];
            $row->district_id = $params['district_id'];
            $row->status = isset($params['status']) ? 1 : 0;
            $row->save();
            $result = $row->id;
            // $result = $this->where('id', $params['id'])->update([
            //     'name' => $params['name'],
            //     'id_city' => $params['id_city'],
            //     'status' => isset($params['status']) ? 1 : 0
            // ]);
        }
        return ($result > 0) ? $result : FALSE;
    }

    // @Override
    public function deleteItem($params = null, $options = null) {
        $result = 0;
        if ($options['task'] == 'delete-item-multi') {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $result = $this->_model->whereIn('id', $params['aid'])->delete();
            }
        }
        if ($options['task'] == 'delete-item') {
            if (isset($params['id'])) {
                $result = $this->_model->where('id', $params['id'])->delete();
            }
        }
        return ($result > 0) ? TRUE : FALSE;
    }
}
