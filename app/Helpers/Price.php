<?php

namespace App\Helpers;

class Price {

    public static function formatPrice($price, $class = null) {
        $currency = number_format($price, 0, '', '.');
        $price_format = $currency . "&nbsp;₫";
        if ($class != "") {
            $price_format = sprintf('<span class="%s">%s</span>', $class, $price_format);
        }
        return $price_format;
    }

    public static function formatNumber($price = 0) {
        $price_format = number_format($price, 0, '', '.');
        return $price_format;
    }

    public static function getPrice($price) {
        return str_replace('.', '', $price);
    }

}
