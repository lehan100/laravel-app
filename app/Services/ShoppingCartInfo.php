<?php

namespace App\Services;

use Illuminate\Session\SessionManager;
use App\Services\Shipping;
use App\Services\ShoppingCart;
use Exception;

class ShoppingCartInfo
{

    protected $session;
    protected $instance;
    private $shipping;
    private $shoppingCart;


    public function __construct(SessionManager $session, ShoppingCart $shoppingCart, Shipping $shipping)
    {
        $this->session = $session;
        $this->shipping = $shipping;
        $this->shoppingCart = $shoppingCart;

        $this->setSessionKey();
    }

    public function setSessionKey($instance = 'shoppingCartInfo')
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

    public function getInfo()
    {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            $data = $this->getData();
            return isset($data['customer']) ? $data['customer'] : null;
        }
        return null;
    }

    public function getShipping()
    {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            $data = $this->getData();
            return isset($data['shipping']) ? $data['shipping'] : null;
        }
        return null;
    }

    public function getTotal(){
        $total = $this->shoppingCart->getTotal();
        return $total;
    }

    public function getData()
    {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            $data = $this->session->get($keySession);
            $totalWeight = $this->shoppingCart->getWeight();
            $subtotal = $this->shoppingCart->getTotal();
            $shipping = isset($data['customer']) ? $this->shipping->getShippingPrice($data['customer'], $subtotal, $totalWeight) : null;
            if (isset($data['shipping']) && $data['shipping'] != $shipping) {
                $data['shipping'] = $shipping;
                $this->session->put($keySession, $data);
            }
            return $data;
        }
        return null;
    }

    public function updateShipping($shipping = 0)
    {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            $data = $this->getData();
            $data['shipping'] = $shipping;
            $this->session->put($keySession, $data);
            return true;
        }
        return false;
    }


    public function setPaymentMethod($payment = "")
    {
        if ($payment != "") {
            $keySession = $this->getSessionKey();
            if ($this->session->has($keySession)) {
                $data = $this->getData();
                $data['payment_method'] = $payment;
                $this->session->put($keySession, $data);
                return true;
            }
            return false;
        }
    }
    public function setDiscountCode($coupon_info = null, $discount_amount = 0)
    {
        if ($coupon_info != null && $discount_amount > 0) {
            $keySession = $this->getSessionKey();
            if ($this->session->has($keySession)) {
                $data = $this->getData();
            }
            $data['coupon'] = [
                'coupon_info' => $coupon_info,
                'discount' => $discount_amount
            ];
            $this->session->put($keySession, $data);
            return true;
        }
        return false;
    }
    public function getDiscountCode()
    {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            $data = $this->getData();
            if (isset($data['coupon'])) {
                return $data['coupon'];
            }
        }
        return false;
    }
    public function deleteCoupon()
    {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            $data = $this->getData();
            if (isset($data['coupon'])) {
               unset($data['coupon']);
               $this->session->put($keySession, $data);
            }
        }
        return false;
    }

    public function customer($param)
    {
        try {
            $keySession = $this->getSessionKey();
            $totalWeight = $this->shoppingCart->getWeight();
            $subtotal = $this->shoppingCart->getTotal();
            if ($this->session->has($keySession)) {
                $infomation = $this->getData();
            }
            $infomation['customer'] = [
                'gender' => $param['gender'],
                'name' => $param['name'],
                'phone' => $param['phone'],
                'email' => $param['email'],
                'note' => $param['note'],
                'address' => $param['address'],
                'city_id' => $param['city_id'],
                'district_id' => $param['district_id'],
                'ward_id' => $param['ward_id'],
            ];
            $infomation['shipping'] =  $this->shipping->getShippingPrice($param, $subtotal, $totalWeight);
            // $infomation = [
            //     'customer' => [
            //         'gender' => $param['gender'],
            //         'name' => $param['name'],
            //         'phone' => $param['phone'],
            //         'email' => $param['email'],
            //         'note' => $param['note'],
            //         'address' => $param['address'],
            //         'city_id' => $param['city_id'],
            //         'district_id' => $param['district_id'],
            //         'ward_id' => $param['ward_id'],
            //     ],
            //     'shipping' => $this->shipping->getShippingPrice($param, $subtotal, $totalWeight),
            // ];
            $this->session->put($keySession, $infomation);
            return true;
        } catch (Exception $exc) {
            return false;
        }
    }

    public function setViewer($product_id = "")
    {
        if ($product_id != "") {
            $keySession = $this->getSessionKey();
            if ($this->session->has($keySession)) {
                $data = $this->getData();
                echo "<pre>";
                print_r($data);
                $data['product_viewer'] = $product_id;
                $this->session->put($keySession, $data);
                return true;
            }
            return false;
        }
    }
}
