@php
    use App\Helpers\Price as Price;
@endphp
@if (isset($items) && count($items) > 0)
    @foreach ($items as $item)
        @php
            $configPath = config('image.path.product_option');
            $id = $item['id'];
            $title = $item['title'];
            $color = $item['color'];
            $priceFixed = Price::formatNumber($item['price']);
            $status = $item['status'] && $item['status'] == 1 ? 'checked' : '';
            $status_value = $item['status'];
            $picture = $item['picture'];
            $pictureUrl = $item['picture'] != '' ? asset($configPath['path'] . '/' . $picture) : '';
        @endphp
        <tr>
            <td width="30"><i class="fa fa-th"></i></td>
            <td class="picture images-option @if ($type != 1) d-none @endif">
                @if ($picture != '')
                    <div class="wp">
                        <img class="uploadPreview" src="{{ $pictureUrl }}" width="100%" alt="">
                        <div class="icon-add"><i class="fa fa-file-image-o"></i></div>
                        <a class="delete fa fa-trash" onclick="doDeleteImgAttr(this)"></a>
                        <input class="btn-add-images" name="option_picture" type="file"
                            onchange="reviewImages(this)">
                        <input type="hidden" name="option_value_picture[]" class="images"
                            value="{{ $picture }}" />
                        <input type="hidden" name="option_value_picture_del[]" class="picture_del" value="" />
                    </div>
                @else
                    <div class="wp">
                        <img class="uploadPreview d-none" src="" width="100%" alt="">
                        <div class="icon-add"><i class="fa fa-file-image-o"></i></div>
                        <input class="btn-add-images" name="option_picture" type="file"
                            onchange="reviewImages(this)">
                        <input type="hidden" name="option_value_picture[]" class="images" value="" />
                    </div>
                @endif

            </td>
            <input type="hidden" name="option_value_task[]" value="edit-item">
            <input type="hidden" name="option_value_id[]" value="{{ $id }}" />
            <td class="color @if ($type != 2) d-none @endif">
                <div class="input-group colorpicker-component colorrgba" style="width:130px;margin-bottom:0">
                    <input type="text" name="option_value_color[]" value="{{ $color }}"
                        class="form-control" />
                    <span class="input-group-addon"><i></i></span>
                </div>
            </td>
            <td><input type="text" name="option_value_name[]" value="{{ $title }}"
                    class="option-attr-title form-control" placeholder="Title" /></td>
            <td><input type="text" onKeyUp="this.value = FormatNumber(this.value);" name="option_value_price[]"
                    value="{{ $priceFixed }}" class="option-price form-control" placeholder="Price" /></td>
            <td class="text-center"><input type="checkbox" value="{{ $status_value }}" {{ $status }}
                    class="m-0" name="option_value_status[]" /></td>
            <td class="text-center"><button type="button" class="btn btn-default p-0 m-0 text-danger"
                    onclick="deleteOptionValue(this,{{ $id }})" title="Delete Value"><i
                        class="fa fa-trash-o"></i></button></td>
        </tr>
    @endforeach
@else
    <tr>
        <td width="30"><i class="fa fa-th"></i></td>
        <td class="picture images-option @if ($type != 1) d-none @endif">
            <div class="wp">
                <img class="uploadPreview d-none" src="" width="100%" alt="">
                <div class="icon-add"><i class="fa fa-file-image-o"></i></div>
                <input class="btn-add-images" name="option_picture" type="file" onchange="reviewImages(this)">
                <input type="hidden" name="option_value_picture[]" class="images" value="" />
            </div>
        </td>
        <input type="hidden" name="option_value_task[]" value="add-item">
        <td class="color @if ($type != 2) d-none @endif">
            <div class="input-group colorpicker-component colorrgba" style="width:130px;margin-bottom:0">
                <input type="text" name="option_value_color[]" value="#000" class="form-control" />
                <span class="input-group-addon"><i></i></span>
            </div>
        </td>
        <td><input type="text" name="option_value_name[]" class="option-attr-title form-control"
                placeholder="Title" /></td>
        <td><input type="text" onKeyUp="this.value = FormatNumber(this.value);" name="option_value_price[]"
                value="0" class="option-price form-control" placeholder="Price" /></td>
        <td class="text-center"><input type="checkbox" value="1" checked="true" class="m-0"
                name="option_value_status[]" /></td>
        <td><button type="button" class="btn btn-default p-0 m-0 text-danger" onclick="deleteOptionValue(this)"
                title="Delete Value"><i class="fa fa-trash-o"></i></button></td>
    </tr>
@endif
