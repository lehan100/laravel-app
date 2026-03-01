<!DOCTYPE html>
<html lang="en">

<head>
    @include('admin.layouts.elements.head')
</head>
<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                @include('admin.layouts.elements.sidebar')
            </div>
            <div class="top_nav d-print-none">
                @include('admin.layouts.elements.nav_top')
            </div>
            <div class="right_col" role="main">
                @yield('content')
            </div>
            @include('admin.layouts.elements.footer')
        </div>
    </div>
    @include('admin.layouts.elements.scripts')
</body>
</html>