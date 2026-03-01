<?php

namespace App\Http\Controllers\Default;

use App\Helpers\Product\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\FrontendController;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Services\ShoppingCart;
use App\Services\ShoppingCartInfo;
use App\Services\Shipping;

class CartController extends FrontendController
{

    protected $controllerView = 'default.pages.cart.';
    protected $controllerViewLayout = 'default.layouts.';
    protected $controllerName = 'cart';
    protected $productModel;
    protected $shoppingCart;
    protected $shoppingCartInfo;
    protected $shipping;

    public function __construct(
        ProductRepositoryInterface $productModel,
        ShoppingCart $shoppingCart,
        ShoppingCartInfo $shoppingCartInfo,
        Shipping $shipping
    ) {
        parent::__construct();
        $this->productModel = $productModel;
        $this->shoppingCart = $shoppingCart;
        $this->shoppingCartInfo = $shoppingCartInfo;
        $this->shipping = $shipping;
    }

    public function update(Request $request)
    {
        if ($request->id != "" && $request->qty > 0) {
            $this->shoppingCart->updateQuantity($request->id, $request->qty);
            $htmlPrice = $this->shoppingCart->getSpecialPrice($request->id);
            $listCarts = $this->shoppingCart->getCart();
            $totalQuantity = $this->shoppingCart->getQuantity();
            $subTotal = $this->shoppingCart->getTotal();
            $totalWeight = $this->shoppingCart->getWeight();
            $shoppingCartInfo = $this->shoppingCartInfo->getInfo();
            $shipping = $this->shipping->getShippingPrice($shoppingCartInfo, $subTotal, $totalWeight);
            $discountCode = $this->shoppingCartInfo->getDiscountCode();
            $discountCode = ($discountCode) ? $discountCode ['discount'] : 0;
            $this->shoppingCartInfo->updateShipping($shipping);
            // $miniCart = view($this->controllerView . 'ajax/minicart', [
            //     'listCarts' => $listCarts,
            //     'subTotal' => $subTotal,
            //     'shipping' => $shipping
            // ])->render();
            $gift = \App\Helpers\Product\Sale::saleGiftCheckout($listCarts[$request->id],$request->id);
            return response()->json(['status' => true,'htmlPrice'=>$htmlPrice, 'shipping' => $shipping, 'subTotal' => $subTotal,'discountCode'=>$discountCode, 'quantity' => $totalQuantity,'html_gift'=>$gift]);
        }
        return response()->json(['status' => false]);
    }

    public function delete(Request $request)
    {
        if ($request->id != "") {
            $delete = $this->shoppingCart->delete($request->id);
            if (!$delete) {
                return response()->json(['status' => false]);
            }
            $listCarts = $this->shoppingCart->getCart();
            $totalQuantity = $this->shoppingCart->getQuantity();
            $subTotal = $this->shoppingCart->getTotal();
            $totalWeight = $this->shoppingCart->getWeight();
            $shoppingCartInfo = $this->shoppingCartInfo->getInfo();
            $shipping = $this->shipping->getShippingPrice($shoppingCartInfo, $subTotal, $totalWeight);
            $this->shoppingCartInfo->updateShipping($shipping);
            $miniCart = view($this->controllerView . 'ajax/minicart', [
                'listCarts' => $listCarts,
                'subTotal' => $subTotal,
                'shipping' =>  $shipping
            ])->render();
            $redirect = ($totalQuantity <= 0) ? route("checkout/cart-empty") : null;

            return response()->json(['status' => true, 'shipping' =>  $shipping, 'redirect' => $redirect, 'subTotal' => $subTotal, 'quantity' => $totalQuantity, 'html' => \App\Helpers\Content::minifyContent($miniCart)]);
        }
        return response()->json(['status' => false]);
    }

    public function addCart(Request $request)
    {
        if ($request->id != "") {
            $product = $this->productModel->getItem($request->all(), ['task' => 'item-in-cart']);
            $product->qty = $request->qty;
            $listCarts = $this->shoppingCart->addCart($product->toArray());
            $totalQuantity = $this->shoppingCart->getQuantity();
            $subTotal = $this->shoppingCart->getTotal();
            $miniCart = view($this->controllerView . 'ajax/minicart', [
                'listCarts' => $listCarts,
                'subTotal' => $subTotal
            ])->render();
            $image_src = (new \App\Helpers\Product\Image())->getLinkDefault($product, 'small');
            $item = [
                'name'=>$product['name'],
                'link' => url($product['url']['path']),
                'picture' => $image_src,
                'qty' => $request->qty

            ];
            return response()->json(['status' => true, 'subTotal' => $subTotal, 'quantity' => $totalQuantity, 'item' => $item,'data'=>$listCarts, 'html' => \App\Helpers\Content::minifyContent($miniCart)]);
        }
        return response()->json(['status' => false]);
    }

    public function miniCart(Request $request)
    {
        $listCarts = $this->shoppingCart->getCart();
        $totalQuantity = $this->shoppingCart->getQuantity();
        
        $subTotal = $this->shoppingCart->getTotal();
        
        $listCarts = $this->shoppingCart->getCart();
        $miniCart = view($this->controllerView . 'ajax/minicart', [
            'listCarts' => $listCarts,
            'subTotal' => $subTotal
        ])->render();

        return response()->json(['status' => true, 'subTotal' => $subTotal, 'quantity' => $totalQuantity, 'html' => \App\Helpers\Content::minifyContent($miniCart)]);
    }
}
