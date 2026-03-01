@php
use App\Helpers\Product\Price as ProductPriceHelper;
$result = 0;
@endphp
@if(count($dataSearch)>0)
<ul>
    @foreach($dataSearch as  $product)
    @php
        if(++$result>9) break;
        $name = $product->name;
        $link = url($product->url['path']);
        $image_src = (new \App\Helpers\Product\Image())->getLinkDefault($product, 'large');
        $priceBox = ProductPriceHelper::priceSearchToString($product);
    @endphp
    <li class="p-2 border-bottom">
        <div class="row align-items-center">
            <div class="img col-auto"><img src="{{$image_src}}" alt="{{$name}}" width="60px"></div>
            <div class="detail text-left col pl-0">
                <div class="name"><a href="{{$link}}">{{$name}}</a></div>
                {!!$priceBox!!}
            </div>
        </div>
    </li>
    @endforeach
    <li class="viewAll"><a class="link-search" href="{{route('product/search',['keyword'=>$keyword])}}">Xem tất cả {{count($dataSearch)}} sản phẩm</a></li>
</ul>
@else
<div class="px-2"><p class="text-danger p-2 my-2">Không tìm thấy dữ liệu!</p></div>
@endif