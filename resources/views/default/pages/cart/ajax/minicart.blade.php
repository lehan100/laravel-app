@php
    use App\Helpers\Product\Price as ProductPriceHelper;
    $total = ProductPriceHelper::format_price($subTotal);
@endphp
@if (count($listCarts) > 0)
    <div id="mCustomScrollbar" class="mCustomScrollbar" style="max-height: 380px;">
        <ul>
            @foreach ($listCarts as $cart)
                @php
                    $name = $cart['name'];
                    $qty = $cart['qty'];
                    $link = url($cart['url']['path']);
                   $image = new \App\Helpers\Product\Image();
                    $image_src =$image->getLinkDefault($cart, 'small');

                @endphp
                <li>
                    <div class="row">
                        <div class="col-3 img mm-img pe-0">
                            <a href="{{ $link }}">
                                <img src="/media/1x.jpg" data-src="{{ $image_src }}" class="img-thumbnail"
                                    alt="{{ $name }}" width="100%" />
                            </a>
                        </div>
                        <div class="col">
                            <div class="cart">
                                <p class="name"> <a href="{{ $link }}">{{ $name }}</a></p>
                                <p class="qty">x {{ $qty }}</p>
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
                                                <li class="option-item"><b>{{ $title }}: </b>{{ $titleValue }}
                                                </li>
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
                                {!! ProductPriceHelper::priceMiniCartToString($cart) !!}
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="total">Tổng đơn hàng:<span class="ms-2">{!! $total !!}</span></div>
    <div class="checkout"><a href="{{ route('checkout/cart') }}" class="btn btn-custom w-100">GIỎ HÀNG</a></div>
@else
    <p class="text-danger p-2 text-center m-0"> <i class="bi bi-exclamation-triangle-fill me-2"></i>Chưa có sản phẩm!
    </p>
@endif
