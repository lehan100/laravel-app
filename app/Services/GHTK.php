<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHTK {

    protected $token;
    protected $urlShipment;
    protected $param;

    public function __construct() {
        $this->urlShipment = 'https://services.giaohangtietkiem.vn/services/shipment/fee';
        $this->param['pick_province'] = "Hồ Chí Minh";
        $this->param['pick_district'] = "quận Thủ Đức";
        $this->param['pick_ward'] = "phường Trường Thọ";
        $this->param['pick_address'] = "38/11/3 đường số 3";
        $this->param['transport'] = "fly";

//        $this->param['deliver_option'] = "xteam";
//        $this->param['tags'] = [1];
    }

    public function setToken($token) {
        $this->token = $token;
        return $this;
    }

    public function setWeight($weight = 0) {
        if ($weight > 0) {
            $this->param['weight'] = $weight;
        }
        return $this;
    }

    public function setValue($value = 0) {
        if ($value > 0) {
            $this->param['value'] = $value;
        }
        return $this;
    }

    public function setShippingProvince($province = "") {
        if ($province != "") {
            $this->param['province'] = $province;
        }
        return $this;
    }

    public function setShippingDistrict($district = "") {
        if ($district != "") {
            $this->param['district'] = $district;
        }
        return $this;
    }

    public function setShippingWard($ward = "") {
        if ($ward != "") {
            $this->param['ward'] = $ward;
        }
        return $this;
    }

    public function setShippingAddress($address = "") {
        if ($address != "") {
            $this->param['address'] = $address;
        }
        return $this;
    }

    public function getShipping() {
        $response = Http::withHeaders(['Token' => $this->token])->withOptions(['verify'=>base_path('cacert.pem')])->get($this->urlShipment, $this->param);
        return json_decode($response, true);
    }
}
