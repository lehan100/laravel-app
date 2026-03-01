@extends('default.layouts.1-column')
@section('content')
    @php
        use App\Helpers\Product\Info as ProductHelper;
        use App\Helpers\Product\Price as ProductPriceHelper;
        use App\Helpers\Product\Sale as SalePriceHelper;
        use App\Helpers\Product\Option as ProductOptionHelper;
        use App\Helpers\Product\OptionEntries as ProductOptionEntriesHelper;
        use App\Helpers\Product\Quantity as ProductQuantityHelper;
        $IMAGE = new \App\Helpers\Product\Image();
        $id = $item->id;
        $productName = $item->name;
        $stockStatus = $item->stock;
        $quantity = $item->quantity;
        $sortContent = $item->contents->sort_content;
        $longContent = $item->contents->content;
        $picture = json_decode($item->picture, true);
        $image_src = $IMAGE->getLinkDefault($item, 'large');
        $image_Zoom = $IMAGE->getLinkDefault($item, 'large');
        $stock = ProductHelper::toStock($item->stock);
        $brand = count($item->attibute_sets) > 0 ? ProductHelper::toAttribute($item->attibute_sets[0]) : '';
        $sku = $brand!= '' ? ProductHelper::toSku($item->sku, '&nbsp;| '):ProductHelper::toSku($item->sku, '');
        $priceBox = ProductPriceHelper::toPriceBox($item);
        $options = ProductOptionHelper::toStrings($item->options);
        $optionsEntries = ProductOptionEntriesHelper::toStrings($item->option_entries);
        $quantityBox = ProductQuantityHelper::toStrings(['min' => 0, 'data-limit' => $item->quantity, 'value' => 1]);
        $gift_sales = SalePriceHelper::sales($item);
        $tier_price_data = $item['tier_prices'];
    @endphp
    <div class="block-breadcrumb py-2">
        <div class="container">
            {!! $breadcrumbs !!}
        </div>
    </div>
    <div class="page-product-view container my-4" itemtype="http://schema.org/Product" itemscope="itemscope">
        <div class="row">
            <div class="col-12 col-md-6 product-media mb-4">
                <a id="asyncZoom" itemprop="image" class="MagicZoom "href="{{ $image_Zoom }}"
                    data-options="zoomMode: on; hint: off; rightClick: true; selectorTrigger: hover; expandCaption: false; history: false;">
                    <img src="{{ $image_src }}" alt="{{ $productName }}">
                </a>
                @if (count($picture) > 1)
                    <div class="row mt-3 justify-content-center justify-content-md-start galaxy-thumb">
                        @foreach ($picture as $key => $val)
                            @php
                                $thumb = $IMAGE->getLink($val, 'small');
                                $thumbZoom = $IMAGE->getLink($val, 'large');
                            @endphp
                            <div class="item col-auto col-md-3 my-2">
                                <a data-zoom-id="asyncZoom" data-image="{{ $thumbZoom }}" href="{{ $thumbZoom }}"><img
                                        class="border rounded" alt="{{ $productName }}" src="{{ $thumb }}"></a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-12 col-md-6 product-info mb-4 ps-md-5">
                <h1 class="product-name mb-2" itemprop="name">{{ $productName }}</h1>
				<meta itemprop="description" content="{{$description}}" />
                <p class="text-warning">{!! $ratingObject->ratingToStringInfo() !!}</p>
                <p class="mb-4 pb-1 border-bottom">{!! $brand . $sku !!}</p>
                {!! $priceBox !!}
                {!! $optionsEntries !!}
                {!! $options !!}
                {!! SalePriceHelper::saleGift($gift_sales,$tier_price_data) !!}
                @if ($stockStatus != 0 && $quantity > 0)
                    <form id="product_addtocart_form" method="POST" action="{{ route('cart/add-cart') }}">
                        <input type="hidden" value="{{ csrf_token() }}" id="tokenAddToCart" />
                        <div class="row align-items-center mt-4">
                            <div class="col-12 fw-bold my-1">Số lượng</div>
                            <div class="col-auto">{!! $quantityBox !!}</div>
                        </div>
                        <div class="row align-items-center mt-4">
                            <div class="col-12 col-md my-2">
                                <button type="button" @disabled(true) data-id="{{ $id }}"
                                    id="btn-order-checkout" class="btn-order hangle-button btn w-100">Mua ngay</button>
                            </div>
                            <div class="col-12 col-md my-2">
                                <button type="button" @disabled(true) data-id="{{ $id }}"
                                    id="btn-add-cart" class="btn-add-cart hangle-button btn btn-outline-info w-100"><i
                                        class="bi bi-cart2 me-3"></i>Chọn Mua</button>
                            </div>
                        </div>
                    </form>
                @else
                    <button type="button" class="btn-outstock btn-danger btn text-uppercase mt-4">Tạm hết hàng</button>
                @endif
                @include('default.pages.product.blocks.policy')
            </div>
        </div>
        @if ($longContent != '' && $sortContent != '')
            <div class="row product-infomation my-4">
                @if ($longContent != '')
                    <div class="col-12 col-md-8 order-2 order-md-0">
                        <h3 class="title mb-3">Thông tin sản phẩm</h3>
                        <div class="content">{!! $longContent !!}</div>
                    </div>
                @endif
                @if ($sortContent != '')
                    <div class="sort_content col-12 col-md-4 order-1 order-md-2">
                        <div class="sticky-top">
                            <h3 class="title mb-3">Thông số kỹ thuật</h3>
                            <div class="content">{!! $sortContent !!}</div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
        @include('default.pages.product.blocks.rating')
        @if (count($otherProducts) > 0)
            <div class="list-products related-products">
                <h3 class="title mb-3">Sản phẩm tương tự</h3>
                <div class="row row-cols-2 row-cols-sm-4 row-cols-lg-5">
                    @foreach ($otherProducts as $product)
                        @include('default.blocks.item', ['product' => $product])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
@section('styles')
    <link href="{{ asset('default/magiczoom/magiczoomplus.css') }}" rel="stylesheet" />
    @if (count($listRatings) > 0)
        <link href="{{ asset('default/css/fancybox.css') }}" rel="stylesheet" />
    @endif
    <style>
        body {
            background: #fff;
        }

        .fancybox__caption {
            width: 100%;
        }
    </style>
@endsection
@section('script')
    <script>
        var dataCounter = {
            url: "{{ route('product/viewer') }}",
            'id': {{ $id }}
        };
    </script>
    @if (count($listRatings) > 0)
        <script defer="defer" async="async" type="text/javascript" src="{{ asset('default/js/fancybox.umd.js') }}"></script>
    @endif
    <script defer="defer" async="async" type="text/javascript" src="{{ asset('default/js/product.js') }}"></script>
    <script type="text/javascript" src="{{ asset('default/magiczoom/magiczoomplus.js') }}"></script>
@endsection
