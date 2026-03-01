@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use Illuminate\Support\Carbon;
    use App\Helpers\Price as Price;
    $imageSize = config('image.admin.product');
    $configPath = config('image.path.product');
    $formInputAttr = config('configs.template.form_input');
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    <div class="x_panel">
        @include('admin.templates.x_title')
        <div class="x_content">
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
                                'name' => 'Sản phẩm',
                                'width' => '500',
                            ])
                            @include('admin.templates.thead.column', [
                                'name' => 'Bậc giảm',
                            ])
                            @include('admin.templates.thead.column', ['name' => 'Ngày bắt đầu','width' => '150'])
                            @include('admin.templates.thead.column', ['name' => 'Ngày kết thúc','width' => '150'])
                            @include('admin.templates.thead.active')
                            @include('admin.templates.thead.action')
                            @include('admin.templates.thead.id')
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $val)
                            @php
                                $products = $val->products;
                                $option = $val->items;
                                $date_from = Carbon::parse($val['date_from'])->format('d/m/Y');
                                $date_to = Carbon::parse($val['date_to'])->format('d/m/Y');
                                $id = $val['id'];
                                $status = Template::showStatus($controllerName, $val['status'], $id);
                                $buttonAction = Template::showButtomAction($controllerName, $id);
                                $linkEdit = route($controllerName . '/form', ['id' => $id]);
                            @endphp
                            <tr class="dblclick" data-link='{{ $linkEdit }}'>
                                <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                                <td>
                                    @if (count($products) > 0)
                                        @foreach ($products as $product)
                                            @php
                                                $name = $product->name;
                                                $sku = $product->sku;
                                                $price = Price::formatPrice(
                                                    $product->price,
                                                    'text-danger font-weight-bold',
                                                );
                                                $image_src = '';
                                                if ($product['picture'] != '') {
                                                    $picture = json_decode($product['picture']);
                                                    $image_src = asset($configPath['path'] . '/small/' . $picture[0]);
                                                }
                                                $img = Template::showImageAminLists(
                                                    $image_src,
                                                    $imageSize['width'],
                                                    $imageSize['height'],
                                                );
                                            @endphp
                                            <div class="bg-white mb-2 p-2 rounded border">
                                                <div class="row">
                                                    <div class="col-auto">
                                                        {!! $img !!}
                                                    </div>
                                                    <div class="col">
                                                        <div class="name">{{ $name }}</div>
                                                        <div class="sku"><i>#{{ $sku }}</i></div>
                                                        <div class="item-price">{!! $price !!}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if (count($items)> 0)
                                    @foreach ($option as $item)
                                        @php
                                            $qty_buy = $item ->order_qty;
                                            $type = (int) $item ->type;
                                            $special_percent = $item ->special_percent;
                                            $special_price = $item ->special_price;
                                            $discount = ($type == 0) ? 'giá còn <strong class="text-danger">'.Price::formatPrice($special_price,'text-danger font-weight-bold')."</strong>" : 'giảm <strong class="text-danger">'.$special_percent ."%</strong>";
                                        @endphp
                                        <p>Mua <strong class="text-danger">{{$qty_buy}}</strong>,  {!!$discount!!}</p>
                                    @endforeach
                                    @endif
                                </td>
                                <td>{{ $date_from }}</td>
                                <td>{{ $date_to }}</td>
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
