<?php

namespace App\Repositories\Sales;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Repositories\EloquentRepository;
use App\Helpers\Price as Price;
use App\Models\UrlRewrite;
use App\Models\ProductSaleItems;
use App\Repositories\Product\ProductEloquentRepository;

class SalesEloquentRepository extends EloquentRepository implements SalesRepositoryInterface
{
    protected $cachePrefix = 'sales-';
    /**
     * get model
     * @return string
     */
    private $id_update;
    public function getModel()
    {
        return \App\Models\ProductSales::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select()
                ->orderBy('id', 'asc');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == "admin-list-items-sitemap") {
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            $query = $this->_model->select()
                ->with("url")
                ->whereDate('date_from', '<=', $now)
                ->whereDate('date_to', '>=', $now)
                ->orderBy('id', 'asc');
            $result = $query->get();
        }
        if ($options['task'] == "fronend-list-items") {
            $keycache = $this->cachePrefix . $options['task'];
            $result = Cache::store('custom')->get($keycache, function () {
                $now = Carbon::now('Asia/Ho_Chi_Minh');
                $query = $this->_model->select()
                    ->with(array(
                        'url',
                        'product_items' => function ($query) {
                            $query->with(array('product' => function ($query) {
                                $query->with(array(
                                    'url',
                                    'ratings' => function ($query) {
                                        $query->select('product_id', DB::raw("SUM(rating) as sum_star"), DB::raw("Count(id) as total_rating"))->groupBy("product_id");
                                    }
                                ))->where('status', 1);
                            }))->take(10);
                        }
                    ))
                    ->whereDate('date_from', '<=', $now)
                    ->whereDate('date_to', '>=', $now)
                    ->where('status', 1)
                    ->where('is_homepage', 1);
                return $query->get();
            });

            // echo "<pre>";
            // print_r($result->toArray());
            // die();
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == 'frontend-get-item') {
            $now = Carbon::now();
            $result = $this->_model->select()
                ->with("url")
                ->where('id', $params['id'])
                ->first();
        }
        if ($options['task'] == 'get-item') {
            $now = Carbon::now();
            $result = $this->_model->select()
                ->with("url")
                ->where('id', $params['id'])
                ->whereDate('date_from', '<=', $now)
                ->whereDate('date_to', '>=', $now)
                ->where('status', 1)
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
        if ($options['task'] == 'update-quantity_is_uses-order') {
            $data = array(
                // 'quantity_is_uses_product' =>  DB::raw('quantity_is_uses_product-' . $params['qty']),
                'order_qty' =>  DB::raw('order_qty+' . $params['qty']),
            );
            (new ProductSaleItems())->where('product_id', $params['product_id'])->where('product_sales_id', $params['product_sales_id'])->update($data);
        }
        if ($options['task'] == 'update-quantity_is_uses-order-cancel') {
            $data = array(
                // 'quantity_is_uses_product' =>  DB::raw('quantity_is_uses_product+' . $params['qty']),
                'order_qty' =>  DB::raw('order_qty-' . $params['qty']),
            );
            (new ProductSaleItems())->where('product_id', $params['product_id'])->where('product_sales_id', $params['product_sales_id'])->update($data);
        }
        DB::beginTransaction();
        try {
            $alias = \App\Helpers\Filter::setUrlKey($params['name']);
            if ($options['task'] == 'add-item') {
                $row = $this->_model;
                $row->name = $params['name'];
                $row->alias = $alias;
                $row->description = $params['description'];
                $row->date_from = Carbon::createFromFormat('d/m/Y', $params['date_from'])->format('Y-m-d');
                $row->date_to = Carbon::createFromFormat('d/m/Y', $params['date_to'])->format('Y-m-d');
                $row->status = isset($params['status']) ? 1 : 0;
                $row->is_homepage = isset($params['is_homepage']) ? 1 : 0;
                $row->save();
                $id = $row->id;
                $this->setUrlRewrite($alias, $id);
                if ($params['condition_sales'] != "") {
                    $this->saleItems($params['condition_sales'], $id);
                }
                if ($params['product_ids_delete'] != "") {
                    $this->deleteSaleItems($params['product_ids_delete'], $id);
                }
                DB::commit();
            }
            if ($options['task'] == 'edit-item') {
                $row = $this->_model::where('id', $params['id'])->first();
                $row->name = $params['name'];
                $row->alias = $alias;
                $row->description = $params['description'];
                $row->date_from = Carbon::createFromFormat('d/m/Y', $params['date_from'])->format('Y-m-d');
                $row->date_to = Carbon::createFromFormat('d/m/Y', $params['date_to'])->format('Y-m-d');
                $row->status = isset($params['status']) ? 1 : 0;
                $row->is_homepage = isset($params['is_homepage']) ? 1 : 0;
                $row->save();
                $id = $row->id;
                $this->setUrlRewrite($alias, $id);
                if ($params['condition_sales'] != "") {
                    $this->saleItems($params['condition_sales'], $id);
                }
                if ($params['product_ids_delete'] != "") {
                    $this->deleteSaleItems($params['product_ids_delete'], $id);
                }
                DB::commit();
            }
            return $id;
        } catch (\Exception $e) {
            DB::rollBack();
            return FALSE;
        }
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
        $result = 0;
        if ($options['task'] == 'delete-item-multi') {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $items = $this->_model->select("id", 'name')
                    ->whereIn('id', $params['aid'])
                    ->get();
                if (count($items) > 0) {
                    $this->_model->whereIn('id', $params['aid'])->delete();
                    (new ProductSaleItems())
                        ->whereIn('product_sales_id', $params['aid'])
                        ->delete();
                    foreach ($items as $item) {
                        $this->deleteUrlRewrite($item['id']);
                    }
                }
            }
        }
        if ($options['task'] == 'delete-item') {
            if (isset($params['id'])) {
                $result = $this->_model->where('id', $params['id'])->delete();
                (new ProductSaleItems())
                    ->where('product_sales_id', $params['id'])
                    ->delete();
                $this->deleteUrlRewrite($params['id']);
            }
        }
        return ($result > 0) ? TRUE : FALSE;
    }
    public function setUrlRewrite($alias = "", $sale_id = 0)
    {
        if ($alias != "" && $sale_id > 0) {
            $aliasPath = "promotions/" . $alias;
            $route = "product/sale/" . $sale_id;
            $data_urlRewrite = array(
                'path' => $aliasPath . ".html",
                'route' => $route,
                'sale_id' => $sale_id
            );
            (new UrlRewrite())->updateOrCreate(['sale_id' => $sale_id], $data_urlRewrite);
        }
    }

    public function deleteUrlRewrite($sale_id = 0)
    {
        if ($sale_id > 0) {
            (new UrlRewrite())::withTrashed()->where("sale_id", $sale_id)->forceDelete();
        }
    }
    public function saleItems($data_sale, $sale_id)
    {
        $data = json_decode($data_sale, true);
        foreach ($data as $item) {
            $gift_sku_info = null;
            if ($item['gift_sku'] != "") {
                $gift_sku_info = (new ProductEloquentRepository())->listItems(['sku' => $item['gift_sku']], ['task' => 'frontend-get-items-gift-buySku']);
                foreach ($gift_sku_info as $key => $sku_item) {
                    $picture = json_decode($sku_item['picture'], true);
                    $gift_sku_info[$key]['picture'] = $picture[0];
                }
                $gift_sku_info = json_encode($gift_sku_info->toArray());
            }

            $data_update = [
                'product_sales_id' => $sale_id,
                'product_id' => $item['product_id'],
                'quantity_is_uses_product' => $item['quantity_is_uses_product'],
                'special_percent' => $item['special_percent'],
                'special_price' => Price::getPrice($item['special_price']),
                'buy_qty' => $item['buy_qty'],
                'gift_qty' => $item['gift_qty'],
                'gift_sku' => $item['gift_sku'],
                'gift_sku_info' => $gift_sku_info,
                'status' => 1
            ];
            (new ProductSaleItems())->updateOrCreate(['product_id' => $item['product_id'], 'product_sales_id' => $sale_id], $data_update);
        }
    }
    public function deleteSaleItems($product_ids, $sale_id)
    {
        $data_product_ids = explode(",", $product_ids);
        if ($sale_id > 0) {
            (new ProductSaleItems())->where("product_sales_id", $sale_id)->whereIn("product_id", $data_product_ids)->delete();
        }
    }
}
