@php
    use App\Helpers\Product\Price as ProductPriceHelper;
    use App\Helpers\Product\Sale as SalePriceHelper;
    use App\Helpers\Product\Quantity as ProductQuantityHelper;
    $index = 0;
@endphp
<div class="block-shopping p-3 bg-white rounded">
    <div class="title my-3">
        Thông tin giỏ hàng
    </div>
    @foreach ($listCarts as $key => $cart)
        @php
            $name = $cart['name'];
            $sku = $cart['sku'];
            $qty = $cart['quantity'];
            $link = url($cart['url']['path']);
            $image = new \App\Helpers\Product\Image();
             $image_src =$image->getLinkDefault($cart, 'small');
            $quantityBox = ProductQuantityHelper::toStrings([
                'min' => 0,
                'data-limit' => $cart['quantity'],
                'value' => $cart['qty'],
                'data-id' => $key,
            ]);
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
        <div class="item-cart pt-3">
            <div class="row">
                <div class="col pe-0">
                    <div class="row">
                        <div class="col-12 col-md-auto pt-1 mb-3">
                            <a href="{{ $link }}">
                                <img src="{{ $image_src }}" alt="{{ $name }}" width="85" />
                            </a>
                        </div>
                        <div class="info col-12 col-md" id="info_{{ $key }}">
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
                            @if ($outStock)
                                <div class="alert alert-danger d-inline-flex mt-1 p-2 px-3 mb-0"><i
                                        class="bi bi-x-circle-fill me-2"></i>Sản phẩm hết hàng</div>
                            @endif
                            {!! SalePriceHelper::saleGiftCheckout($cart, $key) !!}
                        </div>
                    </div>
                </div>
                <div class="col-1"></div>
                <div class="col-4 col-md-auto" id="price_{{ $key }}">
                    {!! ProductPriceHelper::priceCheckoutToString($cart) !!}
                    @if (!$outStock)
                        {!! $quantityBox !!}
                    @endif
                </div>
                <div class="col-12 block-action text-end"><button data-id='{{ $key }}'
                        class="btn btn-delete-cart px-0" type="button"><i
                            class="bi bi-x-circle-fill me-1"></i>Xóa</button></div>
            </div>
        </div>
    @endforeach
</div>
