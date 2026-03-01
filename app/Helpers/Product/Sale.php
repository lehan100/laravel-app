<?php

namespace App\Helpers\Product;

use \Illuminate\Support\Arr;

class Sale
{
    public static function sales($param)
    {

        if (isset($param['sales']) && count($param['sales']) > 0) {
            $sale_percent  = 0;
            foreach ($param['sales'] as $sale) {
                if (($sale['quantity_is_uses_product'] - $sale['order_qty']) > 0) {
                    $getSalePercent = self::getSalePercent($sale, $param['price']);
                    if ($sale['buy_qty'] > 0 && $sale['gift_qty'] > 0 && $sale['gift_sku'] != "") {
                        return $sale;
                    } elseif ($getSalePercent > $sale_percent &&  $getSalePercent > 0) {
                        $sale_percent = $getSalePercent;
                        $data_sale = $sale;
                    }
                    return $data_sale;
                }
            }
        } elseif (isset($param['id']) && isset($param['buy_qty'])) {
            return $param;
        }
        return null;
    }
    public static function sale($param)
    {
        if (isset($param['sale']) && count($param['sale']) > 0) {
            $sale_percent  = 0;
            $sale = $param['sale'];
            if (($sale['quantity_is_uses_product'] - $sale['order_qty']) > 0) {
                $getSalePercent = self::getSalePercent($sale, $param['price']);
                if ($sale['buy_qty'] > 0 && $sale['gift_qty'] > 0 && $sale['gift_sku'] != "") {
                    return $sale;
                } elseif ($getSalePercent > $sale_percent &&  $getSalePercent > 0) {
                    $sale_percent = $getSalePercent;
                    $data_sale = $sale;
                }
                return $data_sale;
            }
            return null;
        } elseif (isset($param['id']) && isset($param['buy_qty'])) {
            return $param;
        }
        return null;
    }

    public static function saleGift($sale, $tier_price_data = [])
    {
        $xhtml = "";
        if ($sale != null && $sale['buy_qty'] > 0 && $sale['gift_qty'] > 0 && $sale['gift_sku'] != "" && $sale['gift_sku_info'] != "" || $tier_price_data) {

            // $xhtml .= '<div class="box-gift alert alert-success pb-0">';
            $xhtml .= '<div class="box-gift card mt-5">';
            $xhtml .= '<div class="card-header py-3"><i class="bi bi-gift me-2"></i> Khuyến mãi</div>';
            $xhtml .= '<div class="card-body">';
            if ($sale != null && $sale['buy_qty'] > 0 && $sale['gift_qty'] > 0 && $sale['gift_sku'] != "" && $sale['gift_sku_info'] != "") {
                $gift_sku_info = json_decode($sale['gift_sku_info'], true);
                $xhtml .= '<div class="row item-sale">';
                $xhtml .= '<div class="col-auto pe-1"><span class="dinamic"></span></div>';
                $xhtml .= '<div class="col">';
                $xhtml .= sprintf('<h6 class="mb-3 text-danger"><strong>Mua %s tặng quà</strong></h6>', $sale['buy_qty']);
                $xhtml .= sprintf('<input type="hidden" value="%s" id="sale_id">', $sale['id']);
                $xhtml .= '<div class="p-2 alert alert-warning">';
                foreach ($gift_sku_info as $item) {
                    $xhtml .= '<div class="row align-items-center">';
                    $picture = (new \App\Helpers\Product\Image())->getLink($item['picture']);
                    $xhtml .= sprintf('<div class="col-auto pe-0"><img src="%s" alt="%s" width="50" class="me-2"/></div>', $picture, $item['name']);
                    $xhtml .= sprintf('<div class="col ps-2"><div><span class="badge badge-danger">Quà tặng</span></div><strong>%s</strong></div>', $item['name']);
                    $xhtml .= '</div>';
                }
                $xhtml .= '</div>';
                $xhtml .= '</div>';
                $xhtml .= '</div>';
            }
            $xhtml .= self::tierPrice($tier_price_data);
            $xhtml .= '</div>';
            $xhtml .= '</div>';
        }
        return $xhtml;
    }
    public static function saleGiftSmall($sale)
    {
        $xhtml = "";
        if ($sale != null && $sale['buy_qty'] > 0 && $sale['gift_qty'] > 0 && $sale['gift_sku'] != "" && $sale['gift_sku_info'] != "") {
            $gift_sku_info = json_decode($sale['gift_sku_info'], true);
            $xhtml .= '<a href="javascript:;">';
            $xhtml .= '<div class="box-gift alert alert-success rounded p-1 mb-0">';
            $xhtml .= '<div class="row align-items-center">';
            $xhtml .= sprintf('<div class="col-12 col-xl-auto pe-0 order-1 order-xl-0">');
            foreach ($gift_sku_info as $item) {
                $picture = (new \App\Helpers\Product\Image())->getLink($item['picture']);
                $xhtml .= sprintf('<img src="%s" alt="%s" width="20" class="me-2"/>', $picture, $item['name']);
            }
            $xhtml .= '</div>';
            $xhtml .= sprintf('<div class="col ps-1 text-start order-0 order-xl-1"><strong>Mua %s tặng quà</strong></div>', $sale['buy_qty']);
            $xhtml .= '</div>';
            $xhtml .= '</div>';
            $xhtml .= '</a>';
        }
        return $xhtml;
    }
    public static function saleGiftCheckout($cart, $id, $xhtml_full = true)
    {
        $xhtml = "";
        $alert_status = FALSE;
        if (isset($cart['sales'][0]) || isset($cart['tier_prices']) && $xhtml_full) {
            if (isset($cart['sales'][0])) {
                $sale = $cart['sales'][0];
                if ($sale != null && $sale['buy_qty'] > 0 && floor($cart['qty'] / $sale['buy_qty']) > 0 && $sale['gift_qty'] > 0 && $sale['gift_sku'] != "" && $sale['gift_sku_info'] != "") {
                    $alert_status = TRUE;
                }
            }
            if (isset($cart['tier_prices'])) {
                $qty = $cart['qty'];
                $data_items = $cart['tier_prices']['tier_price']['items'];
                $item = Arr::last($data_items, function ($val) use ($qty) {
                    return $qty >= $val['order_qty'];
                });
                if ($item) {
                    $alert_status = TRUE;
                }
            }
            if ($alert_status) {
                $xhtml .= sprintf("<div id='gift_%s' class='alert alert-success rounded p-2 pb-0'>", $id);
            }
        }
        if (isset($cart['tier_prices'])) {
            $data_items = $cart['tier_prices']['tier_price']['items'];
            //die("111");
            $qty = $cart['qty'];
            $item = Arr::last($data_items, function ($val) use ($qty) {
                return $qty >= $val['order_qty'];
            });
            if ($item) {
                if ($item['type'] == 0) {
                    $xhtml .= sprintf('<div class="mb-2"><span class="badge badge-danger">Giảm giá</span> Giảm còn <strong>%s</strong> khi mua từ <strong>%s</strong> sản phẩm.</div>', Sale::binText(0, $item['special_price']), $item['order_qty']);
                } else {
                    $xhtml .= sprintf('<div class="mb-2"><span class="badge badge-danger">Giảm giá</span> Giảm <strong>%s</strong> khi mua từ <strong>%s</strong> sản phẩm.</div>', Sale::binText($item['special_percent']), $item['order_qty']);
                }
            }
        }
        if (isset($cart['sales'][0])) {
            $sale = $cart['sales'][0];
            if ($sale != null && $sale['buy_qty'] > 0 && floor($cart['qty'] / $sale['buy_qty']) > 0 && $sale['gift_qty'] > 0 && $sale['gift_sku'] != "" && $sale['gift_sku_info'] != "") {
                $gift_sku_info = json_decode($sale['gift_sku_info'], true);
                $qty = floor($cart['qty'] / $sale['buy_qty']) * $sale['gift_qty'];
                foreach ($gift_sku_info as $item) {
                    $xhtml .= sprintf('<div class="mb-2"><span class="badge badge-danger">Quà tặng</span> <strong>%s x %s</strong> | #%s</div>', $item['name'], $qty, $item['sku']);
                }
            }
        }
        if ($alert_status) {
            $xhtml .= '</div>';
        }
        return $xhtml;
    }
    public static function saleGiftMail($cart)
    {
        $xhtml = "";
        if (isset($cart['sales'][0])) {
            $sale = $cart['sales'][0];
            if ($sale != null && $sale['buy_qty'] > 0 && floor($cart['qty'] / $sale['buy_qty']) > 0 && $sale['gift_qty'] > 0 && $sale['gift_sku'] != "" && $sale['gift_sku_info'] != "") {
                $xhtml .= '<div style="border:1px solid #ddd; padding: 10px 10px 0 10px;background-color: #eee">';
                $xhtml .= '<p style="margin: 0 0 10px 0; color: red"><strong>Quà tặng</strong></p>';
                $gift_sku_info = json_decode($sale['gift_sku_info'], true);
                $qty = floor($cart['qty'] / $sale['buy_qty']) * $sale['gift_qty'];
                foreach ($gift_sku_info as $item) {
                    $xhtml .= sprintf('<p style="margin: 0 0 10px 0"><strong>%s</strong></p>', $item['name']);
                    $xhtml .= sprintf('<p style="margin: 0 0 10px 0">SKU: %s | Số lượng: %s</p>', $item['sku'], $qty);
                }
                $xhtml .= '</div>';
            }
        }
        return $xhtml;
    }
    public static function saleGiftCheckoutData($cart)
    {
        if (isset($cart['sales'][0]) || isset($cart['tier_prices'])) {
            $data = [];
            if (isset($cart['sales'][0])) {
                $sale = $cart['sales'][0];
                if ($sale != null) {
                    $special_price = self::getSaleSpecialPrice($sale, $cart['price']);
                    $data = [
                        'special_price' => $special_price,
                        'info' => $sale['info'],
                    ];
                    if ($sale['buy_qty'] > 0 && floor($cart['qty'] / $sale['buy_qty']) > 0 && $sale['gift_qty'] > 0 && $sale['gift_sku'] != "" && $sale['gift_sku_info'] != "") {
                        $gift_sku_info = json_decode($sale['gift_sku_info'], true);
                        $qty = floor($cart['qty'] / $sale['buy_qty']) * $sale['gift_qty'];

                        //$dateFrom = self::get_sales_from($cart);
                        //$dateTo = self::get_sales_to($cart);
                        $data['qty'] = $qty;
                        $data['gift_items'] = $gift_sku_info;
                        // $data = [
                        //     'qty' => $qty,
                        //     'special_price' => $special_price,
                        //     'special_price_from' => $dateFrom,
                        //     'special_price_to' => $dateTo,
                        //     'info' => $sale['info'],
                        //     'gift_items' => $gift_sku_info
                        // ];

                    }
                }
            }
            if (isset($cart['tier_prices'])) {
                $price = Price::getPrice($cart);
                $special_price = Price::getTierPrice($cart, $price);
                if (isset($data['special_price']) && $data['special_price'] > $special_price) {
                    $data['special_price'] = $special_price;
                } else if (!isset($data['special_price'])) {
                    $data['special_price'] = $special_price;
                }
                $data['tier_prices'] = $cart['tier_prices'];
            }
            return $data;
        }
        return null;
    }
    public static function get_sales_from($cart)
    {
        if (isset($cart['sales'][0])) {
            $sale = $cart['sales'][0];
            $date_from = $sale['info']['date_from'];
            return $date_from;
        }
        return null;
    }
    public static function get_sales_to($cart)
    {
        if (isset($cart['sales'][0])) {
            $sale = $cart['sales'][0];
            $date_to = $sale['info']['date_to'];
            return $date_to;
        }
        return null;
    }
    public static function get_sales_id($cart)
    {
        if (isset($cart['sales'][0])) {
            $sale = $cart['sales'][0];
            $product_sales_id = $sale['product_sales_id'];
            return $product_sales_id;
        }
        return 0;
    }
    public static function getSaleSpecialPrice($sale, $price)
    {
        
        if ($sale['special_percent'] > 0) {
            $special_price = $price * ((100 - $sale['special_percent']) / 100);
            return $special_price;
        } elseif ($sale['special_price'] > 0) {
            return $sale['special_price'];
        } else {
            return 0;
        }
    }
    public static function getSalePercent($sale, $price)
    {

        if ($sale['special_percent'] > 0) {
            return $sale['special_percent'];
        }
        if ($sale['special_price'] > 0) {
            $percent = ($price - $sale['special_price']) / $price;
            return $percent * 100;
        }
        return 0;
    }
    public static function tierPrice($tier_price_data)
    {
        $xhtml = "";
        if ($tier_price_data) {
            $data_items = $tier_price_data->tier_price->items;
            if (count($data_items) > 0) {
                $xhtml .= '<div class="row item-sale">';
                $xhtml .= '<div class="col-auto pe-1"><span class="dinamic"></span></div>';
                $xhtml .= '<div class="col">';
                $xhtml .= '<h6 class="mb-3 text-danger"><strong>Mua nhiều giảm giá</strong></h6>';



                $xhtml .= '<div class="p-2 alert alert-warning">';
                $xhtml .= '<ul class="tier_prices">';
                foreach ($data_items as $item) {
                    $type = $item->type;
                    if ($type == 1) {
                        if ($item['special_percent'] < 10) {
                            $xhtml .= sprintf('<li data-buy="%s">Giảm <strong>%s</strong> khi mua từ <strong>%s &nbsp;</strong> sản phẩm.</li>', $item['order_qty'], self::binText($item['special_percent']), $item['order_qty']);
                        } else {
                            $xhtml .= sprintf('<li data-buy="%s">Giảm <strong>%s</strong> khi mua từ <strong>%s</strong> sản phẩm.</li>', $item['order_qty'], self::binText($item['special_percent']), $item['order_qty']);
                        }
                    } else if ($type == 0) {
                        $xhtml .= sprintf('<li data-buy="%s">Giảm còn <strong>%s</strong> khi mua từ <strong>%s</strong> sản phẩm.</li>', $item['order_qty'], self::binText(0, $item['special_price']), $item['order_qty']);
                    }
                }
                $xhtml .= '</ul>';
                $xhtml .= '</div>';
                $xhtml .= '</div>';
                $xhtml .= '</div>';
            }
        }
        return $xhtml;
    }
    public static function binText($percent = 0, $price = 0)
    {
        if ($percent > 0) {
            return $percent . "%";
        }
        if ($price > 0) {
            return Price::format_price($price);
        }
    }
}
