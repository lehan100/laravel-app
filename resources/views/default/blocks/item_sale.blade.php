@php
    use App\Helpers\Product\Price as ProductPriceHelper;
    use App\Helpers\Product\Sale as SalePriceHelper;
    $quantity_is_uses_product = $item_sale->quantity_is_uses_product;
    $order_qty = $item_sale->order_qty;
@endphp
@if ($order_qty < $quantity_is_uses_product)
    @php
        $product = $item_sale->product;
        $name = $product->name;
        $pecent = round(($quantity_is_uses_product-$order_qty) / $quantity_is_uses_product,3) * 100;
        $link = url($product->url['path']);
        $image_src = (new \App\Helpers\Product\Image())->getLinkDefault($product, 'medium');
        $ratingObject = new \App\Helpers\Product\Rating();
        if (count($product->ratings) > 0) {
            $ratingObject->setTotalStar($product->ratings[0]->sum_star);
            $ratingObject->setTotalRating($product->ratings[0]->total_rating);
        }
        $param = $item_sale->toArray();
        $gift_sales = SalePriceHelper::sale(['sale' => $param, 'price' => $product->price]);
    @endphp
    <div class="sale-items col item">
        <div class="card p-0 h-100">
            <div class="thumbnail position-relative mm-ani">
                <div class="warning"><span class="loading"></span></div>
                <a href="{{ $link }}">
                    <img src="/media/1x.jpg" data-img="{{ $image_src }}" alt="{{ $name }}" />
                </a>
            </div>
            <div class="card-body caption p-2">
                <h2 class="name"><a href="{{ $link }}">{{ $name }}</a></h2>
                {!! SalePriceHelper::saleGiftSmall($gift_sales) !!}
            </div>
            <div class="card-footer border-0 bg-transparent p-2 pt-0">
                <!--<div class="text-center"><button class="add-to-cart mt-0" data="591" max="100">Chọn mua</button></div>-->
                @if (count($product->ratings) > 0)
                    <div class="text-warning mb-1 small">{!! $ratingObject->ratingToStringProduct() !!}</div>
                @endif
                <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="{{$pecent}}"
                    aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar text-bg-warning" style="width: {{$pecent}}%">Còn {{$quantity_is_uses_product- $order_qty}} sản phẩm</div>
                </div>
                {!! ProductPriceHelper::priceToString($product) !!}
            </div>
        </div>
    </div>
@endif
