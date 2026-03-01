<?php
/*
namespace App\Helpers;

class Product {

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
            return sprintf('<span class="text-success fw-bold">%s</span>', (new Product())->getStock($stock));
        }
        return sprintf('<span class="text-danger fw-bold">%s</span>', (new Product())->getStock($stock));
    }

    public static function toAttribute($attribute, $space = "") {
        if ($attribute) {
            $label = $attribute->label->name;
            $value = $attribute->value->name;
            return sprintf('<span class="text-secondary">%s%s: <strong class="text-success">%s</strong></span>', $space, $label, $value);
        }
        return "";
    }

    public static function toSku($sku, $space = "") {
        if ($sku != "") {
            return sprintf('<span class="text-secondary">%sMã sản phẩm:</span> %s', $space, $sku);
        }
    }


    public static function toOptions($options) {
        if (count($options) > 0) {
            $xhtml = '<div class="option-group">';
            foreach ($options as $option) {
                $xhtml .= '<div class="row align-items-center">';
                $xhtml .= sprintf('<div class="col-auto fw-bold">%s</div>', $option->title);
                $xhtml .= sprintf('<div class="col">111</div>');
                $xhtml .= '</div>';
            }
            $xhtml .= '</div>';
            return $xhtml;
        }
        return null;
    }
}
