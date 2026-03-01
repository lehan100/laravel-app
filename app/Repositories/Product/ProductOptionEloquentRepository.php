<?php

namespace App\Repositories\Product;

use Illuminate\Support\Facades\DB;
use App\Helpers\Price as Price;
use App\Repositories\EloquentRepository;
use App\Repositories\Product\ProductOptionRepositoryInterface;
use App\Models\ProductOptionAttribute;
use Exception;
use Illuminate\Support\Carbon;

class ProductOptionEloquentRepository extends EloquentRepository implements ProductOptionRepositoryInterface {

    /**
     * get model
     * @return string
     */
    public function getModel() {
        return \App\Models\ProductOption::class;
    }

    // @Override
    public function listItems($params = null, $options = null) {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select('id', 'title', 'sku', 'product_id', 'type', 'status')
                    ->with('attributes')
                    ->orderBy('id', 'asc');
            $result = $query->get();
        }
        if ($options['task'] == "admin-list-options") {

            $query = $this->_model->select()
                    ->with('attributes')
                    ->whereIn("id", $params['option_id'])
                    ->orderBy('id', 'asc');
            $result = $query->get();
        }
        
        return $result;
    }

    public function getItem($params = null, $options = null) {
        
    }

    // @Override
    public function saveItem($params = null, $options = null) {
        DB::beginTransaction();
        try {
            if (isset($params['option_index']) && count($params['option_index']) > 0) {
                foreach ($params['option_index'] as $k => $index) {
                    $flag = false;
                    if ($params['option_task'][$index] == "edit-item") {
                        $row = $this->_model::where('id', $params['option_id'][$index])->first();
                        $flag = true;
                    } elseif ($params['option_task'][$index] == "add-item") {
                        $row = new $this->_model;
                        $flag = true;
                    }
                    if ($flag) {
                        $row->title = $params['option_title'][$index];
                        $row->product_id = $params['product_id'];
                        $row->type = $params['option_type'][$index];
                        $row->status = isset($params['option_status'][$index]) ? 1 : 0;
                        $row->sort = $k;
                        $row->save();
                        $id = $row->id;
                        $this->saveAttributes($params, $index, $id);
                    }
                }
            }
            if (isset($params['task-main']) && $params['task-main'] == 'edit-item') {
                $option_id = $params['option_de_id'];
                $option_value_id = $params['option_value_id'];
                if ($option_id != "") {
                    $arrIdOption = explode(",", $option_id);
                    $productOptions = $this->listItems(['option_id' => $arrIdOption], ['task' => 'admin-list-options']);
                    $this->_model->whereIn('id', $arrIdOption)->delete();
                    (new ProductOptionAttribute())->whereIn('option_id', $arrIdOption)->delete();
                    if (count($productOptions) > 0) {
                        foreach ($productOptions as $option) {
                            event(new \App\Events\ProductTrashImageOption($option->attributes));
                        }
                    }
                }

                if ($option_value_id != "") {
                    $arrIdOptionValue = explode(",", $option_value_id);
                    $this->deleteItems(['option_value_id' => $arrIdOptionValue], ['task' => 'delete-option-value']);
                }
                DB::commit();
            } else {
                DB::commit();
            }
            return TRUE;
        } catch (\Exception $e) {
            DB::rollBack();
            return FALSE;
        }
        return TRUE;
    }

    public function saveAttributes($params = null, $index = 0, $id = 0) {
        foreach ($params['option_attr_title'][$index] as $k => $val) {
            $flag = false;
            if ($params['option_attr_task'][$index][$k] == "edit-item") {
                $row = (new ProductOptionAttribute())->where('id', $params['option_attr_id'][$index][$k])->first();
                $flag = true;
            } elseif ($params['option_attr_task'][$index][$k] == "add-item") {
                $row = new ProductOptionAttribute();
                $flag = true;
            }

            $row->title = $params['option_title'][$index];
            $row->option_id = $id;
            $row->title = $val;
            $row->price = Price::getPrice($params['option_attr_price'][$index][$k]);
            $row->special_price = Price::getPrice($params['option_attr_special_price'][$index][$k]);
            if ($params['option_attr_special_price_date'][$index][$k] != "") {
                $special_date = explode('-', $params['option_attr_special_price_date'][$index][$k]);
                $row->special_price_from = Carbon::parse($special_date[0]);
                $row->special_price_to = Carbon::parse($special_date[1]);
            } else {
                $row->special_price_from = null;
                $row->special_price_to = null;
            }
            $row->picture = $params['option_attr_picture'][$index][$k];
            $row->color = $params['option_attr_color'][$index][$k];
            $row->status = isset($params['option_attr_status'][$index][$k]) ? 1 : 0;
            $row->sort = $k;
            $row->save();
            //Save Picture
            $picture = $params['option_attr_picture'][$index][$k];
            event(new \App\Events\ProductResizeImageOption($picture));
        }
    }

    public function deleteItems($params = null, $options = null) {

        DB::beginTransaction();
        $tbProductOptionAttribute = new ProductOptionAttribute();
        try {
            if ($options['task'] == 'delete-option-value') {
                if (isset($params['option_value_id']) && count($params['option_value_id']) > 0) {
                    $listOptionValue = $tbProductOptionAttribute->select()->whereIn('id', $params['option_value_id'])->get();
                    $tbProductOptionAttribute->whereIn("id", $params['option_value_id'])->delete();
                    DB::commit();
                    if (count($listOptionValue) > 0) {
                        event(new \App\Events\ProductTrashImageOption($listOptionValue));
                    }
                }
            }
            return TRUE;
        } catch (Exception $exc) {
            DB::rollBack();
            return FALSE;
        }
    }

    // @Override
    public function deleteItem($params = null, $options = null) {
        DB::beginTransaction();
        $tbProductOptionAttribute = new ProductOptionAttribute();
        try {
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $this->_model->where("id", $params['id'])->delete();
                    $listOptionValue = $tbProductOptionAttribute->select()->whereIn('option_id', $params['id'])->get();
                    $tbProductOptionAttribute->where("option_id", $params['id'])->delete();
                    DB::commit();
                    if (count($listOptionValue) > 0) {
                        event(new \App\Events\ProductTrashImageOption($listOptionValue));
                    }
                }
            }
            return TRUE;
        } catch (Exception $exc) {
            DB::rollBack();
            return FALSE;
        }
    }
}
