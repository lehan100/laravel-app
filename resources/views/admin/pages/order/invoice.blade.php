@extends('admin.layouts.default')
@php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Carbon;
    use App\Helpers\Template as Template;
    use App\Helpers\Format as Format;
    use App\Helpers\Product\Price as Price;
    use App\Helpers\Product\Sale;
    $dataTyle = config('configs.location');
    $paymentConfig = config('configs.payment_method');
    $orderStatus = config('configs.order_status');
    $shippingStatus = config('configs.shipping_status');
    $paymentStatus = config('configs.payment_status');
@endphp
@section('title', $metaTitle)
@section('content')
    @if ($invoice)
        {{ html()->form('POST', route("$controllerName/post-shipping"))->attributes([
                'accept-charset' => 'UTF-8',
                'enctype' => 'multipart/form-data',
                'id' => 'appForm',
            ])->open() }}
        @php
            $male = $invoice->gender == 0 ? 'Anh' : 'Chị';
            $name = $male . ' <strong>' . $invoice->name . '</strong>';
            $id = $invoice->id;
            $order_id = sprintf('#%06d', $id);
            $invoice_id = sprintf('#%s', $invoice->invoice_id);
            $order_id = $invoice->viewer == 0 ? '<strong>' . $order_id . '</strong>' : $order_id;
            $phone = Format::formatPhone($invoice->phone);
            $email = $invoice['email'] != '' ? $invoice['email'] : 'Không';
            $address = $invoice['address'];
            $note = $invoice['note'] != '' ? $invoice['note'] : 'Không';
            $createdAt = Carbon::parse($invoice->created_at);
            $created_at = $createdAt->format('d/m/Y h:m:s');
            $city = $invoice->province->name;
            $district = $invoice->district->name;
            $ward = $invoice->ward->name;
            $paymentMothod = $paymentConfig[$invoice->payment_method];
            $order_status = $orderStatus[$invoice->order_status]['name'];
            $order_status_class = $orderStatus[$invoice->order_status]['class'];
            $shipping_status = $shippingStatus[$invoice->shipping_status]['name'];
            $shipping_status_class = $shippingStatus[$invoice->shipping_status]['class'];
            $payment_status = $paymentStatus[$invoice->payment_status]['name'];
            $payment_status_class = $paymentStatus[$invoice->payment_status]['class'];
            $total_qty = 0;
            $timeline = $invoice->timeline;
            // Input
            $inputHiddenID = html()->hidden('id', $id);
        @endphp
        {!! $inputHiddenID !!}
        <div id="header-order">
            <div class="mains">
                <h3 class="title">
                    <div class="row">
                        <div class="col">
                            ĐƠN ĐẶT HÀNG <br><span class="code">(Mã số {!! $order_id !!}, Ngày
                                {{ $created_at }})</span>
                        </div>
                        <div class="title_right col-auto d-print-none">
                            @if (
                                $invoice->order_status != 'success' &&
                                    $invoice->shipping_status != 'success' &&
                                    ($invoice->order_status != 'cancel' && $invoice->shipping_status != 'cancel'))
                                <button type="submit" data-order-id="{{ $id }}" id="btn-invoice"
                                    class="btn btn-success"><i class="fa fa-truck mr-2"></i>Xác nhận giao hàng</button>
                                @if ($invoice->payment_status != 'success')
                                    <button type="button" data-link="{{ route("$controllerName/post-payment") }}"
                                        data-order-id="{{ $id }}" id="btn-payment-success"
                                        class="btn btn-success"><i class="fa fa-credit-card mr-2"></i>Đã thanh toán</button>
                                @endif
                                <button type="button"
                                    data-link="{{ route("$controllerName/post-shipping", ['type' => 'cancel']) }}"
                                    data-order-id="{{ $id }}" id="btn-shipping-cancel" class="btn btn-danger"><i
                                        class="fa fa-close mr-2"></i>Hủy đơn</button>
                            @endif
                            <a href="{{ route('order') }}" class="btn btn-info "><i class="fa fa-mail-reply mr-2"></i>Quay
                                về</a>
                        </div>
                    </div>

                </h3>
            </div>
        </div>
        @include('admin.layouts.elements.messages')
        <div class="row">
            <div class="col col-print-12">
                <div class="x_panel">
                    <div class="x_content">
                        <div class="row">
                            <div class="col">
                                <h2 class="title font-weight-bold mb-3">Thông tin người mua</h2>
                                <p>{!! $name !!}</p>
                                <p><strong>Địa chỉ:</strong> {!! sprintf('%s, %s, %s, %s', $address, $ward, $district, $city) !!}</p>
                                <p><strong>Số điện thoại:</strong> {{ $phone }}</p>
                                <p><strong>Email:</strong> {{ $email }}</p>
                                <p><strong>Ghi chú:</strong> {{ $note }}</p>
                            </div>
                            <div class="col-auto">
                                <p><strong>Invoice ID: </strong>{{ $invoice_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="x_panel">
                      @include('admin.pages.order.blocks.items', ['invoice' => $invoice]);
                </div>
                <div class="x_panel">
                    <div class="x_content">
                        <div class="row">
                            <div class="col-12 col-md-8 col-print-6">
                                <h2 class="title font-weight-bold mb-3">Hình thức thanh toán</h2>
                                <div class="row align-items-center mx-0">
                                    <div class="col-auto px-1">
                                        <img src="{{ asset($paymentMothod['picture']) }}" width="45px"
                                            alt="{{ $paymentMothod['title'] }}" />
                                    </div>
                                    <div class="col ps-1"><strong>{{ $paymentMothod['title'] }}</strong></div>
                                </div>
                            </div>
                            @php
                                $total = Price::format_price($invoice->price_total);
                                $shipping = Price::format_price($invoice->price_shipping);
                                $discount = Price::format_price($invoice->price_discount);
                                $coupon_code =
                                    $invoice->coupon_code != '' ? sprintf('(%s)', $invoice->coupon_code) : '';
                                $subtotal = Price::format_price(
                                    $invoice->price_total + $invoice->price_shipping - $invoice->price_discount,
                                );
                            @endphp
                            <div class="col-12 col-md-4 col-print-6">
                                <h2 class="title font-weight-bold mb-3">Thành tiền</h2>
                                <table class="table table-striped">
                                    <tr>
                                        <td>Tạm tính</td>
                                        <td class="text-right"><strong>{!! $total !!}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Tổng số lượng</td>
                                        <td class="text-right"><strong>{!! $total_qty !!}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Phí vận chuyển</td>
                                        <td class="text-right"><strong>{!! $shipping !!}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Giảm giá {{ $coupon_code }}</td>
                                        <td class="text-right"><strong>{!! $discount !!}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Tổng đơn hàng</td>
                                        <td class="text-right text-danger"><strong>{!! $subtotal !!}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto col-print-12">
                <div class="sticky-top">
                    <div class="x_panel">
                        <div class="x_content">
                            <ul class="list-group list-group-flush">
                                <li class="px-0 list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ml-0 mr-auto">
                                        <div class="font-weight-bold">Trạng thái đơn hàng</div>
                                    </div>
                                    <span class="{{ $order_status_class }}">{!! $order_status !!}</span>
                                </li>
                                <li class="px-0 list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ml-0 mr-auto">
                                        <div class="font-weight-bold">Trạng thái vận đơn</div>
                                    </div>
                                    <span class="{{ $shipping_status_class }}">{!! $shipping_status !!}</span>
                                </li>
                                <li class="px-0 list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ml-0 mr-auto">
                                        <div class="font-weight-bold">Trạng thái thanh toán</div>
                                    </div>
                                    <span class="{{ $payment_status_class }}">{!! $payment_status !!}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ html()->form()->close() }}
        @include('admin.pages.order.blocks.timeline', ['id' => $id, 'timeline' => $timeline]);
    @else
        <p class="alert alert-danger">
            <i class="fa fa-warning mr-2"></i> Đơn hàng không tồn tại
        </p>
    @endif
@endsection
<style>
    #appForm {
        overflow-x: inherit !important;
    }

    .list-options {
        margin-top: 10px;
        font-size: 14px;
        font-style: italic
    }

    .text-warning::before {
        background: #ffc107;
    }

    .text-info::before {
        background: #17a2b8;
    }

    .text-success::before .gender.male {
        background: #28a745;
    }

    .text-danger::before {
        background: #dc3545;
    }

    .order_status:before {
        content: "";
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    #header-order {
        margin: 20px 0;
    }

    .title {
        color: #2084C4;
    }

    #header-order .title {
        border-bottom: 2px solid #FDB817;
        color: #2084C4;
        font-size: 24px;
        font-weight: bold;
        padding-bottom: 5px;
        text-transform: uppercase;
    }

    .code {
        color: #333333;
        display: block;
        font: 12px Arial;
        margin-top: 5px;
        text-transform: none;
    }

    .list-options li {
        margin-bottom: 10px;
    }

    ul.timeline li {
        border: none !important
    }

    .timeline .block {
        margin-left: 0 !important
    }
</style>
