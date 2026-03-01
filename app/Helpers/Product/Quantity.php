<?php

namespace App\Helpers\Product;

class Quantity {

    public static function toStrings($property = null) {
        $xproperty = "";
        if ($property) {
            foreach ($property as $key => $val) {
                $xproperty .= sprintf('%s = %s ', $key, $val);
            }
        }
        $xquantityBox = sprintf('<div class="box-qty d-flex align-items-center border rounded bg-white">');
        $xquantityBox .= sprintf('<span class="control qty-minus"><i class="bi bi-dash"></i></span>');
        $xquantityBox .= sprintf('<input type="number" %s title="Số lượng" id="qty" class="px-1 border-0 form-control qty"/>', $xproperty);
        $xquantityBox .= sprintf('<span class="control qty-plus"><i class="bi bi-plus"></i></span>');
        $xquantityBox .= '</div>';
        return $xquantityBox;
    }
}
