<!DOCTYPE html>
<html lang="vi">
<head>
    @include('default.layouts.elements.head')
</head>
<body>
    @include('default.layouts.elements.mobile.navigation')
    @include('default.layouts.elements.header')
    @include('default.layouts.elements.navigation')
    <div class="block-breadcrumb py-2">
        <div class="container">
            {!! $breadcrumbs !!}
        </div>
    </div>
    <div class="container py-4">
        <div class="row">
            <div class="col-12 col-xl-3 order-1 order-md-0">@yield('sitebar')</div>
            <div class="col-12 col-xl-9">@yield('content')</div>
        </div>
    </div>
    @include('default.layouts.elements.footer')
    <div style="display: none;" id="mm-blocker"></div>
    <div id="loading" style="display:none"><span class="loading"></span></div>
    @yield('modal')
    @include('default.layouts.elements.scripts')
</body>

</html>
