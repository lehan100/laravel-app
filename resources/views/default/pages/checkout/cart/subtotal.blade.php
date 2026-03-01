@php
    use App\Helpers\Product\Price as ProductPriceHelper;
@endphp
<div class="block-subtotal bg-white border rounded p-3">
    <table class="table mb-3">
        <tr>
            <td>Tổng tiền hàng</td>
            <td class="text-end text-danger"><strong id="subtotal">{!! ProductPriceHelper::format_price($subTotal) !!}</strong></td>
        </tr>
        <tr>
            <td>Phí vận chuyển
                <a class="toogle-tooltip" type="button" data-bs-html="true" data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title="Miễn phí vận chuyển cho đơn hàng từ <strong>{!! ProductPriceHelper::format_price($settings['freeshipping_price']) !!}</strong>">
                    <i class="bi bi-question-circle-fill"></i>
                </a>
            </td>
            <td class="text-end"><strong id="shipping">
                    @if ($shippingPrice != 'null')
                        {!! ProductPriceHelper::format_price($shippingPrice) !!}
                    @else
                        Có thể phát sinh
                    @endif
                </strong></td>
        </tr>
        @if ($discountCode)
            <tr id="discount_price">
                <td>Giảm giá ({{$discountCode['coupon_info']['coupon_code']}})</td>
                <td class="text-end text-danger"><strong>- {!! ProductPriceHelper::format_price($discountCode['discount']) !!}</strong></td>
            </tr>
        @endif

        <tr>
            <td>Tổng đơn hàng</td>
            @php
                $totalPurchase = $checkoutHeper->getPurchase();
            @endphp
            <td class="text-end text-danger"><strong id="total" class="price">{!! ProductPriceHelper::format_price($totalPurchase) !!}</strong></td>
        </tr>
        @include('default.pages.checkout.blocks.coupon')
    </table>
    @if (count($listProductOutstockInCart) <= 0)
        <button type="button" @disabled(true)
            class="btn btn-custom hangle-button btn-checkout w-100 py-3">THANH TOÁN</button>
    @endif
</div>
