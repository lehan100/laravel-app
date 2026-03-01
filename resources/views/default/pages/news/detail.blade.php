@extends('default.layouts.2-column-new')
@section('content')
    @if ($newItem)
        <div class="page-new-view px-3 py-4 mb-4">
            @php
                $name = $newItem->name;
                $content = $newItem->contents->content;
            @endphp
            <h1 class="title mb-3">{{ $name }}</h1>
            <div class="main">
                {!! $content !!}
            </div>
        </div>
    @else
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi flex-shrink bi-exclamation-triangle-fill me-2"></i>
            <div>
                Không tìm thấy dữ liệu
            </div>
        </div>
    @endif
@endsection
@section('sitebar')
    @include('default.pages.news.blocks.category_sitebar_detail')
@endsection
@section('styles')
    <link href="{{ asset('default/css/news.css') }}" rel="stylesheet" />
@endsection
