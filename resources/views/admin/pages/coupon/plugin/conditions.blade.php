@php
    use Illuminate\Support\Arr;
    $configPath = config('image.path.product');
    $product_ids = $product_ids != null ? Arr::join($product_ids, ',') : '';
    $category_ids = $category_ids != null ? Arr::join($category_ids, ',') : '';
    $inputHiddenCategoryID = html()->hidden('category_id', $category_ids)->attributes( ['id' => 'input-condition-category']);
    $inputHiddenProductID = html()->hidden('product_id', $product_ids)->attributes( ['id' => 'input-condition-product']);
@endphp
{!! $inputHiddenCategoryID !!}
{!! $inputHiddenProductID !!}
<div class="card item-condition mb-4">
    <div class="card-header">
        <div class="form-inline">
            <label><strong>Danh mục</strong></label>
            <button type="button" class="btn-condition btn-condition-category btn btn-sm btn-success m-0 ml-2"><i
                    class="fa fa-plus-circle mr-2"></i>Chọn danh mục</button>
        </div>
    </div>
    <div id="list-category" class="card-body">
        @if ($category_condition_list != null)
            @foreach ($category_condition_list as $item)
                @php
                    $id = $item['id'];
                    $name = $item['name'];
                @endphp
                <div class="breadcrumb item-category rounded p-2 border">
                    <strong>{{ $name }}</strong>
                    <a data-id="{{ $id }}" href="javascript:;" class="condition-category text-danger ml-3"><i
                            class="fa fa-trash mr-2"></i>Xóa</a>
                </div>
            @endforeach
        @else
            Chưa có danh mục nào được chọn!
        @endif

    </div>
</div>
<div class="card item-condition">
    <div class="card-header">
        <div class="form-inline">
            <label><strong>Sản phẩm</strong></label>
            <button type="button" class="btn-condition btn-condition-product btn btn-sm btn-success m-0 ml-2"><i
                    class="fa fa-plus-circle mr-2"></i>Chọn sản phẩm</button>
        </div>
    </div>
    <div id="list-product" class="card-body">
        @if ($product_condition_list != null)
            <table id="reviewProductDataTable" class=" table table-striped jambo_table" style="width:100%">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th class="text-nowrap">Tên sản phẩm</th>
                        <th>Sku</th>
                        <th>Giá</th>
                        <th class="text-nowrap">Tình trạng</th>
                        <th class="text-nowrap">Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($product_condition_list as $item)
                        @php
                            $id = $item['id'];
                            $name = $item['name'];
                            $sku = $item['sku'];

                            if ($item['stock'] == 0 || $item['quantity'] == 0) {
                                $stock = sprintf('<span class="badge badge-danger">Tạm hết hàng</span>');
                            } else {
                                $stock = sprintf('<span class="badge badge-success">Còn %s sản phẩm</span>', $item['quantity']);
                            }
                            if ($item['status'] == 1) {
                                $status = sprintf('<span class="badge badge-success"><i class="fa fa-check mr-1"></i>Kích hoạt</span>');
                            } else {
                                $status = sprintf('<span class="badge badge-danger"><i class="fa fa-ban mr-1"></i>Tạm ẩn</span>');
                            }
                            $image_src = '';
                            if ($item['picture'] != '') {
                                $picture = json_decode($item['picture']);
                                $image_src = asset($configPath['path'] . '/small/' . $picture[0]);
                            }
                            $img = sprintf('<img src="%s" width="80" height="80">', $image_src);
                            $price = \App\Helpers\Product\Price::format_price($item['price']);
                        @endphp
                        <tr>
                            <td>{!! $img !!}</td>
                            <td>{{ $name }}</td>
                            <td>{{ $sku }}</td>
                            <td>{!! $price !!}</td>
                            <td>{!! $stock !!}</td>
                            <td>{!! $status !!}</td>
                            <td><a data-id="{{ $id }}" href="javascript:;"
                                    class="condition-product text-nowrap text-danger"><i
                                        class="fa fa-trash mr-2"></i>Xóa</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            Chưa có sản phẩm nào được chọn!
        @endif

    </div>
</div>

