<?php

namespace App\Helpers\Product;

class Image
{
    protected $PRODUCT;
    protected $CONFIGPATH;
    public function __construct()
    {
        $this->CONFIGPATH = config('image.path');
    }
    public function getLinkDefault($product = null, $type = 'small')
    {
        if ($product != null && $product['picture'] != "") {
            $picture = json_decode($product['picture']);
            $image_src = asset(sprintf("%s/%s/%s", $this->CONFIGPATH['product']['path'], $type, $picture[0]));
            return $image_src;
        }
        return  null;
    }
    public function getLink($picture = null, $type = 'small')
    {
        if ($picture != null) {
            $image_src = asset(sprintf("%s/%s/%s", $this->CONFIGPATH['product']['path'], $type, $picture));
            return $image_src;
        }
        return  null;
    }
}
