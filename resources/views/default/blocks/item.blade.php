@php
    use App\Helpers\Product\Price as ProductPriceHelper;
    use App\Helpers\Product\Sale as SalePriceHelper;
    $name = $product->name;
    $link = url($product->url['path']);
    $image_src = (new \App\Helpers\Product\Image())->getLinkDefault($product, 'medium');
    $ratingObject = new \App\Helpers\Product\Rating();
    if (count($product->ratings) > 0) {
        $ratingObject->setTotalStar($product->ratings[0]->sum_star);
        $ratingObject->setTotalRating($product->ratings[0]->total_rating);
    }
    $gift_sales = SalePriceHelper::sales($product);
   
@endphp
<div class="col item mb-4">
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
            {!! ProductPriceHelper::priceToString($product) !!}
        </div>
    </div>
</div>
