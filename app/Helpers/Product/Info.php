<?php

namespace App\Helpers\Product;

class Info {

    private $_INSTOCK = "CÒN HÀNG";
    private $_OUTTOCK = "HẾT HÀNG";

    public static function weightToString($weight = 0) {
        if ($weight >= 1000) {
            $caculate = round(($weight / 1000), 2);
            return $caculate . " kg";
        }
        return $weight . " g";
    }

    

    public function getStock($stock = 0) {
        if ($stock == 1) {
            return $this->_INSTOCK;
        }
        return $this->_OUTTOCK;
    }

    public static function toStock($stock = 0) {
        if ($stock == 1) {
            return sprintf('<span class="text-success fw-bold">%s</span>', (new Info())->getStock($stock));
        }
        return sprintf('<span class="text-danger fw-bold">%s</span>', (new Info())->getStock($stock));
    }

    public static function toAttribute($attribute, $space = "") {
        if ($attribute) {
            $label = $attribute->label->name;
            $value = $attribute->value->name;
            return sprintf('<span class="text-secondary" itemprop="brand" itemtype="https://schema.org/Brand" itemscope>%s%s: <strong class="text-info" itemprop="name">%s</strong></span>', $space, $label, $value);
        }
        return "";
    }

    public static function toSku($sku, $space = "") {
        if ($sku != "") {
            return sprintf('<span class="text-secondary">%sSKU:</span> <strong class="text-info" itemprop="sku">%s</strong>', $space, $sku);
        }
    }   
}
