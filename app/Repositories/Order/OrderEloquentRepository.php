<?php

namespace App\Repositories\Order;

use Illuminate\Support\Facades\DB;
use App\Repositories\EloquentRepository;
use App\Models\OrderItems;
use App\Models\OrderTimelines;
use Exception;
use App\Helpers\Product\Price;
use Illuminate\Support\Carbon;
use App\Helpers\Order\Timeline;
use App\Repositories\Inventory\InventoryRepositoryInterface;
use App\Repositories\Sales\SalesRepositoryInterface;

class OrderEloquentRepository extends EloquentRepository implements OrderRepositoryInterface
{
    protected $timeline;
    protected $inventory;
    protected $sales;
    public function __construct(Timeline $timeline, InventoryRepositoryInterface $inventory, SalesRepositoryInterface $sales)
    {
        parent::__construct();
        $this->timeline = $timeline;
        $this->inventory = $inventory;
        $this->sales = $sales;
    }
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Orders::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select();
            if (isset($params['filter']) && $params['filter'] != null) {
                $filter = $params['filter'];
                if (isset($filter['from'])) {
                    $dateFrom = new Carbon($filter['from']);
                    unset($filter['from']);
                    if (isset($filter['to'])) {
                        $dateTo = new Carbon($filter['to']);
                        unset($filter['to']);
                    } else {
                        $dateTo = Carbon::today();
                    }
                    $query->whereBetween('created_at', [$dateFrom->format('Y-m-d') . " 00:00:00", $dateTo->format('Y-m-d') . " 23:59:59"]);
                }
                foreach ($filter as $key => $val) {
                    $query->where($key, $val);
                }
            }
            $query->with("province")
                ->with("district")
                ->with("ward")
                ->orderBy('id', 'DESC');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == "list-item-report-purchases-status") {
            $result =  $this->_model->select('order_status', DB::raw("SUM(price_total) as total"))
                ->groupBy("order_status")
                ->get();
        }
        if ($options['task'] == "list-item-report-count-status") {
            $result =  $this->_model->select('order_status', DB::raw("Count(id) as number"))
                ->groupBy("order_status")
                ->get();
        }
        if ($options['task'] == "list-item-new-order") {
            $result =  $this->_model->select()
                ->with(array('items' => function ($query) {
                    $query->select('order_id', DB::raw("SUM(qty) as total"))->groupBy("order_id");
                }))
                ->where("order_status", 'awaiting')
                ->orderBy('id', 'DESC')
                ->get();
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select()
                ->with("province")
                ->with("district")
                ->with("ward")
                ->with('items')
                ->with('timeline')
                ->where('id', $params['id'])
                ->first();
        }
        if ($options['task'] == 'get-item-invoice') {
            $result = $this->_model->select()
                ->with("province")
                ->with("district")
                ->with("ward")
                ->with('items')
                ->with('timeline')
                ->where('invoice_id', $params['invoice_id'])
                ->first();
        }
        if ($options['task'] == 'is_purchase') {
            $params['phone'] = preg_replace('/\s+/', '', $params['phone']);
            $result = $this->_model->select()
                ->where('phone', $params['phone'])
                ->where('order_status', "success")
                ->where('shipping_status', "success")
                ->first();
            return ($result) ? 1 : 0;
        }
        if ($options['task'] == 'check-apply-coupon') {
            $params['phone'] = preg_replace('/\s+/', '', $params['phone']);
            $result = $this->_model->select('coupon_code', DB::raw("Count(phone) as total"))
                ->where('coupon_code', $params['coupon_code'])
                ->where('phone', $params['phone'])
                ->orwhere('email', $params['email'])
                ->where('order_status', '!=', "cancel")
                ->groupBy("coupon_code")
                ->first();
            return $result;
            //return ($result) ? true : false;
        }
        return $result;
    }

    // @Override
    public function saveItem($params = null, $options = null)
    {
        $flag = true;

        if ($options['task'] == "admin-update-multi-cancel") {
            $order_id = $params['id'];
            $row = $this->_model::where('id', $order_id)->first();
            if (isset($params['invoice_id'])) {
                $row->invoice_id = $params['invoice_id'];
            }
            if (isset($params['order_status'])) {
                $row->order_status = $params['order_status'];
            }
            if (isset($params['shipping_status'])) {
                $row->shipping_status = $params['shipping_status'];
            }
            if (isset($params['payment_status'])) {
                $row->payment_status = $params['payment_status'];
            }
            $row->save();

            $listProducts = (new \App\Models\OrderItems())->where("order_id",  $order_id)->get();
            $task_inventory = 'update-qty-inventory-cancel';
            foreach ($listProducts as $product) {
                $param = [
                    'qty' => $product['qty'],
                    'id' => $product['product_id'],
                    'order_id' => $order_id
                ];

                $this->inventory->saveItem($param, ['task' => $task_inventory]);
                if ($product->gift != 'null') {
                    $gifts = json_decode($product->gift, true);
                    $sale_id = (isset($gifts['info'])) ? $gifts['info']['id'] : null;
                    if ($sale_id > 0) {
                        $dataSalesUpdate = [
                            'qty' => $product['qty'],
                            'product_id' => $product['product_id'],
                            'product_sales_id' => $sale_id
                        ];
                        $this->sales->saveItem($dataSalesUpdate, ['task' => 'update-quantity_is_uses-order-cancel']);
                    }
                    if (isset($gifts['gift_items']) && count($gifts['gift_items']) > 0) {
                        foreach ($gifts['gift_items'] as $gift) {
                            //print_r($gift);die();
                            $param_gift = [
                                'qty' => $gifts['qty'],
                                'id' => $gift['id'],
                                'order_id' => $order_id
                            ];
                            $this->inventory->saveItem($param_gift, ['task' => $task_inventory]);
                        }
                    }
                }
            }
            $shipping_comment = config("configs.shipping_status")[$params['shipping_status']]['comment'];
            $order_comment = config("configs.order_status")[$params['order_status']]['comment'];
            $payment_comment = config("configs.payment_status")[$params['payment_status']]['comment'];
            $this->timeline->createdTimeLine($shipping_comment, auth()->user()->username);
            $this->timeline->createdTimeLine($payment_comment, auth()->user()->username);
            $this->timeline->createdTimeLine($order_comment, auth()->user()->username);
            $timelineData = $this->timeline->getData();
            $dataTimeline = ['order_id' =>  $params['id'], 'comments' => json_encode($timelineData)];
            (new OrderTimelines())->updateOrCreate(['order_id' => $params['id']], $dataTimeline);
            return true;
        }
        // DB::beginTransaction();
        // try {

            if ($options['task'] == 'update-invoice') {
                $row = $this->_model::where('id', $params['id'])->first();
                if (isset($params['invoice_id'])) {
                    $row->invoice_id = $params['invoice_id'];
                }
                if (isset($params['order_status'])) {
                    $row->order_status = $params['order_status'];
                }
                if (isset($params['shipping_status'])) {
                    $row->shipping_status = $params['shipping_status'];
                }
                if (isset($params['payment_status'])) {
                    $row->payment_status = $params['payment_status'];
                }
                $row->save();
                $dataTimeline = ['order_id' =>  $params['id'], 'comments' => json_encode($params['timeline'])];
                (new OrderTimelines())->updateOrCreate(['order_id' => $params['id']], $dataTimeline);
                DB::commit();
                return true;
            }
            if ($options['task'] == 'update-comment') {
                $dataTimeline = ['order_id' =>  $params['id'], 'comments' => json_encode($params['timeline'])];
                (new OrderTimelines())->updateOrCreate(['order_id' => $params['id']], $dataTimeline);
                DB::commit();
                return true;
            }
            //  echo "<pre>";
            //  print_r($params);die();
            if ($options['task'] == 'add-item') {
                $infomation = $params['customer'];
                $shoppingCart = $params['shoppingCart'];
                $row = $this->_model;
                //$row->invoice_id = $infomation['invoice_id'];
                $infomation['phone'] = preg_replace('/\s+/', '', $infomation['phone']);
                $row->name = $infomation['name'];
                $row->gender = $infomation['gender'];
                $row->phone = $infomation['phone'];
                $row->email = $infomation['email'];
                $row->city_id = $infomation['city_id'];
                $row->district_id = $infomation['district_id'];
                $row->ward_id = $infomation['ward_id'];
                $row->address = $infomation['address'];
                $row->note = $infomation['note'];
                $row->price_total = $params['price_total'];
                $row->price_shipping = $params['shipping'];
                $row->price_discount = $params['price_discount'];
                $row->coupon_code = $params['coupon_code'];
                $row->order_status = $params['order_status'];
                $row->shipping_status = $params['shipping_status'];
                $row->payment_status = $params['payment_status'];
                $row->payment_method = $params['payment_method'];

                $row->save();
                $order_id = $row->id;
               
                // Save Order Item
                $item = [];
                foreach ($shoppingCart as $cart) {
                    $options =  isset($cart['options']) ? json_encode($cart['options']) : null;
                    $option_entries =  isset($cart['option_entries']) ? json_encode($cart['option_entries']) : null;
                    $image_src = (new \App\Helpers\Product\Image())->getLinkDefault($cart, 'small');
                    $special_price = $cart['special_price'];
                    $dateFrom = $cart['special_price_from'];
                    $dateTo = $cart['special_price_to'];
                    // if(Price::sales($cart)){
                    //     $special_price = Price::getPrice($cart);
                    //     $dateFrom = Price::get_sales_from($cart);
                    //     $dateTo = Price::get_sales_to($cart);
                    // }
                    $item[] = [
                        'product_id' => $cart['id'],
                        'order_id' => $order_id,
                        'name' => $cart['name'],
                        'sku' => $cart['sku'],
                        'qty' => $cart['qty'],
                        'price' => $cart['price'],
                        'special_price' => $special_price,
                        'special_price_from' => $dateFrom,
                        'special_price_to' => $dateTo,
                        'options' =>  $options,
                        'option_entries' =>  $option_entries,
                        'picture' => $image_src,
                        'path' => $cart['url']['path'],
                        'gift' => json_encode(\App\Helpers\Product\Sale::saleGiftCheckoutData($cart))
                    ];
                }
                
                // echo "<pre>";print_r($item);die();
                (new OrderItems())->insert($item);
                
                // Save Order Timeline
                $this->timeline->createdOrder();
               
                $timelines = $this->timeline->getData();
                $dataTimeline = ['order_id' => $order_id, 'comments' => json_encode($timelines)];
                
                (new OrderTimelines())->insert($dataTimeline);
                //update Inventory
                
                DB::commit();
                if ($params['payment_method'] == 'cash_on_delivery') {
                    $this->updateInventory($shoppingCart, $order_id);
                }
                
                return $order_id;
            }
        // } catch (\Exception $ex) {
        //     DB::rollBack();
        //     return false;
        // }
    }
    public function updateInventory($shoppingCart, $order_id = 0)
    {

        if ($order_id > 0) {

            foreach ($shoppingCart as $cart) {
                $cart['order_id'] = $order_id;
                
                $this->inventory->saveItem($cart, ['task' => 'update-inventory-order']);
                $gifts = \App\Helpers\Product\Sale::saleGiftCheckoutData($cart);
                
                $product_sale_id = \App\Helpers\Product\Sale::get_sales_id($cart);
                if ($product_sale_id > 0) {
                    $dataSalesUpdate = [
                        'qty' => $cart['qty'],
                        'product_sales_id' => \App\Helpers\Product\Sale::get_sales_id($cart),
                        'product_id' => $cart['id']
                    ];
                    $this->sales->saveItem($dataSalesUpdate, ['task' => 'update-quantity_is_uses-order']);
                }

                if ($gifts != null && isset($gifts['gift_items'])) {
                    foreach ($gifts['gift_items'] as $gift) {
                        $data_gift['id'] = $gift['id'];
                        $data_gift['order_id'] = $order_id;
                        $data_gift['qty'] = $gifts['qty'];
                        //$data_gift['product_sales_id'] = $gifts['product_sales_id'];
                        $this->inventory->saveItem($data_gift, ['task' => 'update-inventory-order']);
                    }
                }
            }
        }
    }
    // @Override
    public function deleteItem($params = null, $options = null)
    {
        $result = 0;
        DB::beginTransaction();
        try {
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $this->_model->where('id', $params['id'])->delete();
                    (new OrderItems())->where('order_id', $params['id'])->delete();
                    (new OrderTimelines())->where('order_id', $params['id'])->delete();
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
