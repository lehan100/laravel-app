@php
    use App\Helpers\Product\Price;
    use App\Helpers\Template as Template;
    $imageSize = config('image.admin.product');
    $configPath = config('image.path.product');
@endphp
@if (count($productSeller) > 0)
    <table id="datatable-seller" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
        width="100%">
        <thead>
            <tr>
                <th class="no-sort">Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Đơn giá</th>
                <th>Tổng lượt mua</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productSeller as $product)
                @php
                    $name = $product->name;
                    $price = Price::getPrice($product);
                    $hit_order = $product->hit_order;
                    $image_src = '';
                    if ($product['picture'] != '') {
                        $picture = json_decode($product['picture']);
                        $image_src = asset($configPath['path'] . '/small/' . $picture[0]);
                    }
                    $img = Template::showImageAminLists($image_src, $imageSize['width'], $imageSize['height']);
                @endphp
                <tr>
                    <td>{!! $img !!}</td>
                    <td>{{ $name }}</td>
                    <td>{!! Price::format_price($price) !!}</td>
                    <td>{{ $hit_order }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="alert alert-danger">Không tìm thấy dữ liệu</p>
@endif
