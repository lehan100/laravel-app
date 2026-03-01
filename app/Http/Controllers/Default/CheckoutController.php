<?php

namespace App\Http\Controllers\Default;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\FrontendController;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Province\ProvinceRepositoryInterface;
use App\Repositories\Coupon\CouponRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Services\ShoppingCart;
use App\Services\ShoppingCartInfo;
use App\Helpers\Seo as SEO;
use App\Http\Requests\Default\CheckoutPostRequest;
use App\Services\Shipping;
use App\Services\Payment\MomoPay;
use App\Helpers\Order\Timeline;
use Illuminate\Contracts\Session\Session;
use App\Helpers\Checkout\Checkout;
use App\Jobs\SendNewOrderMail;
use \Illuminate\Support\Carbon;

class CheckoutController extends FrontendController
{

    protected $controllerView = 'default.pages.checkout.';
    protected $controllerViewLayout = 'default.layouts.';
    protected $controllerName = 'checkout';
    protected $productModel;
    protected $proviceModel;
    protected $couponModel;
    protected $orderModel;
    protected $shoppingCart;
    protected $shoppingCartInfo;
    protected $shipping;
    protected $SEO;
    protected $timeline;
    protected $momoPay;

    public function __construct(
        ProductRepositoryInterface $productModel,
        ProvinceRepositoryInterface $proviceModel,
        CouponRepositoryInterface $couponModel,
        OrderRepositoryInterface $orderModel,
        ShoppingCart $shoppingCart,
        ShoppingCartInfo $shoppingCartInfo,
        Shipping $shipping,
        Checkout $checkout,
        Timeline $timeline,
        MomoPay $momoPay,
    ) {
        parent::__construct();
        $this->productModel = $productModel;
        $this->proviceModel = $proviceModel;
        $this->couponModel = $couponModel;
        $this->orderModel = $orderModel;
        $this->shoppingCart = $shoppingCart;
        $this->shoppingCartInfo = $shoppingCartInfo;
        $this->shipping = $shipping;
        $this->timeline = $timeline;
        $this->momoPay = $momoPay;
        $this->SEO = new SEO($this->head);
        view()->share(['checkoutHeper' => $checkout]);
    }

    public function cart(Request $request)
    {
        $this->SEO->setMeta(name: "title", content: "Giỏ hàng")
            ->setMeta(name: "keywords", content: "Giỏ hàng")
            ->setMeta(name: "description", content: "Giỏ hàng")
            ->setMetaProperty(property: "og:title", content: "Giỏ hàng")
            ->setMetaProperty(property: "og:keywords", content: "Giỏ hàng")
            ->setMetaProperty(property: "og:description", content: "Giỏ hàng");
        $totalQuantity = $this->shoppingCart->getQuantity();
        if ($totalQuantity <= 0) {
            return redirect()->route("checkout/cart-empty");
        }
        $list_id = $this->shoppingCart->getIDs();
        $listProductInCart = $this->productModel->listItems(['ids' => $list_id], ['task' => 'frontend-check-stocks']);
        $subTotal = $this->shoppingCart->getTotal();
        $listCarts = $this->shoppingCart->getCart();
        //echo "<pre>";print_r($listCarts);die();
        $shoppingCartInfo = $this->shoppingCartInfo->getInfo();
        $shippingPrice = $this->shoppingCartInfo->getShipping();
        $discountCode = $this->shoppingCartInfo->getDiscountCode();
        //$totalWeight = $this->shoppingCart->getWeight();
        //$shippingPrice = $this->shipping->getShippingPrice($shoppingCartInfo, $subTotal, $totalWeight);
        return view($this->controllerView . 'cart', [
            'listCarts' => $listCarts,
            'listProductOutstockInCart' => $listProductInCart,
            'subTotal' => $subTotal,
            'totalQuantity' => $totalQuantity,
            'shoppingCartInfo' => $shoppingCartInfo,
            'shippingPrice' => $shippingPrice,
            'discountCode' => $discountCode,
        ]);
    }

    public function cartEmpty()
    {
        $this->SEO->setMeta(name: "title", content: "Giỏ hàng rỗng")
            ->setMeta(name: "keywords", content: "Giỏ hàng rỗng")
            ->setMeta(name: "description", content: "Giỏ hàng rỗng")
            ->setMetaProperty(property: "og:title", content: "Giỏ hàng rỗng")
            ->setMetaProperty(property: "og:keywords", content: "Giỏ hàng rỗng")
            ->setMetaProperty(property: "og:description", content: "Giỏ hàng rỗng");
        $totalQuantity = $this->shoppingCart->getQuantity();
        if ($totalQuantity > 0) {
            return redirect()->route("checkout/cart");
        }
        return view($this->controllerView . 'cart-empty');
    }

    public function posts(CheckoutPostRequest $request)
    {
        $infoStatus = $this->shoppingCartInfo->customer($request->all());
        if ($infoStatus) {
            $redirect = route("checkout/payment");
            return response()->json(['status' => true, 'redirect' => $redirect]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function shipping(Request $request)
    {
        if (isset($request->city_id) && (isset($request->district_id) || isset($request->ward_id))) {
            $subTotal = $this->shoppingCart->getTotal();
            $totalWeight = $this->shoppingCart->getWeight();
            $shipping = $this->shipping->getShippingPrice($request->all(), $subTotal, $totalWeight);
            $discountCode = $this->shoppingCartInfo->getDiscountCode();
            return response()->json(['status' => true, 'data' => ['shipping' => $shipping, 'subTotal' => $subTotal, 'coupon' => $discountCode]]);
        }
        return response()->json(['status' => false]);
    }

    public function payment(Request $request)
    {
        $this->SEO->setMeta(name: "title", content: "Thông tin thanh toán")
            ->setMeta(name: "keywords", content: "Thông tin thanh toán")
            ->setMeta(name: "description", content: "Thông tin thanh toán")
            ->setMetaProperty(property: "og:title", content: "Thông tin thanh toán")
            ->setMetaProperty(property: "og:keywords", content: "Thông tin thanh toán")
            ->setMetaProperty(property: "og:description", content: "Thông tin thanh toán");
        $totalQuantity = $this->shoppingCart->getQuantity();
        if ($totalQuantity <= 0) {
            return redirect()->route("checkout/cart-empty");
        }
        $list_id = $this->shoppingCart->getIDs();
        $listProductInCart = $this->productModel->listItems(['ids' => $list_id], ['task' => 'frontend-check-stocks']);
        $subTotal = $this->shoppingCart->getTotal();
        $listCarts = $this->shoppingCart->getCart();
        $shoppingCartInfo = $this->shoppingCartInfo->getInfo();
        $shippingPrice = $this->shoppingCartInfo->getShipping();
        $discountCode = $this->shoppingCartInfo->getDiscountCode();
        if (!$shoppingCartInfo || count($listCarts) <= 0) {
            return redirect()->route("checkout/cart");
        }
        $province  = $this->proviceModel->getItem($shoppingCartInfo, ['task' => 'frontent-get-item']);

        $shoppingCartInfo['provice'] = $province->toArray();
        return view($this->controllerView . 'payment', [
            'listCarts' => $listCarts,
            'listProductOutstockInCart' => $listProductInCart,
            'subTotal' => $subTotal,
            'totalQuantity' => $totalQuantity,
            'shippingPrice' => $shippingPrice,
            'infomation' => $shoppingCartInfo,
            'discountCode' => $discountCode
        ]);
    }

    public function order(Request $request)
    {
        $totalQuantity = $this->shoppingCart->getQuantity();
        if ($totalQuantity <= 0) {
            return response()->json(['status' => false, 'message' => 'Cart empty']);
        }
        if (isset($request->payment_method)) {
            $payment_method = $request->payment_method;
            $this->shoppingCartInfo->setPaymentMethod($payment_method);
            $discountCode = $this->shoppingCartInfo->getDiscountCode();
            $shoppingCartInfo = $this->shoppingCartInfo->getData();
            $params = $shoppingCartInfo;
            $params['price_total'] = $this->shoppingCart->getTotal();
            $params['price_discount'] = ($discountCode) ? $discountCode['discount'] : 0;
            $params['coupon_code'] = ($discountCode) ? $discountCode['coupon_info']['coupon_code'] : '';
            $params['order_status'] = 'awaiting';
            $params['shipping_status'] = 'awaiting';
            $params['payment_status'] = 'awaiting';

            $params['shoppingCart'] = $this->shoppingCart->getCart();
            $order_id = $this->orderModel->saveItem($params, ['task' => 'add-item']);
            if ($order_id) {

                Session()->forget('order_id');
                Session()->push('order_id', $order_id);
                
                $invoiceID = (new \App\Helpers\Order\Invoice)->generate($order_id);
                $params['invoice_id'] = $invoiceID;
                $params['order_date'] = Carbon::now()->format("d/m/Y");
               
                //Momo
                if ($payment_method == 'captureMoMoWallet' || $payment_method == 'payWithMoMoATM') {

                    $orderDetail = $this->orderModel->getItem(['id' => $order_id], ['task' => 'get-item']);
                    $order_status = "processed";
                    $timeline = $orderDetail->timeline;
                    $dataTimeLine = json_decode($timeline->comments, true);
                    $order_comment = config("configs.order_status")[$order_status]['comment'];
                    $this->timeline->setData($dataTimeLine)
                        ->createdTimeLine($order_comment, "system");
                    $this->orderModel->saveItem(['id' => $order_id, 'invoice_id' => $invoiceID, 'order_status' => $order_status, 'timeline' => $this->timeline->getData()], ['task' => 'update-invoice']);
                    // Config Momo Request
                    $amount = $orderDetail->price_total + $orderDetail->price_shipping - $orderDetail->price_discount;
                    $this->momoPay->setAmount($amount)
                        ->setOrderId($invoiceID)
                        ->setRequestId((string) time())
                        ->setReturnUrl(url("checkout/success"))
                        ->setNotifyurl(url("checkout/success"))
                        ->setRequestType($payment_method);
                    if ($payment_method == 'payWithMoMoATM') {
                        $this->momoPay->setBankCode("SML");
                    }
                    $response = $this->momoPay->executive();
                    return response()->json(['status' => true, 'momo' => $response]);
                } elseif ($payment_method == 'cash_on_delivery') {
                    $orderDetail = $this->orderModel->getItem(['id' => $order_id], ['task' => 'get-item']);
                    $timeline = $orderDetail->timeline;
                     $order_status = "processed";
                    $dataTimeLine = json_decode($timeline->comments, true);
                    $order_comment = config("configs.order_status")[$order_status]['comment'];
                    $this->timeline->setData($dataTimeLine)
                        ->createdTimeLine($order_comment, "system");
                    $this->orderModel->saveItem(['id' => $order_id, 'invoice_id' => $invoiceID, 'timeline' => $this->timeline->getData()], ['task' => 'update-invoice']);
                    
                    // Send Mail
                    $province = $this->proviceModel->getItem($shoppingCartInfo['customer'], ['task' => 'frontent-get-item']);
                    $totalQuantity = $this->shoppingCart->getQuantity();
                    $subTotal = $this->shoppingCart->getTotal();
                    $shippingPrice = $this->shoppingCartInfo->getShipping();
                    $discountCode = $this->shoppingCartInfo->getDiscountCode();
                    $params['order_message'] = "Đặt hàng thành công";
                    $params['subTotal'] = $subTotal;
                    $params['totalQuantity'] = $totalQuantity;
                    $params['shippingPrice'] = $shippingPrice;
                    $params['discountCode'] = $discountCode;
                    $params['customer']['provice'] = $province->toArray();
                   
                    // Send Mail
                    $emailJob = (new SendNewOrderMail($params))->delay(Carbon::now()->addMinutes(1));
                    dispatch($emailJob);
                    // End Send Mail
                }
                //End Momo


                $redirect = route("checkout/success");
                return response()->json(['status' => true, 'redirect' => $redirect]);
            }
        }
        return response()->json(['status' => false,'mess'=>"payment method error"]);
    }

    public function success(Request $request)
    {

        $meta = "Đặt hàng thành công";
        $shoppingCart = $this->shoppingCart->getCart();
        $shoppingCartInfo = $this->shoppingCartInfo->getData();
        if (!$shoppingCartInfo || count($shoppingCart) <= 0) {
            return redirect()->route("checkout/cart");
        }
        $totalQuantity = $this->shoppingCart->getQuantity();
        $subTotal = $this->shoppingCart->getTotal();
        $shippingPrice = $this->shoppingCartInfo->getShipping();
        $discountCode = $this->shoppingCartInfo->getDiscountCode();
        $province = $this->proviceModel->getItem($shoppingCartInfo['customer'], ['task' => 'frontent-get-item']);
        $shoppingCartInfo['customer']['provice'] = $province->toArray();
        // Param Mail
        $params = $shoppingCartInfo;
        $params['shoppingCart'] = $shoppingCart;
        $params['subTotal'] = $subTotal;
        $params['totalQuantity'] = $totalQuantity;
        $params['shippingPrice'] = $shippingPrice;
        $params['discountCode'] = $discountCode;
        $params['payment_status'] = 'awaiting';
        $params['payment_method'] = $shoppingCartInfo['payment_method'];

        $params['order_date'] = Carbon::now()->format("d/m/Y");
        // Param Mail

        if ($shoppingCartInfo['payment_method'] == 'cash_on_delivery') {
            $payment_status = "success";
        }
        // Update Order 
        $payment_note = "Chưa thanh toán";
        if (isset($request->transId)) {
            $order_id = $request->orderId;
            $params['invoice_id'] = $order_id;
            $orderDetail = $this->orderModel->getItem(['invoice_id' => $order_id], ['task' => 'get-item-invoice']);
            if ($request->errorCode == 0) {
                $payment_status = "success";
                $this->orderModel->updateInventory($shoppingCart, $orderDetail['id']);
                $params['order_message'] = "Đặt hàng thành công";
            } else {
                $payment_status = "cancel";
                $meta = 'Đặt hàng thất bại';
                $params['order_message'] = "Đặt hàng thất bại";
            }
            if ($orderDetail['payment_status'] != 'cancel') {
                $timeline = $orderDetail->timeline;
                $dataTimeLine = json_decode($timeline->comments, true);
                $order_comment = config("configs.payment_status")[$payment_status]['comment'];
                $payment_note = $request->orderInfo;
                $order_comment .= ", " . $request->orderInfo . ", transId: " . $request->transId;
                $this->timeline->setData($dataTimeLine)
                    ->createdTimeLine($order_comment, "system");
                $this->orderModel->saveItem(['id' => $orderDetail->id, 'payment_status' => $payment_status, 'timeline' => $this->timeline->getData()], ['task' => 'update-invoice']);
            }
            // Send Mail
            $emailJob = (new SendNewOrderMail($params))->delay(Carbon::now()->addMinutes(1));
            dispatch($emailJob);
            // End Send Mail
        }
        //Save Payments
        $payment_config = config("configs.payment_method")[$shoppingCartInfo['payment_method']];
        $dataPaymentResult = [
            'order_id' => Session()->get('order_id')[0],
            'payment_code' => $shoppingCartInfo['payment_method'],
            'payment_name' => $payment_config['name'],
            'history' => (count($request->all()) > 0) ? json_encode($request->all()) : null
        ];
        (new \App\Models\OrderPayment())->updateOrCreate(['order_id' => Session()->get('order_id')[0]], $dataPaymentResult);
        $this->SEO->setMeta(name: "title", content: $meta)
            ->setMeta(name: "keywords", content: $meta)
            ->setMeta(name: "description", content: $meta)
            ->setMetaProperty(property: "og:title", content: $meta)
            ->setMetaProperty(property: "og:keywords", content: $meta)
            ->setMetaProperty(property: "og:description", content: $meta);
        // Clear
        $this->shoppingCart->deleteSession();
        if ($payment_status == 'success') {
            if ($discountCode) {
                $this->couponModel->saveItem(['coupon_code' => $discountCode['coupon_info']['coupon_code']], ['task' => 'update-uses']);
            }
            $this->shoppingCartInfo->deleteCoupon();
        }

        // Clear
        return view($this->controllerView . 'success', [
            'listCarts' => $shoppingCart,
            'shoppingCartInfo' => $shoppingCartInfo,
            'subTotal' => $subTotal,
            'totalQuantity' => $totalQuantity,
            'shippingPrice' => $shippingPrice,
            'payment_note' => $payment_note,
            'payment_status' => $payment_status,
            'discountCode' => $discountCode
        ]);
    }
}
