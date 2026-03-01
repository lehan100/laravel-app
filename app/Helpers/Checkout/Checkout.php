<?php

namespace App\Helpers\Checkout;

use App\Services\ShoppingCartInfo;

class Checkout
{
    protected $shoppingCartInfo;
    public function __construct(ShoppingCartInfo $shoppingCartInfo)
    {
        $this->shoppingCartInfo = $shoppingCartInfo;
    }

    public function getPurchase()
    {
        $cartTotal = $this->shoppingCartInfo->getTotal();
        $shippingPrice = $this->shoppingCartInfo->getShipping();
        $discountCode = $this->shoppingCartInfo->getDiscountCode();
        $shippingPrice = $shippingPrice != null ? $shippingPrice : 0;
        $discount = $discountCode ? $discountCode['discount'] : 0;
        return $cartTotal + $shippingPrice - $discount;
    }
}
