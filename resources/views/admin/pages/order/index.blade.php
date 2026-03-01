@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Format as Format;
    use App\Helpers\Product\Price as Price;
    use Illuminate\Support\Carbon;
    $dataTyle = config('configs.location');
    $paymentMethod = config('configs.payment_method');
    $orderStatus = config('configs.order_status');
    $shippingStatus = config('configs.shipping_status');
    $paymentStatus = config('configs.payment_status');
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title', [
        'title' => 'Danh sách đơn hàng',
    ])
<div class="x_panel p-0 border-0">
        {{-- @include('admin.templates.x_title', [
            'metaTitle' => 'Tất cả đơn hàng',
        ]) --}}
        <div class="x_content p-0 m-0">
            @include ('admin.pages.order.toolbar.filter')
            @if (count($items) > 0)
                @include ('admin.templates.notify')
                {{ html()->form('POST', '')->attributes([
                'accept-charset' => 'UTF-8',
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal form-label-left',
                'id' => 'appForm',
            ])->open() }}
                <table class="table table-striped jambo_table ">
                    <thead>
                        <tr>
                            @include('admin.templates.thead.check_all')
                            @include('admin.templates.thead.column', [
                                'name' => 'Order ID',
                                'width' => '100',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => 'Date',
                            ])
                            @include('admin.templates.thead.column', ['name' => 'Fullname'])
                            @include('admin.templates.thead.column', ['name' => 'Phone', 'width' => '130'])
                            @include('admin.templates.thead.column', [
                                'name' => 'Province',
                                'class' => 'd-none',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => 'Total Purchases',
                                'width' => '150',
                                'class' => 'text-center',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => 'Payment Method',
                                'width' => '160',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => 'Order Status',
                                'width' => '160',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => 'Shipping Status',
                                'width' => '160',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => 'Payment Status',
                                'width' => '260',
                            ])
                            @include('admin.templates.thead.action', [
                                'name' => 'Action',
                                'width' => '100',
                            ])
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $val)
                            @php
                                $male = $val->gender == 0 ? 'male' : 'female';
                                //$name = "<span class='gender " . $male . "'></span><strong>" . $val->name . '</strong>';
                                $name = sprintf("<strong>%s</strong>",$val->name);
                                $id = $val->id;
                                $order_id = sprintf('%06d', $id);
                                $order_id = $val->viewer == 0 ? '<strong>' . $order_id . '</strong>' : $order_id;
                                $phone = Format::formatPhone($val->phone);
                                $address = $val['address'];
                                $createdAt = Carbon::parse($val->created_at);
                                $created_at = $createdAt->format('d/m/Y h:m:s');
                                $city = $dataTyle['province'][$val->province->type] . ' ' . $val->province->name;
                                
                                //$district = $dataTyle['district'][$val->district->type] . ' ' . $val->district->name;
                                //$ward = $dataTyle['ward'][$val->ward->type] . ' ' . $val->ward->name;
                                $subtotal = Price::format_price($val->price_total + $val->price_shipping - $val->price_discount);
                                $payment_method = $paymentMethod[$val->payment_method]['name'];
                                $order_status = $orderStatus[$val->order_status]['name'];
                                $order_status_class = $orderStatus[$val->order_status]['class'];
                                $shipping_status = $shippingStatus[$val->shipping_status]['name'];
                                $shipping_status_class = $shippingStatus[$val->shipping_status]['class'];
                                $payment_status = $paymentStatus[$val->payment_status]['name'];
                                $payment_status_class = $paymentStatus[$val->payment_status]['class'];
                                $linkView = route($controllerName . '/view', ['id' => $id]);
                            @endphp
                            <tr class="dblclick" data-link='{{ $linkView }}'>
                                <td><input type="checkbox" @disabled($val->order_status=='success' || $val->shipping_status =='success' || $val->payment_status =='cancel' || $val->order_status=='cancel' || $val->shipping_status =='cancel' || $val->payment_status =='cancel') name="aid[]" value="{{ $id }}"></td>
                                <td>{!! $order_id !!}</td>
                                <td>{{ $created_at }}</td>
                                <td class="text-nowrap">{!! $name !!}</td>
                                <td>{{ $phone }}</td>
                                <td class="d-none"> {{ $city }}</td>
                                <td class="text-danger"><strong>{!! $subtotal !!}</strong></td>
                                <td >{{ $payment_method }}</td>
                                <td><span class="{{ $order_status_class }}">{!! $order_status !!}</span></td>
                                <td><span class="{{ $shipping_status_class }}">{!! $shipping_status !!}</span></td>
                                <td><span class="{{ $payment_status_class }}" style="margin-left: 0!important">{!! $payment_status !!}</span></td>
                                <td class="text-center"><a href="{{ $linkView }}" class="btn btn-info btn-xs">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ html()->form()->close() }}
                @include('pagination.pagination_admin')
            @else
                @include('admin.templates.list_empty')
            @endif
        </div>
    </div>
@endsection
<style>
    .text-warning::before {
        background: #ffc107;
    }

    .text-info::before {
        background: #17a2b8;
    }

    .text-success::before,
    .gender.male {
        background: #28a745;
    }

    .text-danger::before,
    .gender.female {
        background: #dc3545;
    }

    .order_status:before,
    .gender {
        content: "";
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
</style>
