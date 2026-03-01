@php
    use App\Helpers\Product\Price as ProductPriceHelper;
    use App\Helpers\Product\Sale as SalePriceHelper;
    use App\Helpers\Product\Quantity as ProductQuantityHelper;
    $discount = 0;
@endphp
@foreach ($listCarts as $key => $cart)
    @php
        $name = $cart['name'];
        $sku = $cart['sku'];
        $qty = $cart['quantity'];
        $link = url($cart['url']['path']);
        $image = new \App\Helpers\Product\Image();
        $image_src =$image->getLinkDefault($cart, 'small');
    @endphp
    <div class="item-cart py-2">
        <div class="row">
            <div class="info col">
                <p class="name mb-1"><strong>{{ $name }}</strong></p>
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
            </div>
            <div class="col-3 text-end">
                <div class="mb-1"><strong class="text-qty">x {!! $cart['qty'] !!}</strong></div>
                <div class="box-price">
                    <div class="price price-new">
                        {!! ProductPriceHelper::format_price(ProductPriceHelper::getPriceCheckout($cart)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
<hr />
<div class="row align-items-center mb-2">
    <div class="col text-end"><strong>Tổng số lượng:</strong></div>
    <div class="col-4 text-end"><strong>{!! $totalQuantity !!}</strong></div>
</div>
<div class="row align-items-center mb-2">
    <div class="col text-end"><strong>Tổng tiền hàng:</strong></div>
    <div class="col-4 text-end">
        <div class="box-price">
            <div class="price price-new m-0">{!! ProductPriceHelper::format_price($subTotal) !!}</div>
        </div>
    </div>
</div>
<div class="row align-items-center mb-2">
    <div class="col text-end"><strong>Phí vận chuyển:</strong></div>
    <div class="col-4 text-end">
        <div class="box-price">
            <div class="price price-new m-0">{!! ProductPriceHelper::format_price($shippingPrice) !!}</div>
        </div>
    </div>
</div>
@if ($discountCode)
    @php
        $discount = $discountCode['discount'];
    @endphp
    <div class="row align-items-center mb-2">
        <div class="col text-end"><strong>Giảm giá ({{ $discountCode['coupon_info']['coupon_code'] }}):</strong></div>
        <div class="col-4 text-end">
            <div class="box-price">
                <div class="price price-new m-0">- {!! ProductPriceHelper::format_price($discountCode['discount']) !!}</div>
            </div>
        </div>
    </div>
@endif
<div class="row align-items-center mb-2">
    <div class="col text-end"><strong>Tổng đơn hàng:</strong></div>
    <div class="col-4 text-end">
        <div class="box-price">
            <div class="price price-new m-0">{!! ProductPriceHelper::format_price($subTotal + $shippingPrice - $discount) !!}</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col text-end"><strong>Thanh toán:</strong></div>
    <div class="col-4 text-end">{{ $payment_note }}</div>
</div>
