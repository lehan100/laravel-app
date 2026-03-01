@extends('default.layouts.1-column')
@section("content")
<form method="POST" action="{{route("checkout/posts")}}" accept-charset="UTF-8" enctype="multipart/form-data" id="formCart">
    <input type="hidden" name="_token" value="{{csrf_token()}}" id="tokenCheckout"/>
    <div class="block-checkout container">
        <div class="top-content py-2">
            <a href="{{url("/")}}">Mua thêm sản phẩm khác</a>
        </div>
        <div class="row mb-4">
            <div class="col-12 col-md-8">
                @include('default.pages.checkout.cart.shopping')
                @include('default.pages.checkout.cart.infomation')
            </div>
            <div class="col-12 col-md-4">
                <div class="sticky-top">
                    @include("default.pages.checkout.cart.subtotal")
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section("script")
<script  defer="defer" async="async" type="text/javascript" src="{{asset('default/js/assets/checkoutQuantity.js')}}"></script>
<script  defer="defer" async="async" type="text/javascript" src="{{asset('default/js/select.full.js')}}"></script>
<script  defer="defer" async="async" type="text/javascript" src="{{asset('default/js/checkout.js')}}"></script>
@endsection
@section("styles")
<link href="{{asset('default/css/select.min.css')}}" rel="stylesheet"/>
<link href="{{asset('default/css/checkout.css')}}" rel="stylesheet"/>
@endsection