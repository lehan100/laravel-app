<?php

namespace App\Repositories\Inventory;

use Illuminate\Support\Facades\DB;
use App\Repositories\EloquentRepository;
use App\Helpers\Order\Timeline;
use App\Repositories\Product\ProductEloquentRepository;

class InventoryEloquentRepository extends EloquentRepository implements InventoryRepositoryInterface
{
    protected $timeline;
    protected $productModel;
    public function __construct(Timeline $timeline, ProductEloquentRepository $productModel)
    {
        parent::__construct();
        $this->timeline = $timeline;
        $this->productModel = $productModel;
    }
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Inventory::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select()
                ->with("product")
                ->orderBy('product_id', 'desc');
           
            if (isset($params['filter']) && $params['filter'] != null) {
                $filter = $params['filter'];
                $query->whereHas('product', function ($query) use ($filter) {
                    foreach ($filter as $key => $val) {
                        if($key == 'status' || $key =='stock'){
                            $query->where($key,$val);
                        }else{
                            $query->where($key, 'like', "%{$val}%");
                        }
                    }
                });
            }
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select()
                ->with("product")
                ->with("timeline")
                ->where('id', $params['id'])
                ->first();
        }
        if ($options['task'] == 'get-item-by-product-id') {
            $result = $this->_model->select()
                ->with("product")
                ->with("timeline")
                ->where('product_id', $params['product_id'])
                ->first();
        }
        return $result;
    }

    // @Override
    public function saveItem($params = null, $options = null)
    {
        $result = 0;

        if ($options['task'] == 'save-item') {
            $data = array(
                'product_id' => $params['product_id'],
                'available_quantity' => $params['available_quantity'],
                'sold_quantity' => $params['sold_quantity'],
            );
            //print_r($data);die();
            $row = $this->_model->updateOrCreate(['product_id' => $params['product_id']], $data);
            $result = $row->id;
            $this->productModel->saveItem(['id' => $params['product_id'], 'qty' => $params['sold_quantity']], ['task' => 'update-qty']);
        }
        if ($options['task'] == 'edit-item-by-product-id') {
            $data = array(
                'product_id' => $params['product_id'],
                'available_quantity' => $params['available_quantity'],
                'sold_quantity' => $params['sold_quantity'],
            );
            $row = $this->_model->updateOrCreate(['id' => $params['id']], $data);
            $result = $row->id;
            $this->productModel->saveItem(['id' => $params['product_id'], 'qty' => $params['sold_quantity']], ['task' => 'update-qty']);
        }
        if ($options['task'] == 'update-inventory-order') {
            $data = array(
                'sold_quantity' =>  DB::raw('sold_quantity-' . $params['qty']),
                'order_quantity' =>  DB::raw('order_quantity+' . $params['qty']),
            );
            
            $row = $this->_model->updateOrCreate(['product_id' => $params['id']], $data);
            $result = $row->id; 
            $itemInventory = $this->getItem(['id' =>  $result], ['task' => 'get-item']);
            $timeline = $itemInventory->timeline;
            if ($timeline) {
                $dataTimeLine = json_decode($timeline->comments, true);
                $this->timeline->setData($dataTimeLine);
            }
            $this->timeline->createdTimeLine("Khách hàng đã mua " . $params['qty'] . ' sản phẩm , order_id: ' . sprintf('%06d', $params['order_id']) . ". Số lượng bán còn " .  $itemInventory['sold_quantity'] . " sản phẩm ", 'system');
            $dataTimeLine = ['inventory_id' => $result, 'data' => $this->timeline->getData()];
           // print_r($dataTimeLine);
            $this->updateTimeLine($dataTimeLine);
            $this->productModel->saveItem(['id' => $itemInventory['product_id'], 'qty' => $itemInventory['sold_quantity']], ['task' => 'update-qty']);
        }
        if ($options['task'] == 'update-qty-inventory-cancel') {
            $data = array(
                'sold_quantity' =>  DB::raw('sold_quantity+' . $params['qty']),
                'order_quantity' =>  DB::raw('order_quantity-' . $params['qty']),
            );
            try {
                //code...
                $data = array(
                    'sold_quantity' =>  DB::raw('sold_quantity+' . $params['qty']),
                    'order_quantity' =>  DB::raw('order_quantity-' . $params['qty']),
                );
                $row = $this->_model->updateOrCreate(['product_id' => $params['id']], $data);
               
            } catch (\Throwable $th) {
                //throw $th;
                $data = array(
                    'sold_quantity' =>  DB::raw('sold_quantity+' . $params['qty']),
                    'order_quantity' =>  0,
                );
                $row = $this->_model->updateOrCreate(['product_id' => $params['id']], $data);
            }
            $result = $row->id;
            $itemInventory = $this->getItem(['id' =>  $result], ['task' => 'get-item']);
            

            $timeline = $itemInventory->timeline;
            if ($timeline) {
                $dataTimeLine = json_decode($timeline->comments, true);
                $this->timeline->setData($dataTimeLine);
            }
            $this->timeline->createdTimeLine("Hủy đơn hàng " . sprintf('%06d', $params['order_id']) . ". Số lượng bán còn " .  $itemInventory['sold_quantity'] . " sản phẩm ", auth()->user()->username);
            $dataTimeLine = ['inventory_id' => $result, 'data' => $this->timeline->getData()];
            $this->updateTimeLine($dataTimeLine);
            $this->productModel->saveItem(['id' => $itemInventory['product_id'], 'qty' => $itemInventory['sold_quantity']], ['task' => 'update-qty']);
            
        }
        if ($options['task'] == 'update-qty-inventory-success') {
            try {
                $data = array(
                    'order_quantity' =>  DB::raw('order_quantity-' . $params['qty'])
                );
                $row = $this->_model->updateOrCreate(['product_id' => $params['id']], $data);
            } catch (\Throwable $th) {
                $data = array(
                    'order_quantity' =>  0
                );
                $row = $this->_model->updateOrCreate(['product_id' => $params['id']], $data);
            }
            $result = $row->id;
            $itemInventory = $this->getItem(['id' =>  $result], ['task' => 'get-item']);
            $timeline = $itemInventory->timeline;
            if ($timeline) {
                $dataTimeLine = json_decode($timeline->comments, true);
                $this->timeline->setData($dataTimeLine);
            }
            $this->timeline->createdTimeLine("Giao hàng thành công " . sprintf('%06d', $params['order_id']) . ". Số lượng tạm hoãn còn " .  $itemInventory['order_quantity'] . " sản phẩm ", auth()->user()->username);
            $dataTimeLine = ['inventory_id' => $result, 'data' => $this->timeline->getData()];
            $this->updateTimeLine($dataTimeLine);
        }
        return ($result > 0) ? $result : FALSE;
    }
    public function updateTimeLine($params)
    {
        $dataTimeline = ['inventory_id' =>  $params['inventory_id'], 'comments' => json_encode($params['data'])];
       (new \App\Models\InventoryTimelines())->updateOrCreate(['inventory_id' => $params['inventory_id']], $dataTimeline);
    }
    // @Override
    public function deleteItem($params = null, $options = null)
    {
        if ($options['task'] == 'delete-item') {
            $this->_model->where('product_id', $params['product_id'])->delete();
        }
    }
}
