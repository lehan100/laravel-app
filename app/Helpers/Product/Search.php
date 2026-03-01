<?php

namespace App\Helpers\Product;

use Illuminate\Support\Str;

class Search {

    private $_PRODUCTS;

    public function __construct($products) {
        $this->_PRODUCTS = $products;
    }

    public function search($keyword = "") {
        $data = [];
        if ($keyword != "") {
            $data = $this->_PRODUCTS->filter(function ($d) use ($keyword) {
                $keyword = Str::lower($keyword);
                $name = Str::lower($d['name']);
                $name_ascii = Str::lower($d['name_ascii']);
                $sku = Str::lower($d['sku']);
                return Str::contains($name, $keyword) || Str::contains($sku, $keyword) || Str::contains($name_ascii, $keyword);
            });
        }
        return $data;
    }
}
