@php
    use Illuminate\Support\Arr;
    use App\Helpers\Template as Template;
    use App\Helpers\Format as Format;
    use App\Helpers\Product\Price as Price;
    use App\Helpers\Product\Sale;
    use Illuminate\Support\Carbon;
@endphp
<div class="x_content">
    <h2 class="title font-weight-bold mb-3">Thông tin sản phẩm</h2>
    <div class=" overflow-scroll">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th class=" text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $product)
                    @php
                        $name = $product->name;
                        $sku = $product->sku;
                        $qty = $product->qty;
                        $options = $product['options'] =
                            $product['options'] != '' ? json_decode($product['options'], true) : '';
                        $option_entries = $product['option_entries'] =
                            $product['option_entries'] != '' ? json_decode($product['option_entries'], true) : '';
                        $price = Price::adminGetAPriceCheckout($product);
                        $priceBox = Price::format_price($price);
                        $subtotalBox = Price::format_price($price * $qty);
                        $total_qty += $qty;
                        $gift = $product->gift;
                    @endphp

                    <tr>
                        <td>
                            <p><strong>{{ $name }}</strong></p>
                            <div>#{{ $sku }}</div>
                            @if ($options != '' || $option_entries != '')
                                <ul class="list-options">
                                    @if ($option_entries != '')
                                    @foreach ($option_entries as $option)
                                        @php
                                            $title = $option['title'];
                                            $titleValue = $option['attributes'][0]['title'];
                                            $priceFixed = (int) $option['attributes'][0]['price'];
                                            $textFix =
                                                $priceFixed > 0
                                                    ? sprintf(
                                                        ' <i class="text-danger">(+%s)</i>',
                                                        Price::format_price($priceFixed),
                                                    )
                                                    : '';
                                        @endphp
                                        <li class="option-item"><strong>{{ $title }}:</strong>
                                            {!! $titleValue . $textFix !!}
                                        </li>
                                    @endforeach
                                    @endif
                                    @if ($options != '')
                                    @foreach ($options as $option)
                                        @php
                                            $title = $option['title'];
                                            $titleValue = $option['attributes'][0]['title'];
                                        @endphp
                                        <li class="option-item"><strong>{{ $title }}:</strong>
                                            {{ $titleValue }}
                                        </li>
                                    @endforeach
                                    @endif
                                </ul>
                            @endif
                            @if ($gift != 'null')
                                @php
                                    $gift = json_decode($gift, true);
                                    $tierPriceHTML = '';
                                    if (isset($gift['tier_prices'])) {
                                        $tier_price = $gift['tier_prices'];
                                        $data_items = $tier_price['tier_price']['items'];
                                        $item = Arr::last($data_items, function ($val) use ($qty) {
                                            return $qty >= $val['order_qty'];
                                        });
                                        if ($item) {
                                            if ($item['type'] == 0) {
                                                $tierPriceHTML .= sprintf(
                                                    '<div class="text-success bg-transparent border-0 mt-2 pl-0 font-italic font-small"><span class="badge badge-danger">Giảm giá</span> Giảm còn <strong>%s</strong> khi mua từ <strong>%s</strong> sản phẩm.</div>',
                                                    Sale::binText(0, $item['special_price']),
                                                    $item['order_qty'],
                                                );
                                            } else {
                                                $tierPriceHTML .= sprintf(
                                                    '<div class="text-success bg-transparent border-0 mt-2 pl-0 font-italic font-small"><span class="badge badge-danger">Giảm giá</span> Giảm <strong>%s</strong> khi mua từ <strong>%s</strong> sản phẩm.</div>',
                                                    Sale::binText($item['special_percent']),
                                                    $item['order_qty'],
                                                );
                                            }
                                        }
                                    }
                                @endphp
                                @if (isset($gift['qty']))
                                    @php
                                        $qty_gift = $gift['qty'];
                                    @endphp
                                    <div class="text-success bg-transparent border-0 mt-2 pl-0 font-italic font-small">
                                        @foreach ($gift['gift_items'] as $gift_item)
                                            <div class="mb-2"><span class="badge badge-danger">Quà
                                                    tặng</span>
                                                <strong>{{ $gift_item['name'] }} x
                                                    {{ $qty_gift }}</strong> |
                                                #{{ $gift_item['sku'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                {!! $tierPriceHTML !!}
                            @endif

                        </td>
                        <td class="text-danger"><strong>{!! $priceBox !!}</strong></td>
                        <td>{!! $qty !!}</td>
                        <td class="text-danger text-right"><strong>{!! $subtotalBox !!}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
