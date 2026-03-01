<?php

namespace App\Repositories\Order;

use Illuminate\Support\Facades\DB;
use App\Repositories\EloquentRepository;
use Exception;

class OrderItemsEloquentRepository extends EloquentRepository implements OrderItemsRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\OrderItems::class;
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
        //DB::beginTransaction();
       // try {
            if ($options['task'] == 'add-item') {
                $row = $this->_model;
                $row->order_id = $params['order_id'];
                $row->name = $params['name'];
                $row->sku = $params['sku'];
                $row->qty = $params['qty'];
                $row->price = $params['price'];
                $row->special_price = $params['special_price'];
                $row->special_price_from = $params['special_price_from'];
                $row->special_price_to = $params['special_price_to'];
                $row->options = $params['options'];
                $row->path = $params['path'];
                $row->picture = $params['picture'];
                $row->save();
               // DB::commit();
                return true;
            }

            if ($options['task'] == 'edit-item') {
                $row = $this->_model::where('id', $params['id'])->first();
                $row->name = $params['name'];
                $row->sku = $params['sku'];
                $row->qty = $params['qty'];
                $row->price = $params['price'];
                $row->save();
               // DB::commit();
                return true;
            }
        // } catch (Exception $th) {
        //     DB::rollBack();
        //     return false;
        // }
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
        if ($options['task'] == 'delete-item') {
            $this->_model->where('order_id', $params['id'])->delete();
        }
    }
}
