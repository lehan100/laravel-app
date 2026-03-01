@php
use App\Blocks\NavigationMenu as Category;
$menu = (new Category())->generate();
@endphp
<div class="navigation" id="navigation">
    <div class="container container-full">
        <div class="row align-items-center">
            <div class="col-auto position-relative px-0 d-block d-xl-none">
                <a href="javascript:;" class="navicon-menu">
                    <div class="navicon me-0"></div>
                </a>
            </div>
            <div class="col-auto position-relative d-none d-xl-block" id="menu-dept">
                <a href="javascript:;" class="navicon-menu">
                    <div class="d-flex align-items-center">
                        <div class="navicon"></div>
                        <span>Chọn danh mục</span>
                    </div>
                </a>
                <div id="list-menu">
                    {!!$menu!!}
                </div>
            </div>
            <div class="col">
                @include('default.layouts.elements.search')
            </div>
            <div class="col-auto header-group py-2 d-none d-xl-block">
                <a href="/tin-tuc.html" class="text-white"> 
                    <p class="mb-0">Kinh nghiệm hay</p>
                    <span class="name">Hướng dẫn, Mẹo vặt</span>
                </a>
            </div>
            <div class="box-shopping-cart col-auto pe-mobile-0 ps-mobile-0">
                <div class="shopping-cart">
                    <div class="d-flex align-items-center cursor-pointer">
                        <span class="d-none d-xl-block">Giỏ hàng</span>
                        <span class="mx-2" id="icon-cart"><span class="my-quantity-cart">0</span></span>
                        <i class="bi bi-caret-down-fill d-none "></i>
                    </div>
                    @include('default.layouts.elements.shopping_cart')
                </div>
            </div>
        </div>
    </div>
</div>
