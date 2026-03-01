<!DOCTYPE html>
<html lang="vi"> 
    <head>
    @include('default.layouts.elements.head')
    </head>
    <body>
        @include('default.layouts.elements.mobile.navigation')
        @include('default.layouts.elements.header')
        @include('default.layouts.elements.navigation')
        @yield('content')
        @include('default.layouts.elements.footer')
        <div style="display: none;" id="mm-blocker"></div>
        <div id="loading" style="display:none"><span class="loading"></span></div>
        @yield("modal")
        @include('default.layouts.elements.scripts')
    </body>
</html>