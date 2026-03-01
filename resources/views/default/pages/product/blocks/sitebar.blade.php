<div class="sitebar p-3 rounded border">
    <button class="sitebar-close btn btn-secondary btn-sm d-block d-xl-none"><i class="bi bi-x-lg me-2"></i>Đóng</button>
    <div class="content">
        @if (isset($listCategoryFilter) && count($listCategoryFilter) > 0)
            <div class="filter-option mb-4">
                <div class="filter-option mb-4">
                    <div class="title border-bottom mb-3 pb-2">Danh mục</div>
                    <div class="scrollbar">
                        @foreach ($listCategoryFilter as $item)
                            @php
                                $id = $item->id;
                                $name = $item->name;
                                $count_item = $item->products_count;
                                $name = sprintf('%s (%s)', $name, $count_item);
                            @endphp
                            <div class="checkbox-wrapper mb-3">
                                <input class="form-check-input filter-value" data-alias="category" type="checkbox"
                                    value="{{ $id }}" id="category-{{ $id }}">
                                <label class="form-check-label" for="category-{{ $id }}">
                                    {{ $name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        @if (isset($listFilters) && count($listFilters) > 0)
            @foreach ($listFilters as $filter)
                @php
                    $total_item = 0;
                    $name = $filter->name;
                    $alias = $filter->alias;
                    $type = $filter->type;
                    $attributes = $filter->attributes;
                @endphp
                @if (count($attributes) > 0)
                    @foreach ($attributes as $val)
                        @php
                            $total_item += $val->product_attribute_sets_count;
                        @endphp
                    @endforeach
                    @if ($type == 0 && $total_item > 0)
                        <div class="filter-option mb-4">
                            <div class="title border-bottom mb-3 pb-2">{{ $name }}</div>
                            <div class="scrollbar">
                                @foreach ($attributes as $item)
                                    @php
                                        $name = $item->name;
                                        $id = $item->id;
                                        $count_item = $item->product_attribute_sets_count;
                                        $name = sprintf('%s (%s)', $name, $count_item);
                                    @endphp
                                    @if ($count_item > 0)
                                        <div class="checkbox-wrapper mb-3">
                                            <input class="form-check-input filter-value"
                                                data-alias="{{ $alias }}" type="checkbox"
                                                value="{{ $id }}"
                                                id="{{ $alias }}-{{ $id }}">
                                            <label class="form-check-label"
                                                for="{{ $alias }}-{{ $id }}">
                                                {{ $name }}
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($type == 1 && $total_item > 0)
                        <div class="filter-option mb-4">
                            <div class="title border-bottom mb-3 pb-2">{{ $name }}</div>
                            <div class="scrollbar">
                                <div class="row row-cols-3 row-cols-md-2">
                                    @foreach ($attributes as $item)
                                        @php
                                            $name = $item->name;
                                            $id = $item->id;
                                            $count_item = $item->product_attribute_sets_count;
                                            $path = config('image.path.attribute_set');
                                            $picture = $item->picture;
                                            $pictureUrl = $item['picture'] != '' ? asset($path['path'] . '/' . $picture) : '';
                                        @endphp
                                        @if ($count_item > 0)
                                            <div class="col mb-3">
                                                <input class="d-none filter-value picture"
                                                    data-alias="{{ $alias }}" type="checkbox"
                                                    value="{{ $id }}">
                                                <img class="filter-picture cursor-pointer rounded border p-1"
                                                    src="{{ $pictureUrl }}" alt="{{ $name }}"
                                                    title="{{ $name }}" />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($type == 2 && $total_item > 0)
                        <div class="filter-option mb-4">
                            <div class="title border-bottom mb-3 pb-2">{{ $name }}</div>
                            <div class="scrollbar">
                                @foreach ($attributes as $item)
                                    @php
                                        $name = $item->name;
                                        $id = $item->id;
                                        $count_item = $item->product_attribute_sets_count;
                                        $name = sprintf('%s (%s)', $name, $count_item);
                                    @endphp
                                    @if ($count_item > 0)
                                        <div class="checkbox-wrapper mb-3">
                                            <input class="form-check-input filter-value"
                                                data-alias="{{ $alias }}" type="checkbox"
                                                value="{{ $id }}"
                                                id="{{ $alias }}-{{ $id }}">
                                            <label class="form-check-label"
                                                for="{{ $alias }}-{{ $id }}">{{ $name }}</label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        @endif
        <div class="filter-option mb-4">
            <div class="filter-option mb-4">
                <div class="title border-bottom mb-3 pb-2">Giá</div>
                <div class="scrollbar">
                    <div class="checkbox-wrapper mb-3">
                        <input class="form-check-input filter-value" data-alias="price" type="checkbox" value="0-100000"
                            id="price-0-100000">
                        <label class="form-check-label" for="price-0-100000">
                            Dưới 100.000&nbsp;₫
                        </label>
                    </div>
                    <div class="checkbox-wrapper mb-3">
                        <input class="form-check-input filter-value" data-alias="price" type="checkbox"
                            value="100000-500000" id="price-100000-500000">
                        <label class="form-check-label" for="price-100000-500000">
                            100.000&nbsp;₫ - 500.000&nbsp;₫
                        </label>
                    </div>
                    <div class="checkbox-wrapper mb-3">
                        <input class="form-check-input filter-value" data-alias="price" type="checkbox"
                            value="500000-1000000" id="price-500000-1000000">
                        <label class="form-check-label" for="price-500000-1000000">
                            500.000&nbsp;₫ - 1.000.000&nbsp;₫
                        </label>
                    </div>
                    <div class="checkbox-wrapper mb-3">
                        <input class="form-check-input filter-value" data-alias="price" type="checkbox"
                            value="1000000-100000000" id="price-1000000-100000000">
                        <label class="form-check-label" for="price-1000000-100000000">
                            Trên 1.000.000&nbsp;₫
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('styles')
    <link href="{{ asset('default/css/checkbox.css') }}" rel="stylesheet" />
@endsection
