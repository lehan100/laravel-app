@php
    use App\Helpers\Product\Price as ProductPriceHelper;
@endphp
@if (count($itemsCategoryCenter) > 0)
    @foreach ($itemsCategoryCenter as $val)
        @php
            $products = $itemsProducts[$val->id];
            $products_count = 0;
            $linkCategory = url($val->url->path);
            $name = $val->name;
            $categoryParents = $val->category_parents;
        @endphp
        @if (count($products) > 0)
            <div class="group-product rounded-2 overflow-hidden border bg-white mt-4">
                <div class="title px-3 py-2">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-12 col-md-5"><a href="{{ $linkCategory }}" class="name">{{ $name }}</a>
                        </div>
                        @if (count($categoryParents) > 0)
                            <div class="col-12 col-md-7">
                                @if (count($categoryParents) > 5)
                                    <div class="owl-row">
                                @endif
                                @php
                                    $rightClass = count($categoryParents) <= 5 ? 'right-item' : '';
                                @endphp
                                <div class="owl-carousel owl-items-5x0 carousel-nav {{ $rightClass }}">
                                    @foreach ($categoryParents as $parent)
                                        @php
                                            $linkParent = url($parent->url->path);
                                            $nameParent = $parent->name;
                                            $products_count += $parent->products_count;
                                            $products_count_text = $parent->products_count > 0 ? sprintf('(%s)', $parent->products_count) : '';
                                        @endphp
                                        <div class="item row align-items-center mx-2  rounded-2">
                                            <a class="col" href="{{ $linkParent }}">{{ $nameParent }}
                                                {{ $products_count_text }}</a>
                                        </div>
                                    @endforeach
                                </div>
                                @if (count($categoryParents) > 5)
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        <div class="group-items list-products p-3 mt-2">
            <div class="row row-cols-2 row-cols-sm-4 row-cols-lg-5">
                @foreach ($products as $product)
                    @include('default.blocks.item', ['product' => $product])
                @endforeach
            </div>
            <div class="view-all text-center"><a href="{{ $linkCategory }}" class="text-info">Xem thêm
                    {{ $products_count }} sản phẩm<i class="bi bi-caret-down-fill ms-2"></i></a></div>
        </div>
        </div>
    @endif
@endforeach
@endif
