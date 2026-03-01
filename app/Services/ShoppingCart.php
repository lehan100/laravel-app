<?php

namespace App\Services;

use Illuminate\Session\SessionManager;
use \Illuminate\Support\Arr;
use App\Helpers\Product\Price;

class ShoppingCart
{

    protected $session;
    protected $instance;

    public function __construct(SessionManager $session)
    {
        $this->session = $session;
        $this->setSessionKey();
    }

    public function setSessionKey($instance = 'shoppingCart')
    {
        $this->instance = $instance;
    }

    public function getSessionKey()
    {
        return $this->instance;
    }

    public function deleteSession()
    {
        $this->session->forget($this->instance);
    }

    public function getProductKey($product)
    {
        $key = [];
        $key[] = $product['id'];
        if (isset($product['options']) && count($product['options']) > 0) {
            foreach ($product['options'] as $option) {
                $key[] = $option['id'];
                $key[] = $option['attributes'][0]['id'];
            }
        }
         if (isset($product['option_entries']) && count($product['option_entries']) > 0) {
            foreach ($product['option_entries'] as $option) {
                $key[] = $option['id'];
                $key[] = $option['attributes'][0]['id'];
            }
        }
        return md5(Arr::join($key, "_"));
    }

    public function getCart()
    {
        $keySession = $this->getSessionKey();
        // echo "<pre>" ;print_r($this->session->get($keySession));die();
        if ($this->session->has($keySession)) {
            return $this->session->get($keySession);
        }
        return [];
    }

    public function getTotal()
    {
        $total = 0;
        $carts = $this->getCart();

        foreach ($carts as $cart) {
            $price = Price::getPrice($cart);
            $tierPrice = Price::getTierPrice($cart,$price);
            if($tierPrice > 0){
                $price = $tierPrice;
            }
           
            if (isset($cart['options'])) {
                foreach ($cart['options'] as $option) {
                    $priceOptions = Price::getPrice($option['attributes'][0]);

                    if ($priceOptions > 0) {
                        $price = $priceOptions;
                    }
                }
            }
            $priceFixed = Price::getPriceEntries($cart);
            $total += ($price +  $priceFixed) * $cart['qty'];
        }
        return $total;
    }
    
    public function getQuantity()
    {
        $qty = 0;
        $carts = $this->getCart();
        if (count($carts) > 0) {
            foreach ($carts as $cart) {
                $qty += $cart['qty'];
            }
        }
        return $qty;
    }

    public function getWeight()
    {
        $weight = 0;
        $carts = $this->getCart();
        if (count($carts) > 0) {
            foreach ($carts as $cart) {
                $weight += $cart['weight'];
            }
        }
        return $weight;
    }

    public function updateQuantity($id = "", $qty = 0)
    {
        if ($id != "" && $qty > 0) {
            $keySession = $this->getSessionKey();
            $carts = $this->getCart();
            if (isset($carts[$id])) {
                $carts[$id]['qty'] = $qty;
            }
            $this->session->put($keySession, $carts);
            return true;
        }
        return false;
    }
    public function getSpecialPrice($id = "")
    {
        if ($id != "") {
            $carts = $this->getCart();
            $item = $carts[$id];
            $html = Price::priceCheckoutToString($item);
            return $html;
        }
        return 0;
    }

    public function getIDs()
    {
        $carts = $this->getCart();
        $id =  Arr::pluck(Arr::where($carts, function ($val) {
            return $val['id'] > 0;
        }), 'id');
        // $id = [];
        // foreach ($carts as $cart) {
        //     $id[] = $cart['id'];
        // }
        return $id;
    }

    public function delete($id = "")
    {
        if ($id != "") {
            $keySession = $this->getSessionKey();
            $carts = $this->getCart();
            if (isset($carts[$id])) {
                unset($carts[$id]);
                $this->session->put($keySession, $carts);
                return true;
            }
        }
        return false;
    }

    public function addCart($product)
    {
        //        $this->deleteSession();
        $keySession = $this->getSessionKey();
        $carts = $this->getCart();
        if ($product) {
            $productKey = $this->getProductKey($product);
            if (isset($carts[$productKey])) {
                $carts[$productKey]['qty'] += $product['qty'];
            } else {
                $product['qty'] = $product['qty'] ?? 1;
                $carts[$productKey] = $product;
            }
            $this->session->put($keySession, $carts);
        }
        return $carts;
    }
}
