@php
    use App\Helpers\Product\Price as ProductPriceHelper;
    use App\Helpers\Product\Sale as SalePriceHelper;
    use App\Helpers\Product\Quantity as ProductQuantityHelper;
    $index = 0;
@endphp
<div class="block-shopping p-3 bg-white rounded mb-4">
    <div class="title mb-4 mt-2">
        Thông tin đơn hàng
    </div>
    @foreach ($listCarts as $key => $cart)
        @php
            $name = $cart['name'];
            $sku = $cart['sku'];
            $qty = $cart['quantity'];
            $link = url($cart['url']['path']);
            $image = new \App\Helpers\Product\Image();
             $image_src =$image->getLinkDefault($cart, 'small');
            $outStock = false;
            if (count($listProductOutstockInCart) > 0) {
                $stock = $listProductOutstockInCart->filter(function ($d) use ($cart) {
                    return $d['id'] == $cart['id'];
                });
                if (count($stock) > 0) {
                    $outStock = true;
                }
            }
        @endphp
        <div class="item-cart pt-3 pb-2">
            <div class="row align-items-center">
                <div class="col-auto pt-1">
                    <a href="{{ $link }}">
                        <img src="{{ $image_src }}" alt="{{ $name }}" width="85" />
                    </a>
                </div>
                <div class="info col px-0">
                    <p class="name"> <a href="{{ $link }}">{{ $name }}</a></p>
                    @if ($sku != '')
                        <p class="text-secondary mb-1"><i>#{{ $sku }}</i></p>
                    @endif
                    @if (
                        (isset($cart['options']) && count($cart['options']) > 0) ||
                            (isset($cart['option_entries']) && count($cart['option_entries']) > 0))
                        <ul class="list-options">
                            @if (isset($cart['option_entries']) && count($cart['option_entries']) > 0)
                                @foreach ($cart['option_entries'] as $option)
                                    @php
                                        $title = $option['title'];
                                        $titleValue = $option['attributes'][0]['title'];
                                    @endphp
                                    <li class="option-item"><b>{{ $title }}: </b>{{ $titleValue }}</li>
                                @endforeach
                            @endif
                            @if (isset($cart['options']) && count($cart['options']) > 0)
                                @foreach ($cart['options'] as $option)
                                    @php
                                        $title = $option['title'];
                                        $titleValue = $option['attributes'][0]['title'];
                                    @endphp
                                    <li class="option-item"><b>{{ $title }}: </b>{{ $titleValue }}
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    @endif
                    {!! SalePriceHelper::saleGiftCheckout($cart, $key) !!}
                    @if ($outStock)
                        <div class="alert alert-danger d-inline-flex mt-1 p-2 px-3 mb-0"><i
                                class="bi bi-x-circle-fill me-2"></i>Sản phẩm hết hàng</div>
                    @endif
                </div>
                <div class="col-1 d-none d-md-block"></div>
                <div class="col-12 col-md-auto text-end">
                    @if (!$outStock)
                        <div class="mb-1"><strong class="text-qty">x {!! $cart['qty'] !!}</strong></div>
                    @endif
                    <div class="box-price">
                        <div class="price price-new">
                            {!! ProductPriceHelper::format_price(ProductPriceHelper::getPriceCheckout($cart)) !!}
                        </div>
                    </div>
                </div>
                <div class="col-12 block-action d-none"><button data-id='{{ $key }}'
                        class="btn btn-delete-cart px-0" type="button"><i
                            class="bi bi-x-circle-fill me-1"></i>Xóa</button></div>
            </div>
        </div>
    @endforeach
</div>
