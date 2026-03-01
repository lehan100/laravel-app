<?php

namespace App\Repositories\Order;

use Illuminate\Support\Facades\DB;
use App\Repositories\EloquentRepository;
class OrderTimelinesEloquentRepository extends EloquentRepository implements OrderTimelinesRepositoryInterface
{
   
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\OrderTimelines::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        
    }

    public function getItem($params = null, $options = null)
    {
        
    }

    // @Override
    public function saveItem($params = null, $options = null)
    {
        $result = 0;
        if ($options['task'] == 'add-item') {
            $row = $this->_model;
            $row->order_id = $params['order_id'];
            $row->comments = $params['comments'];
            $row->save();
            $result = $row->id;
        }

        if ($options['task'] == 'edit-item') {
            $row = $this->_model::where('id', $params['id'])->first();
            $row->comments = $params['comments'];
            $row->save();
            $result = $row->id;
        }
        return ($result > 0) ? $result : FALSE;
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
        if ($options['task'] == 'delete-item') {
            $this->_model->where('order_id', $params['id'])->delete();
        }
    }
}
