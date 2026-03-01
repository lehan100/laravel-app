<?php

namespace App\Repositories\Media;

use Illuminate\Support\Facades\DB;
use App\Repositories\EloquentRepository;

class PositionEloquentRepository extends EloquentRepository implements PositionRepositoryInterface {

    /**
     * get model
     * @return string
     */
    public function getModel() {
        return \App\Models\MediaPosition::class;
    }

    // @Override
    public function listItems($params = null, $options = null) {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select('id', 'name','mode', 'status')
                    ->orderBy('id', 'asc');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == "admin-list-items-selector") {
            $result = $this->_model->select('id', 'name','mode','code')
                    ->orderBy('id', 'asc')
                    ->get()
                    ->toArray();
        }

        return $result;
    }

    public function getItem($params = null, $options = null) {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select('id', 'name','code',"mode", 'status')->where('id', $params['id'])->first();
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
            $row = $this->_model;
            $row->name = $params['name'];
            $row->code = $params['code'];
            $row->mode = $params['mode'];
            $row->status = isset($params['status']) ? 1 : 0;
            $row->save();
            $result = $row->id;
        }

        if ($options['task'] == 'edit-item') {
            $row = $this->_model::where('id', $params['id'])->first();
            $row->name = $params['name'];
            $row->code = $params['code'];
            $row->mode = $params['mode'];
            $row->status = isset($params['status']) ? 1 : 0;
            $row->save();
            $result = $row->id;
        }
        return ($result > 0) ? $result : FALSE;
    }

    // @Override
    public function deleteItem($params = null, $options = null) {
        $result = 0;
        DB::beginTransaction();
        try {
            $banner = new \App\Models\MediaBanners();
            if ($options['task'] == 'delete-item-multi') {
                if (isset($params['aid']) && count($params['aid']) > 0) {
                    $this->_model->whereIn('id', $params['aid'])->delete();
                    $banner->whereIn('position_id', $params['aid'])->delete();
                    DB::commit();
                    return TRUE;
                }
            }
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $this->_model->where('id', $params['id'])->delete();
                    $banner->where('position_id', $params['id'])->delete();

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
