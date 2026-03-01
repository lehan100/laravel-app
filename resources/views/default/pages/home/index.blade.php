@extends('default.layouts.1-column')
@section("content")
<div class="container container-full">
    @include('default.pages.home.blocks.banner')
    @include('default.pages.home.blocks.category_home')
    @include('default.pages.home.blocks.sales')
    @include('default.pages.home.blocks.category_product')
</div>
@endsection