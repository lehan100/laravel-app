@extends('default.layouts.1-column')
@section("content")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="alert alert-info my-5" role="alert">
                <h4 class="alert-heading">
                    <i class="bi bi-info-circle-fill me-2"></i>Giỏ hàng của bạn đang rỗng.
                </h4>
                <p class="my-4">Giỏ hàng của bạn chưa có sản phẩm nào. Hãy về trang chủ để chọn sản phẩm phù hợp với bạn!</p>
                <hr>
                <p class="mb-0">Bấm vào <a class="alert-link" href="{{url("/")}}">đây</a> để bắt đầu mua sắm.</p>
            </div>
        </div>
    </div>
</div>
@endsection