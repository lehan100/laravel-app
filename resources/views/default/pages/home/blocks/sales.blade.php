@if (count($listSales) > 0)
    @foreach ($listSales as $sale)
        @php
            $title = $sale->name;
            $products = $sale->product_items;
            $link_sale = url($sale->url['path']);
        @endphp
        @if (count($products) > 0)
            <div class="group-product sale_group rounded-2 overflow-hidden border mt-4">
                <div class="title p-3">
                    <a href="{{ $link_sale }}"><span class="sale-name">{{ $title }}</span></a>
                </div>
                <div class="group-items list-products p-3">
                    <div class="owl-items-5x owl-carousel carousel-nav my-3">
                        @foreach ($products as $item_sale)
                            @include('default.blocks.item_sale', ['item_sale' => $item_sale])
                        @endforeach
                    </div>
                    {{-- <div class="row row-cols-2 row-cols-sm-4 row-cols-lg-5">
                        @foreach ($products as $item_sale)
                            @include('default.blocks.item_sale', ['item_sale' => $item_sale])
                        @endforeach
                    </div> --}}
                    <div class="view-all text-center"><a href="{{ $link_sale }}" class="btn btn-light">Xem tất cả sản
                        phẩm<i class="bi bi-caret-down-fill ms-2"></i></a></div>
                </div>
                
            </div>
        @endif
    @endforeach
@endif
<style>
    .group-items .owl-carousel .owl-stage {
        display: flex;
    }

    .group-items .sale-items {
        display: flex;
        flex: 1 0 auto;
        height: 100%;
        background: transparent!important
    }
    .group-items .sale-items a{
       padding: 0;
    }
</style>
