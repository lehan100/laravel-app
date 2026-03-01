<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $configPath = config('image.path.product');

        if ($this['stock'] == 0 || $this['quantity'] == 0) {
            $stock = sprintf('<span class="badge badge-danger">Tạm hết hàng</span>');
        } else {
            $stock = sprintf('<span class="badge badge-success">Còn %s sản phẩm</span><span class="d-none">Còn hàng</span>', $this['quantity']);
        }
        if ($this['status'] == 1) {
            $status = sprintf('<span class="badge badge-success"><i class="fa fa-check mr-1"></i>Kích hoạt</span>');
        } else {
            $status = sprintf('<span class="badge badge-danger"><i class="fa fa-ban mr-1"></i>Tạm ẩn</span>');
        }
        $image_src = '';
        if ($this['picture'] != '') {
            $picture = json_decode($this['picture']);
            $image_src = asset($configPath['path'] . '/small/' . $picture[0]);
        }
        $img = sprintf('<img src="%s" width="80" height="80">', $image_src);
        $price = \App\Helpers\Product\Price::format_price($this['price']);
        $special_price = \App\Helpers\Product\Price::getSpecialPrice($this);
        if ($special_price > 0) {
            $special_price = \App\Helpers\Product\Price::format_price($special_price);
        }
        $category_name = $this['category'][0]['name'];
        $category_id = $this['category'][0]['id'];
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'alias' => $this['alias'],
            'category' => $category_name,
            'category_id' => $category_id,
            'sku' => $this['sku'],
            'price' => $price,
            'final_price' =>$this['price'],
            'special_price' => $special_price,
            'picture' => $img,
            'stock' => $stock,
            'qty' => $this['quantity'],
            'status' => $status
        ];
    }
}
