@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Price as Price;
    use App\Helpers\Category as Category;
    use App\Helpers\Product\Info as Product;
    $imageSize = config('image.admin.product');
    $configPath = config('image.path.product');
    $formInputAttr = config('configs.template.form_input');
    $stock_status = config('configs.stock_status');
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    <div class="x_panel p-0 border-0">
        {{-- @include('admin.templates.x_title') --}}
        <div class="x_content p-0 m-0">
            @include ('admin.pages.product.toolbar.filter')
            {{-- <div class="row align-items-center mb-3">
            <div class="col-auto">Lọc</div>
            <div class="col-auto">{{ $categoryFilter }}</div>
            <div class="col-auto">{{ $stockFilter }}</div>
        </div> --}}
            @if (count($items) > 0)
                @include ('admin.templates.notify')
                {{ html()->form('POST', '')->attributes([
                'accept-charset' => 'UTF-8',
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal form-label-left',
                'id' => 'appForm',
            ])->open() }}
                <table class="table table-striped jambo_table ">
                    <thead>
                        <tr>
                            @include('admin.templates.thead.check_all')
                            @include('admin.templates.thead.column', [
                                'name' => 'Hình ảnh',
                                'width' => '120',
                            ])
                            @include('admin.templates.thead.column', ['name' => 'SKU', 'width' => '120'])
                            @include('admin.templates.thead.column', ['name' => 'Tên ' . $title])
                            @include('admin.templates.thead.column', [
                                'name' => 'Danh mục',
                                'width' => '200',
                            ])
                            @include('admin.templates.thead.column', ['name' => 'Giá', 'width' => '150'])
                            @include('admin.templates.thead.active')
                            @include('admin.templates.thead.action')
                            @include('admin.templates.thead.id')
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $val)
                            @php
                                $name = $val['name'];
                                $sku = $val['sku'];
                                $stock = sprintf('<span class="badge badge-danger">Hết hàng</span>');
                                $weight = Product::weightToString($val->weight);
                                if ($val['quantity'] > 0) {
                                    $stock = sprintf('<span class="badge badge-success">Còn %s sản phẩm</span>', $val['quantity']);
                                }
                                if( $val['stock'] == 0){
                                    $stock = sprintf('<span class="badge badge-danger">Tạm hết hàng</span>');
                                }
                                $category = isset($val['category'][0]) ? $val['category'][0]['name'] : '';
                                $image_src = '';
                                if ($val['picture'] != '') {
                                    $picture = json_decode($val['picture']);
                                    $image_src = asset($configPath['path'] . '/small/' . $picture[0]);
                                }
                                $img = Template::showImageAminLists($image_src, $imageSize['width'], $imageSize['height']);
                                $id = $val['id'];
                                $price = Price::formatPrice($val['price'], 'text-danger font-weight-bold');
                                $status = Template::showStatus($controllerName, $val['status'], $id);
                                $buttonAction = Template::showButtomAction($controllerName, $id);
                                $linkEdit = route($controllerName . '/form', ['id' => $id]);
                            @endphp
                            <tr class="dblclick" data-link='{{ $linkEdit }}'>
                                <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                                <td class="text-center">{!! $img !!}</td>
                                <td>{{ $sku }}</td>
                                <td>{{ $name }}<p class="mb-0">{!! $stock !!}</p>
                                    <p><span class="badge badge-danger">Trọng lượng {{ $weight }}</span></p>
                                </td>
                                <td>{{ $category }}</td>
                                <td>{!! $price !!}</td>
                                <td class="text-center">{!! $status !!}</td>
                                <td class="text-center">{!! $buttonAction !!}</td>
                                <td class="text-center">{{ $id }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ html()->form()->close() }}
                @include('pagination.pagination_admin')
            @else
                @include('admin.templates.list_empty')
            @endif
        </div>
    </div>
@endsection
