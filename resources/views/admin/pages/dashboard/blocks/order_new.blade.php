@php
    use App\Helpers\Product\Price;
@endphp
@if (count($listNewOrder) > 0)
    <div class="x_panel border-warning order-new" style="background:rgb(255 193 7 / 30%)">
        <div class="x_title border-0">
            <h2>Đơn hàng mới</h2>
        </div>
        <div class="x_content">
            <table class="table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Số lượng</th>
                        <th class="text-right">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listNewOrder as $order)
                        @php
                            $id = $order->id;
                            $name = $order->name;
                            $total_item = $order->items[0]->total ?? 0;
                            $price = Price::format_price($order->price_total);
                            $linkView = route('order/view', ['id' => $id]);
                        @endphp
                        <tr class="onclick cursor-pointer" data-link='{{ $linkView }}' title='{{ $linkView }}'>
                            <td>{{ $name }}</td>
                            <td>{{ $total_item }}</td>
                            <td class="text-right">{!! $price !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
