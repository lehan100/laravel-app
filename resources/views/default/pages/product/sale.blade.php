@extends('default.layouts.2-column')
@section('content')
    @include('default.pages.product.blocks.toolbar')
    <div id="lazyLoadProducts">
        @if (count($listItems) > 0)
            <div class="list-products all-products">
                <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4">
                    @foreach ($listItems as $product)
                        @include('default.blocks.item', ['product' => $product])
                    @endforeach
                </div>
            </div>
            @include('pagination.pagination')
        @else
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bi flex-shrink bi-exclamation-triangle-fill me-2"></i>
                <div>
                    Không tìm thấy dữ liệu
                </div>
            </div>
        @endif
    </div>
@endsection
@section('sitebar')
    @include('default.pages.product.blocks.sitebar')
@endsection
@section("script")
<script  defer="defer" async="async" type="text/javascript" src="{{asset('default/js/product.js')}}"></script>
@endsection