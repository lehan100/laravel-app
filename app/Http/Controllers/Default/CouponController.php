<?php

namespace App\Http\Controllers\Default;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Requests\Default\CouponPostRequest;
use App\Http\Controllers\FrontendController;
use App\Repositories\Coupon\CouponRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Services\ShoppingCart;
use App\Services\ShoppingCartInfo;
use App\Services\CouponCode;

class CouponController extends FrontendController
{

    protected $controllerView = 'default.pages.coupon.';
    protected $controllerViewLayout = 'default.layouts.';
    protected $controllerName = 'coupon';
    protected $couponModel;
    protected $orderModel;
    protected $shoppingCart;
    protected $shoppingCartInfo;
    protected $couponService;
    public function __construct(
        CouponRepositoryInterface $couponModel,
        OrderRepositoryInterface $orderModel,
        ShoppingCart $shoppingCart,
        CouponCode $couponService,
        ShoppingCartInfo $shoppingCartInfo
    ) {
        parent::__construct();
        $this->couponModel = $couponModel;
        $this->orderModel = $orderModel;
        $this->shoppingCart = $shoppingCart;
        $this->shoppingCartInfo = $shoppingCartInfo;
        $this->couponService = $couponService;
    }

    public function verify(CouponPostRequest $request)
    {
        try {
            $this->params['coupon_code'] = $request->coupon_code;
            $this->params['ids'] = $this->shoppingCart->getIDs();
            $couponDetail = $this->couponModel->getItem($this->params, ['task' => 'frontend-get-item']);
			
            if ($couponDetail) {
                $this->couponService->setData($couponDetail->toArray());
            } else {
                $this->couponService->setData($couponDetail);
            }
            $discountCoupon = $this->couponService->verify();
			
            $discount = 0;
            $shippingPrice = $this->shoppingCartInfo->getShipping();
            $subtotal = $this->shoppingCart->getTotal();
            if ($discountCoupon['status'] == true) {
                $info = $this->shoppingCartInfo->getInfo();
                if ($info) {
                    $info['coupon_code'] = $this->params['coupon_code'];
                    $verifyApply = $this->orderModel->getItem($info, ['task' => 'check-apply-coupon']);
                    if ($verifyApply && $verifyApply['coupon_code']!='' && $verifyApply['total'] >= $couponDetail['max_uses_user']) {
                        $this->shoppingCartInfo->deleteCoupon();
                        return response()->json([
                            'status' =>  true, 'verify' => [
                                'status' => false,
                                'message' => 'Mã giảm giá không thể  sử dụng',
                                'data' => $verifyApply,

                            ],
                            'subtotal' => $subtotal,
                            'shipping' => $shippingPrice
                        ], 200);
                    }
                }

                $discount = $this->couponService->calculator();
            }
            if ($couponDetail) {
                $couponDetail = $couponDetail->toArray();
            }
            unset($couponDetail['product_coupon_codes']);
            $this->shoppingCartInfo->setDiscountCode($couponDetail, $discount);

            return response()->json(['status' =>  true, 'verify' => $discountCoupon, 'subtotal' => $subtotal, 'shipping' => $shippingPrice, 'coupon' => $this->shoppingCartInfo->getDiscountCode()], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false], 400);
        }
    }
}
