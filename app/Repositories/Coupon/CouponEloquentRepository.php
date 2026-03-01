<?php

namespace App\Repositories\Coupon;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Repositories\EloquentRepository;
use App\Helpers\Price as Price;

class CouponEloquentRepository extends EloquentRepository implements CouponRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    private $id_update;
    public function getModel()
    {
        return \App\Models\CouponCode::class;
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
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select()
                ->with(array('product_coupon_codes' => function ($query) {
                    $query->with(array(
                        'product_of_coupons' => function ($query) {
                            $query->with("product");
                        },
                        'category_of_coupons'
                    ));
                }))
                // ->with(array('product_coupon_codes'=>function($query){
                //     $query->with(array(
                //         'product_of_coupons',
                //     'category_of_coupons'=>function($query){
                //         $query->with("product");
                //     }));
                // }))

                ->where('id', $params['id'])->first();
        }
        if ($options['task'] == 'frontend-get-item') {
            $result = $this->_model->select()
                ->with(array('product_coupon_codes' => function ($query) use ($params) {
                    $query->with(array(
                        'product_of_coupons' => function ($query)  use ($params) {
                            if (count($params['ids']) > 0) {
                                $query->whereIn("product_id", $params['ids']);
                            }
                        },
                        'category_of_coupons' => function ($query)  use ($params) {
                            if (count($params['ids']) > 0) {
                                $query->whereIn("product_id", $params['ids']);
                            }
                        }
                    ));
                }))

                ->where('coupon_code', $params['coupon_code'])
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
        if ($options['task'] == 'update-uses') {
            $this->_model->where('coupon_code', $params['coupon_code'])->update(['uses' => DB::raw('uses-1')]);
        }
        if ($options['task'] == 'add-item') {
            $row = $this->_model;
            $row->name = $params['name'];
            $row->coupon_code = $params['coupon_code'];
            $row->type = $params['type'];
            $row->uses = $params['uses'];
            $row->max_uses_user = $params['max_uses_user'];
            $row->discount_amount = Price::getPrice($params['discount_amount']);
            $row->discount_amount_from = Price::getPrice($params['discount_amount_from']);
            $row->discount_max = Price::getPrice($params['discount_max']);
            $row->date_from = Carbon::createFromFormat('d/m/Y', $params['date_from'])->format('Y-m-d');
            $row->date_to = Carbon::createFromFormat('d/m/Y', $params['date_to'])->format('Y-m-d');
            $row->status = isset($params['status']) ? 1 : 0;
            $row->is_public = isset($params['is_public']) ? 1 : 0;
            $row->is_verify = ($params['category_id'] == "" && $params['product_id'] == "") ? 0 : 1;
            $row->is_product_use_coupon = isset($params['is_product_use_coupon']) ? 1 : 0;
            $row->save();
            $result = $row->id;
            //if ($params['category_id'] != "") {
            $this->saveItemCouponWithProduct($params['category_id'], $result, 'category');
            // }
            // if ($params['product_id'] != "") {
            $this->saveItemCouponWithProduct($params['product_id'], $result, 'product');
            // }
        }
        if ($options['task'] == 'edit-item') {
            $row = $this->_model::where('id', $params['id'])->first();
            $row->name = $params['name'];
            $row->coupon_code = $params['coupon_code'];
            $row->type = $params['type'];
            $row->uses = $params['uses'];
            $row->max_uses_user = $params['max_uses_user'];
            $row->discount_amount = Price::getPrice($params['discount_amount']);
            $row->discount_amount_from = Price::getPrice($params['discount_amount_from']);
            $row->discount_max = Price::getPrice($params['discount_max']);
            $row->date_from = Carbon::createFromFormat('d/m/Y', $params['date_from'])->format('Y-m-d');
            $row->date_to = Carbon::createFromFormat('d/m/Y', $params['date_to'])->format('Y-m-d');
            $row->status = isset($params['status']) ? 1 : 0;
            $row->is_public = isset($params['is_public']) ? 1 : 0;
            $row->is_verify = ($params['category_id'] == "" && $params['product_id'] == "") ? 0 : 1;
            $row->is_product_use_coupon = isset($params['is_product_use_coupon']) ? 1 : 0;
            $row->save();
            $result = $row->id;
            //if ($params['category_id'] != "") {
            $this->saveItemCouponWithProduct($params['category_id'], $result, 'category');
            // }
            // if ($params['product_id'] != "") {
            $this->saveItemCouponWithProduct($params['product_id'], $result, 'product');
            // }
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
                (new \App\Models\ProductCouponCode())
                    ->whereIn('coupon_code_id', $params['aid'])
                    ->delete();
            }
        }
        if ($options['task'] == 'delete-item') {
            if (isset($params['id'])) {
                $result = $this->_model->where('id', $params['id'])->delete();
                (new \App\Models\ProductCouponCode())
                    ->where('coupon_code_id', $params['id'])
                    ->delete();
            }
        }
        return ($result > 0) ? TRUE : FALSE;
    }

    public function saveItemCouponWithProduct($data = null, $coupon_id = -1, $mode = 'product')
    {
        if ($data != null && $coupon_id > -1) {
            $data = explode(",", $data);
            foreach ($data as $val) {
                if ($mode == 'product') {
                    $dataSchema = array(
                        'coupon_code_id' => $coupon_id,
                        'product_id' => $val
                    );
                } else if ($mode == 'category') {
                    $dataSchema = array(
                        'coupon_code_id' => $coupon_id,
                        'category_id' => $val
                    );
                }
                (new \App\Models\ProductCouponCode())->updateOrCreate($dataSchema, $dataSchema);
            }
            if ($mode == 'product') {
                (new \App\Models\ProductCouponCode())
                    ->where("coupon_code_id", $coupon_id)
                    ->where("category_id", 0)
                    ->whereNotIn('product_id', $data)
                    ->forceDelete();
            }
            if ($mode == 'category') {
                (new \App\Models\ProductCouponCode())
                    ->where("coupon_code_id", $coupon_id)
                    ->where("product_id", 0)
                    ->whereNotIn("category_id", $data)
                    ->forceDelete();
            }
        } else if ($coupon_id > -1) {
            if ($mode == 'product') {
                (new \App\Models\ProductCouponCode())
                    ->where("coupon_code_id", $coupon_id)
                    ->where("category_id", 0)
                    ->forceDelete();
            }
            if ($mode == 'category') {
                (new \App\Models\ProductCouponCode())
                    ->where("coupon_code_id", $coupon_id)
                    ->where("product_id", 0)
                    ->forceDelete();
            }
        }
    }
}
