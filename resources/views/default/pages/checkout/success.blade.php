@php
    use App\Helpers\Format as Format;
@endphp
@extends('default.layouts.1-column')
@section('content')
    @php
        $infomation = $shoppingCartInfo['customer'];
        $dataTyle = config('configs.location');
        $male = $infomation['gender'] == 0 ? 'Anh' : 'Chị';
        $name = $infomation['name'];
        $email = $infomation['email'];
        $phone = Format::formatPhone($infomation['phone']);
        $note = $infomation['note'];
        $address = $infomation['address'];
        $city = $infomation['provice']['name'];
        $district = $infomation['provice']['district_one']['name'];
        $ward = $infomation['provice']['district_one']['ward']['name'];
        /*Payment*/
        $paymentConfig = config('configs.payment_method');
        $paymentMothod = $paymentConfig[$shoppingCartInfo['payment_method']];
    @endphp
    <div class="block-checkout container">
        <div class="row align-items-center justify-content-center">
            <div class="col-12 col-md-6">
                <div class="top-content py-2">
                    <a href="{{ url('/') }}">Mua thêm sản phẩm khác</a>
                </div>
                <div class="border block-infomation p-3 bg-white">
                    @if ($payment_status =='success')
                        <div class="text-center mt-3"><i class="bi bi-check2-circle"></i></div>
                        <div class="title mb-5 mt-3 text-center text-success">
                            ĐẶT HÀNG THÀNH CÔNG
                        </div>
                    @else
                        <div class="text-center mt-3"><i class="bi bi-x-circle"></i></div>
                        <div class="title mb-5 mt-3 text-center text-danger">
                            ĐẶT HÀNG THẤT BẠI
                        </div>
                    @endif
                   
                    <p>Cảm ơn {{ $male }} <strong>{{ $name }}</strong> đã cho
                        {{ $locals['company'] }}
                        cơ hội phục vụ.</p>
                    <div class="title mt-4 mb-3">
                        Thông tin giao hàng
                    </div>
                    <p><strong>Người nhận hàng: </strong>{{ $male }} {{ $name }}, {{ $phone }},
                        {{ $email }}</p>
                    <p><strong>Giao đến: </strong> {{ $address }}, {{ $ward }}, {{ $district }},
                        {{ $city }}</p>
                    @if ($note != '')
                        <p><strong>Ghi chú: </strong> {{ $note }}</p>
                    @endif
                    <div class="title mt-4 mb-3">
                        Thông tin thanh toán
                    </div>
                    <div class="row align-items-center mx-0">
                        <div class="col-auto px-1">
                            <img src="{{ asset($paymentMothod['picture']) }}" height="20px" alt="cod" />
                        </div>
                        <div class="col ps-1"><strong>{{ $paymentMothod['title'] }}</strong></div>
                    </div>
                    <div class="title mt-4 mb-3">
                        Thông tin đơn hàng
                    </div>
                    @include('default.pages.checkout.success.shopping')
                </div>
            </div>
        </div>
    </div>
@endsection
@section('styles')
    <link href="{{ asset('default/css/checkout.css') }}" rel="stylesheet" />
@endsection
