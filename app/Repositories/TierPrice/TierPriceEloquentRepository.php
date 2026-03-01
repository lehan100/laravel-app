<?php

namespace App\Repositories\TierPrice;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Repositories\EloquentRepository;
use App\Models\ProductofTierPrices;
use App\Helpers\Price as Price;
use App\Models\TierPriceItems;

class TierPriceEloquentRepository extends EloquentRepository implements TierPriceRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    private $id_update;
    public function getModel()
    {
        return \App\Models\TierPrice::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select()
                ->with('items',"products")
                ->orderBy('id', 'asc');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select()
                ->with(array("items", "products"))
                ->where('id', $params['id'])->first();
        }
        if ($options['task'] == 'frontend-get-item') {
            $result = $this->_model->select()
                ->with("items")
                ->where('product_id', $params['product_id'])
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
            }
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->_model->where('id', $params['id'])->update(['status' => $status]);
        }
        try {
            DB::beginTransaction();
            if ($options['task'] == 'add-item') {
                $row = $this->_model;
                $row->date_from = Carbon::createFromFormat('d/m/Y', $params['date_from'])->format('Y-m-d');
                $row->date_to = Carbon::createFromFormat('d/m/Y', $params['date_to'])->format('Y-m-d');
                $row->status = isset($params['status']) ? 1 : 0;
                $row->save();
                $result = $row->id;
                $this->saveItems($result, $params);
                if ($params['product_ids'] != "") {
                    $this->saveItemProducts($params['product_ids'], $result);
                }
                if ($params['product_ids_delete'] != "") {
                    $this->deleteItemProducts($params['product_ids_delete'], $result);
                }
                DB::commit();
            }
            if ($options['task'] == 'edit-item') {
               
               $status = isset($params['status']) ? 1 : 0;
                $row = $this->_model::where('id', $params['id'])->first();
                $row->date_from = Carbon::createFromFormat('d/m/Y', $params['date_from'])->format('Y-m-d');
                $row->date_to = Carbon::createFromFormat('d/m/Y', $params['date_to'])->format('Y-m-d');
                $row->status = isset($params['status']) ? 1 : 0;
                $row->save();
                $result = $row->id;
                $this->saveItems($result, $params);
                if ($params['product_ids'] != "") {
                    $this->saveItemProducts($params['product_ids'], $result);
                }
                if ($params['product_ids_delete'] != "") {
                    $this->deleteItemProducts($params['product_ids_delete'], $result);
                }
                if ($params['tier_price_option_delete'] != "") {
                    $this->deleteItemOptions($params['tier_price_option_delete']);
                }
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return FALSE;
        }

        return ($result > 0) ? $result : FALSE;
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
        $result = 0;
        if ($options['task'] == 'delete-item-multi') {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $result = $this->_model->whereIn('id', $params['aid'])->delete();
                (new \App\Models\TierPriceItems())
                    ->whereIn('tier_price_id', $params['aid'])
                    ->delete();
                 (new \App\Models\ProductofTierPrices())
                    ->whereIn('tier_price_id', $params['aid'])
                    ->delete();
            }
        }
        if ($options['task'] == 'delete-item') {
            if (isset($params['id'])) {
                $result = $this->_model->where('id', $params['id'])->delete();
                (new \App\Models\TierPriceItems())
                    ->where('tier_price_id', $params['id'])
                    ->delete();
                (new \App\Models\ProductofTierPrices())
                    ->where('tier_price_id', $params['id'])
                    ->delete();
            }
        }
        return ($result > 0) ? TRUE : FALSE;
    }


    public function saveItems($tier_price_id = -1, $data_items = null)
    {
        if ($data_items != null && $tier_price_id > -1 && isset($data_items['option_qty'])) {
            foreach ($data_items['option_qty'] as $key => $order_qty) {
                $dataSchema = array(
                    'tier_price_id' => $tier_price_id,
                    'order_qty' => $order_qty,
                    'type' => $data_items['type'],
                    'status' => 1,
                    'special_percent' => (int) $data_items['option_special_percent'][$key],
                    'special_price' => (int) Price::getPrice($data_items['option_special_price'][$key]),
                );
                $tier_price_option_id = $data_items['tier_price_option_id'][$key];
                if ($tier_price_option_id > -1) {
                    (new \App\Models\TierPriceItems())->updateOrCreate(['tier_price_id' => (int) $tier_price_id, 'id' => $tier_price_option_id], $dataSchema);
                } else {
                    (new \App\Models\TierPriceItems())->insert($dataSchema);
                }
            }
        }
    }
    public function saveItemProducts($product_ids = '', $tier_price_id = -1)
    {
        if ($product_ids != '' && $tier_price_id > -1) {
            $data_product_ids = explode(",", $product_ids);
            foreach ($data_product_ids as $key => $product_id) {
                $dataSchema = array(
                    'tier_price_id' => $tier_price_id,
                    'product_id' => $product_id
                );
                (new \App\Models\ProductofTierPrices())->updateOrCreate(['tier_price_id' => $tier_price_id, 'product_id' => $product_id], $dataSchema);
            }
        }
    }
    public function deleteItemProducts($product_ids, $tier_price_id)
    {
        $data_product_ids = explode(",", $product_ids);
        if ($tier_price_id > 0) {
            (new ProductofTierPrices())->where("tier_price_id", $tier_price_id)->whereIn("product_id", $data_product_ids)->delete();
        }
    }
    public function deleteItemOptions($tier_price_option_delete)
    {
        $tier_price_option_ids = explode(",", $tier_price_option_delete);
        if ($tier_price_option_ids > 0) {
            (new TierPriceItems())->whereIn("id", $tier_price_option_ids)->delete();
        }
    }
}
