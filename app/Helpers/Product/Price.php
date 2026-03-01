<?php
namespace App\Helpers\Product;

use App\Helpers\Product\Sale;
use \Illuminate\Support\Arr;

class Price
{

    public static function priceToString($param)
    {
        $str = '<div class="box-price row">';
        $str .= self::toPrice($param);
        $str .= '</div>';
        return $str;
    }

    public static function priceSearchToString($param)
    {
        $str = '<div class="box-price row my-2">';
        $str .= self::toPriceSearch($param);
        $str .= '</div>';
        return $str;
    }

    public static function priceMiniCartToString($product)
    {

        $priceBox = self::priceSearchToString($product);
        if (isset($product['options'])) {
            foreach ($product['options'] as $option) {
                $priceOptions = Price::getPrice($option['attributes'][0]);
                if ($priceOptions > 0) {
                    $priceBox = self::priceSearchToString($option['attributes'][0]);
                }
            }
        }
        return str_replace("price price-new col-auto text-start", "price price-new col-12 text-start", $priceBox);
    }

    public static function priceCheckoutToString($product)
    {
        $str        = '<div class="box-price">';
        $strPrice   = self::toPriceCheckout($product);
        $priceFixed = Price::getPriceEntries($product);
        if (isset($product['options'])) {
            foreach ($product['options'] as $option) {
                $priceOptions = Price::getPrice($option['attributes'][0]);
                if ($priceOptions > 0) {
                    if ($priceFixed > 0) {
                        $option['attributes'][0]['price'] += $priceFixed;
                        $option['attributes'][0]['special_price'] += $priceFixed;
                    }
                    $strPrice = self::toPriceCheckout($option['attributes'][0]);
                }
            }
        }

        $str .= $strPrice;
        $str .= '</div>';
        return $str;
    }
    public static function getPriceCheckout($product)
    {
        $price     = Price::getPrice($product);
        $tierPrice = Price::getTierPrice($product, $price);
        if ($tierPrice > 0) {
            $price = $tierPrice;
        }
        if (isset($product['options']) && $product['options'] != "") {
            foreach ($product['options'] as $option) {
                $priceOptions = Price::getPrice($option['attributes'][0]);
                //if ($priceOptions > 0 && $priceOptions < $price) {
                if ($priceOptions > 0) {
                    $price = $priceOptions;
                }
            }
        }
        if (isset($product['option_entries']) && $product['option_entries'] != "") {
            foreach ($product['option_entries'] as $option) {
                $priceOptions = Price::getPrice($option['attributes'][0]);
                //if ($priceOptions > 0 && $priceOptions < $price) {
                if ($priceOptions > 0) {
                    $price += $priceOptions;
                }
            }
        }
        return $price;
    }
    public static function getPriceEntries($param)
    {
        $priceFixed = 0;
        if (isset($param['option_entries'])) {
             if (is_array($param['option_entries']) || $param['option_entries'] instanceof \ArrayAccess) {
                    foreach ($param['option_entries'] as $option_entries) {
                        $price = (int) $option_entries['attributes'][0]['price'];
                        $priceFixed += $price;
                    }
                }
        }
        return $priceFixed;
    }
    public static function getTierPrice($param, $special_price)
    {
        if (isset($param['tier_prices'])) {
            $tier_prices = $param['tier_prices'];
            $data_items  = $tier_prices['tier_price']['items'];
            $qty         = $param['qty'];
            $check       = self::checkTierPrice($param);
            if ($check && count($data_items) > 0 && $tier_prices['tier_price']['status'] == 1) {
                $item = Arr::last($data_items, function ($val) use ($qty) {
                    return $qty >= $val['order_qty'];
                });

                if ($item) {
                    if ($item['type'] == 0) {
                        $price = $item['special_price'];
                    } else {
                        $price = $special_price * ((100 - $item['special_percent']) / 100);
                    }
                    return $price;
                }
            }
        }
        return 0;
    }

    public static function checkTierPrice($param)
    {
        if (isset($param['tier_prices'])) {
            $tier_prices = $param['tier_prices'];
            $startDate   = \Illuminate\Support\Carbon::parse($tier_prices['tier_price']['date_from'])->format('m/d/Y') . " 00:00:00";
            $endDate     = \Illuminate\Support\Carbon::parse($tier_prices['tier_price']['date_to'])->format('m/d/Y') . " 23:59:59";
            $check       = \Illuminate\Support\Carbon::now('Asia/Ho_Chi_Minh')->between($startDate, $endDate);
            return $check;
        }
        return false;
    }
    public static function adminGetAPriceCheckout($product)
    {
        $price = Price::getPrice($product);
        $priceEntries = self::getPriceEntries($product);
        if ($product['gift'] != 'null') {
            $gift = json_decode($product['gift'], true);
            if (isset($gift['special_price']) && $gift['special_price'] > 0 && $gift['special_price'] < $price) {
                $price = $gift['special_price'];
            }
        }
        if (isset($product['options']) && $product['options'] != "") {
            foreach ($product['options'] as $option) {
                $priceOptions = Price::getPrice($option['attributes'][0]);
                if ($priceOptions > 0) {
                    $price = $priceOptions;
                }
            }
        }

        
        return $price + $priceEntries;
    }
    public static function checkTime($param)
    {
        //        if ($param->special_price_from == "") {
        //            $param->special_price_from = \Illuminate\Support\Carbon::now('Asia/Ho_Chi_Minh')->format('m/d/Y');
        //        }
        //        if ($param->special_price_to == "") {
        //            $param->special_price_to = \Illuminate\Support\Carbon::now('Asia/Ho_Chi_Minh')->format('m/d/Y');
        //        }
        $startDate = \Illuminate\Support\Carbon::parse($param['special_price_from'])->format('m/d/Y') . " 00:00:00";
        $endDate   = \Illuminate\Support\Carbon::parse($param['special_price_to'])->format('m/d/Y') . " 23:59:59";
        $check     = \Illuminate\Support\Carbon::now('Asia/Ho_Chi_Minh')->between($startDate, $endDate);
        if ($check && $param['special_price'] && $param['special_price'] > $param['price']) {
            return false;
        }
        return $check;
    }
    public static function getSpecialPrice($param)
    {
        $check = self::checkTime($param);
        if ($param['special_price'] > 0 && $check) {
            return $param['special_price'];
        }
        return 0;
    }
    public static function toPrice($param)
    {
        $str   = "";
        $check = self::checkTime($param);
        $sales = Sale::sales($param);
        if ($sales) {
            $special_price   = Sale::getSaleSpecialPrice($sales, $param['price']);
            $special_percent = Sale::getSalePercent($sales, $param['price']);
            $special_percent = round($special_percent) . '<i class="bi bi-percent"></i>';
            $str .= sprintf('<div class="price price-new col col-md-12 pe-1 text-start">%s</div>', self::format_price($special_price));
            $str .= sprintf('<div class="price price-old pe-1 col-auto text-start d-none d-md-block">%s</div>', self::format_price($param['price']));
            $str .= sprintf('<div class="col-auto text-start ps-1"><span class="sale-pecent">-%s</span></div>', $special_percent);
        } elseif ($param['special_price'] > 0 && $check) {
            $str .= sprintf('<div class="price price-new col col-md-12 pe-1 text-start">%s</div>', self::format_price($param['special_price']));
            $str .= sprintf('<div class="price price-old pe-1 col-auto text-start d-none d-md-block">%s</div>', self::format_price($param['price']));
            $str .= sprintf('<div class="col-auto text-start ps-1"><span class="sale-pecent">%s</span></div>', self::get_sale_off($param));
        } else {
            $str .= sprintf('<div class="price price-new col text-start">%s</div>', self::format_price($param['price']));
        }
        return $str;
    }

    public static function toPriceCheckout($param)
    {
        $str          = "";
        $copy         = $param;
        $check        = self::checkTime($param);
        $sales        = Sale::sales($param);
        $price        = Price::getPrice($param);
        $tierPrice    = Price::getTierPrice($param, $price); // check Tier Price
        $priceEntries = self::getPriceEntries($param);
        $param['price'] += $priceEntries;
        $param['special_price'] += $priceEntries;

        if ($sales) {
            $special_price = Sale::getSaleSpecialPrice($sales, $param['price']);
            if ($param['special_price'] > 0 && $param['special_price'] < $special_price) {
                $special_price = $param['special_price'];
            }
            if ($special_price > $tierPrice && $tierPrice > 0) {
                $special_price = $tierPrice;
            }
            $str .= sprintf('<div class="price price-new">%s</div>', self::format_price($special_price));
            $str .= sprintf('<div class="price price-old">%s</div>', self::format_price($param['price']));
        } else if ($tierPrice > 0) {
            $str .= sprintf('<div class="price price-new">%s</div>', self::format_price($tierPrice));
            $str .= sprintf('<div class="price price-old">%s</div>', self::format_price($param['price']));
        } else if ($param['special_price'] > 0 && $check) {
            $str .= sprintf('<div class="price price-new">%s</div>', self::format_price($param['special_price']));
            $str .= sprintf('<div class="price price-old">%s</div>', self::format_price($param['price']));
        } else {
            $str .= sprintf('<div class="price price-new col text-start">%s</div>', self::format_price($param['price']));
        }

        return $str;
    }

    public static function toPriceSearch($param)
    {
        $str   = "";
        $check = self::checkTime($param);
        $sales = Sale::sales($param);

        if ($sales) {
            $special_price   = Sale::getSaleSpecialPrice($sales, $param['price']);
            $special_percent = Sale::getSalePercent($sales, $param['price']);

            if ($param['special_price'] > 0 && $param['special_price'] < $special_price) {
                $special_price   = $param['special_price'];
                $special_percent = self::get_sale_off_value($param);
            }
            $special_percent = round($special_percent) . '<i class="bi bi-percent"></i>';
            $str .= sprintf('<div class="price price-new col-auto text-start">%s</div>', self::format_price($special_price));
            $str .= sprintf('<div class="price price-old pe-0 col-auto text-start d-none d-md-block">%s</div>', self::format_price($param['price']));
            $str .= sprintf('<div class="col-auto text-start"><span class="sale-pecent">-%s</span></div>', $special_percent);
        } elseif ($param['special_price'] > 0 && $check) {
            $str .= sprintf('<div class="price price-new col-auto text-start">%s</div>', self::format_price($param['special_price']));
            $str .= sprintf('<div class="price price-old pe-0 col-auto text-start d-none d-md-block">%s</div>', self::format_price($param['price']));
            $str .= sprintf('<div class="col-auto text-start"><span class="sale-pecent">%s</span></div>', self::get_sale_off($param));
        } else {
            $str .= sprintf('<div class="price price-new col text-start">%s</div>', self::format_price($param['price']));
        }
        return $str;
    }

    public static function getPrice($param)
    {
        $check = self::checkTime($param);

        $sales        = Sale::sales($param);

        if ($sales) {
            $special_price = Sale::getSaleSpecialPrice($sales, $param['price']);

            if ($param['special_price'] > 0 && $param['special_price'] < $special_price) {
                $special_price = $param['special_price'];
            }
            return $special_price;
        } elseif ($param['special_price'] > 0 && $check) {
            return $param['special_price'];
        } else {
            return $param['price'];
        }
    }

    public static function get_sale_off($arrParam)
    {
        $percent  = (($arrParam['price'] - $arrParam['special_price']) / $arrParam['price']) * 100;
        $str_sale = round($percent) . '<i class="bi bi-percent"></i>';
        return "-" . $str_sale;
    }
    public static function get_sale_off_value($arrParam)
    {
        $percent = (($arrParam['price'] - $arrParam['special_price']) / $arrParam['price']) * 100;
        return round($percent);
    }
    public static function format_price($price)
    {
        $currency = number_format($price, 0, '', '.');
        return $currency . "&nbsp;₫";
    }

    public static function toPriceBox($param)
    {
        $xhtml = sprintf('<div id="box-price" class="box-price row mb-4 mt-5" itemprop="offers" itemscope itemtype="https://schema.org/Offer">');
        $xhtml .= self::toPriceInline($param, 'col-auto');
        $xhtml .= sprintf('<meta itemprop="price" content="%s">', $param->price);
        $xhtml .= sprintf('<meta itemprop="priceCurrency" content="VND">');

        if ($param['updated_at'] != '') {
            $xhtml .= sprintf('<meta itemprop="priceValidUntil" content="%s">', \Illuminate\Support\Carbon::parse($param['updated_at'])->format("Y-m-d"));
        } else {
            $xhtml .= sprintf('<meta itemprop="priceValidUntil" content="%s">', \Illuminate\Support\Carbon::parse($param['created_at'])->format("Y-m-d"));
        }
        if ($param->stock == 1) {
            $xhtml .= sprintf('<meta itemprop="availability" content="https://schema.org/InStock">');
        } else {
            $xhtml .= sprintf('<meta itemprop="availability" content="https://schema.org/OutStock">');
        }
        $xhtml .= "</div>";
        $xhtml .= sprintf('<script>var dataPrice = %s;</script>', self::getDataBind($param));

        return $xhtml;
    }

    public static function toPriceInline($param, $col = "col")
    {
        $str   = "";
        $check = self::checkTime($param);
        $sales = Sale::sales($param);
        if ($sales) {
            $special_price   = Sale::getSaleSpecialPrice($sales, $param['price']);
            $special_percent = Sale::getSalePercent($sales, $param['price']);

            if ($param['special_price'] > 0 && $param['special_price'] < $special_price) {
                $special_price   = $param['special_price'];
                $special_percent = self::get_sale_off_value($param);
            }
            $special_percent_text = round($special_percent) . '<i class="bi bi-percent"></i>';
            $str .= sprintf('<div class="price price-new col-12 col-md-auto text-start">%s</div>', self::format_price($special_price));
            $str .= sprintf('<div class="price price-old pe-0 %s text-end">%s</div>', $col, self::format_price($param['price']));
            $str .= sprintf('<div class="col-auto text-end"><span class="sale-pecent">-%s</span></div>', $special_percent_text);
            // $str .= sprintf('<script>var dataPrice = {price:%s,special_price:%s}</script>', $param['price'], $special_price);
        } elseif ($param['special_price'] > 0 && $check != false) {
            $str .= sprintf('<div class="price price-new col-12 col-md-auto text-start">%s</div>', self::format_price($param['special_price']));
            $str .= sprintf('<div class="price price-old pe-0 %s text-end">%s</div>', $col, self::format_price($param['price']));
            $str .= sprintf('<div class="col-auto text-end"><span class="sale-pecent">%s</span></div>', self::get_sale_off($param));
            //$str .= sprintf('<script>var dataPrice = {price:%s,special_price:%s}</script>', $param['price'], $param['special_price']);
        } else {
            $str .= sprintf('<div class="price price-new col text-start">%s</div>', self::format_price($param['price']));
            //$str .= sprintf('<script>var dataPrice = {price:%s}</script>', $param['price']);
        }
        return $str;
    }

    public static function getDataBind($param)
    {
        $check = self::checkTime($param);
        $sales = Sale::sales($param);
        if ($sales) {
            $special_price   = Sale::getSaleSpecialPrice($sales, $param['price']);
            $special_percent = $sales['special_percent'];
            if ($param['special_price'] > 0 && $param['special_price'] < $special_price) {
                $special_price   = $param['special_price'];
                $special_percent = self::get_sale_off_value($param);
            }
            $data = sprintf('{price:%s,special_price:%s,special_percent:%s}', $param['price'], $special_price, $special_percent);
        } else if ($param['special_price'] > 0 && $check) {
            $data = sprintf('{price:%s,special_price:%s}', $param['price'], $param['special_price']);
        } else {
            $data = sprintf('{price:%s}', $param['price']);
        }
        return $data;
    }
}
