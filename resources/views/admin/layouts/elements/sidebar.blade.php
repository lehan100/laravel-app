<div class="left_col scroll-view">
    <div class="navbar nav_title" style="border: 0;">
        <a href="index.html" class="site_title"><span>Admin Control Panel</span></a>
    </div>
    <div class="profile clearfix">
        <div class="profile_pic">
            <img src="{{ asset('admin/production/images/img.jpg') }}" alt="..." class="img-circle profile_img">
        </div>
        <div class="profile_info">
            <span>Welcome,</span>
            <h2>{{ auth()->user()->fullname }}</h2>
        </div>
    </div>
    <br />

    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
        <div class="menu_section">
            <ul class="nav side-menu">
                @if (auth()->user()->can('settings'))
                    <li>
                        <a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('settings/sitemap') }}">Tạo Sitemap</a></li>
                            <li><a target="_blank" href="{{ url('') }}">Xem trang chính</a></li>
                        </ul>
                    </li>
                @endif
                @if (auth()->user()->can('settings') ||
                        auth()->user()->can('cache') ||
                        auth()->user()->can('province') ||
                        auth()->user()->can('district') ||
                        auth()->user()->can('ward'))
                    <li><a><i class="fa fa-gear"></i> Systems <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            @if (auth()->user()->can('settings'))
                                <li><a href="{{ route('settings') }}">Page Settings</a></li>
                            @endif
                            @if (auth()->user()->can('cache'))
                                <li><a href="{{ route('cache') }}">Cache Management</a></li>
                            @endif
                            @if (auth()->user()->can('province'))
                                <li><a href="{{ route('province') }}">Provinces</a></li>
                            @endif
                            @if (auth()->user()->can('district'))
                                <li><a href="{{ route('district') }}">Districts</a></li>
                            @endif
                            @if (auth()->user()->can('ward'))
                                <li><a href="{{ route('ward') }}">Wards</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                @auth
                    @role('Aministrators')
                        <li><a><i class="fa fa fa-user"></i> Customers<span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                <li><a href="{{ route('user') }}">List Users</a></li>
                                <li><a href="{{ route('roles.index') }}">Roles</a></li>
                            </ul>
                        </li>
                    @endrole
                @endauth
                @if (auth()->user()->can('order'))
                    <li><a><i class="fa fa-shopping-cart"></i> Orders <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            {{-- <li><a href="#">Cấu hình</a></li> --}}
                            <li><a href="{{ route('order') }}">List Orders</a></li>
                        </ul>
                    </li>
                @endif
                @if (auth()->user()->can('category') || auth()->user()->can('product') || auth()->user()->can('post'))
                    <li><a><i class="fa fa-clipboard"></i> Catalog <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            @if (auth()->user()->can('category'))
                                <li><a href="{{ route('category') }}">Categories</a></li>
                            @endif
                            @if (auth()->user()->can('product'))
                                <li><a href="{{ route('product') }}">Products</a></li>
                            @endif
                            @if (auth()->user()->can('post'))
                                <li><a href="{{ route('post') }}">News</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if (auth()->user()->can('attribute') || auth()->user()->can('inventory') || auth()->user()->can('rating'))
                    <li><a><i class="fa fa fa-clipboard"></i> Store Products<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            @if (auth()->user()->can('inventory'))
                                <li><a href="{{ route('inventory') }}">Inventory</a></li>
                            @endif
                            @if (auth()->user()->can('attribute'))
                                <li><a href="{{ route('attribute') }}">Product Attributes</a></li>
                            @endif
                            @if (auth()->user()->can('optionEntries'))
                                <li><a href="{{ route('optionEntries') }}">Product Option Entries</a></li>
                            @endif
                            @if (auth()->user()->can('rating'))
                                <li><a href="{{ route('rating') }}">Product Ratings</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if (auth()->user()->can('coupon') || auth()->user()->can('sales')|| auth()->user()->can('tierPrice'))
                    <li><a><i class="fa fa fa-shopping-cart"></i> Store Promotions<span
                                class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            @if (auth()->user()->can('coupon'))
                                <li><a href="{{ route('coupon') }}">Coupon Code</a></li>
                            @endif
                            @if (auth()->user()->can('sales'))
                                <li><a href="{{ route('sales') }}">Sales</a></li>
                            @endif
                            @if (auth()->user()->can('tierPrice'))
                                <li><a href="{{ route('tierPrice') }}">Tier Price</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if (auth()->user()->can('position') || auth()->user()->can('photo'))
                    <li><a><i class="fa fa-file-archive-o"></i> Media<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            @if (auth()->user()->can('position'))
                                <li><a href="{{ route('position') }}">Position</a></li>
                            @endif
                            @if (auth()->user()->can('photo'))
                                <li><a href="{{ route('photo') }}">Images</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (auth()->user()->can('contact'))
                    <li><a><i class="fa fa-inbox"></i> Others <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ route('contact') }}">Contacts</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>

    </div>
    <div class="sidebar-footer hidden-small">
        {{-- <a data-toggle="tooltip" data-placement="top" title="Settings">
            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="FullScreen">
            <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="Lock">
            <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
        </a> --}}
        <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('auth/logout') }}">
            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
        </a>
    </div>

</div>
