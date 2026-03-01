<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Services\ShoppingCart;
use App\Services\ShoppingCartInfo;
use App\Helpers\Product\Price;

class CouponCode
{
    protected $data;
    private $shoppingCart;
    private $shoppingCartInfo;
    public function __construct(ShoppingCart $shoppingCart,ShoppingCartInfo $shoppingCartInfo)
    {
        $this->shoppingCart = $shoppingCart;
        $this->shoppingCartInfo = $shoppingCartInfo;
    }
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    public function validateTime()
    {
        $startDate = Carbon::parse($this->data['date_from'])->format('m/d/Y') . " 00:00:00";
        $endDate = Carbon::parse($this->data['date_to'])->format('m/d/Y') . " 23:59:59";
        if (Carbon::now('Asia/Ho_Chi_Minh')->between($startDate, $endDate)) {
            return true;
        }
        return false;
    }
    public function validateProduct()
    {
        if ($this->data['is_verify'] == 0) {
            return true;
        }
        $product_coupon_codes = $this->data['product_coupon_codes'];
        $product_ids =  Arr::pluck(Arr::where($product_coupon_codes, function ($val) {
            return $val['product_id'] > 0 && $val['product_of_coupons'] != null;
        }), 'product_id');
        $category_ids =  Arr::pluck(Arr::where($product_coupon_codes, function ($val) {
            return $val['category_id'] > 0 && count($val['category_of_coupons']) > 0;
        }), 'category_id');
        if (count($product_ids) > 0 || count($category_ids) > 0) {
            return true;
        }
        return false;
    }
    public function validateTotal($total = 0)
    {
        if ($total >= $this->data['discount_amount_from']) {
            return true;
        }
        return false;
    }
    public function calculator()
    {
        $subtotal = $this->shoppingCart->getTotal();
        if ($this->validateTime() && $this->validateTotal($subtotal)) {

            switch ($this->data['type']) {
                case '0':
                    $discount = $this->data['discount_amount'];
                    if ($discount > $subtotal) {
                        $discount = $subtotal;
                    }
                    break;
                case '1':
                    $discount = ($this->data['discount_amount'] / 100) * $subtotal;
                    if ($discount > $this->data['discount_max']) {
                        $discount = $this->data['discount_max'];
                    }
                    break;
            }
            if ($this->data['is_verify'] == 0) {
                return $discount;
            } else if ($this->validateProduct()) {
                return $discount;
            }
            return 0;
        }
    }

    public function verify()
    {
        $subtotal = $this->shoppingCart->getTotal();
        try {
            if (!$this->data) {
                $this->shoppingCartInfo->deleteCoupon();
                return [
                    'status' => false,
                    'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn'
                ];
            } else if (!$this->validateProduct()) {
                $this->shoppingCartInfo->deleteCoupon();
                return [
                    'status' => false,
                    'message' => 'Mã giảm giá không được áp dụng cho các sản phẩm trong giỏ hàng'
                ];
            } else if (!$this->validateTime()) {
                $this->shoppingCartInfo->deleteCoupon();
                return [
                    'status' => false,
                    'message' => 'Mã giảm giá đã hết hạn'
                ];
            } else if (!$this->validateTotal($subtotal)) {
                $this->shoppingCartInfo->deleteCoupon();
                return [
                    'status' => false,
                    'message' => 'Mã giảm giá chỉ áp dụng cho đơn hàng từ ' . Price::format_price($this->data['discount_amount_from'])
                ];
            } else {
                return [
                    'status' => true,
                    'message' => 'Sử dụng mã giảm giá thành công'
                ];
            }
        } catch (\Throwable $th) {
            $this->shoppingCartInfo->deleteCoupon();
            return [
                'status' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn'
            ];
        }
    }
}
