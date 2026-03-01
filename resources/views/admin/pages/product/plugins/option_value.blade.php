@php
use App\Helpers\Price as Price;
use Illuminate\Support\Carbon;
@endphp
@if(isset($items) && count($items)>0)
@foreach($items as $item)
@php
    
    $configPath = config('image.path.product_option');
    $id = $item->id;
    $title = $item->title;
    $priceFixed = Price::formatNumber($item->price);
    $special_price = Price::formatNumber($item->special_price);
    $special_price_date ="";
    if(isset($item->special_price_from) && isset($item->special_price_to) && $item->special_price_from && $item->special_price_to){
        $special_price_from = @Carbon::parse($item->special_price_from)->format('m/d/Y');
        $special_price_to = @Carbon::parse($item->special_price_to)->format('m/d/Y');
        $special_price_date = $special_price_from.' - '.$special_price_to;
    }
    
    $color = $item->color;
    $status = ($item->status && $item->status==0) ? '' : 'checked';
    $status_value = $item->status;
    $picture = $item->picture;
    $pictureUrl = ($picture != "")? asset($configPath['path'].'/'.$picture) : "";
@endphp
<tr>
    <td width="30"><i class="fa fa-th"></i></td>
    <td class="picture images-option @if($type!=1) d-none @endif">
        <div class="wp">
            <img class="uploadPreview" src="{{$pictureUrl}}" width="100%" alt="">
            <div class="icon-add"><i class="fa fa-file-image-o"></i></div>
            <a class="delete fa fa-trash" onclick="doDeleteImgAttr(this)"></a>
            <input class="btn-add-images" name="option_picture" type="file" onchange="reviewImages(this)" accept="image/png, image/gif, image/jpeg">
            <input type="hidden" name="option_attr_picture[{{$index}}][]" class="images" value="{{$picture}}"/>
        </div>
    </td>
<input type="hidden" name="option_attr_task[{{$index}}][]" value="edit-item">
<input type="hidden" name="option_attr_id[{{$index}}][]"  value="{{$id}}"/>
<td class="color @if($type!=2) d-none @endif">
    <div class="input-group colorpicker-component colorrgba" style="width:130px;margin-bottom:0">
        <input type="text" name="option_attr_color[{{$index}}][]" value="{{$color}}" class="form-control"/>
        <span class="input-group-addon"><i></i></span>
    </div>	 
</td>
<td><input type="text" name="option_attr_title[{{$index}}][]" value="{{$title}}" class="option-attr-title form-control" placeholder="Title"/></td>
<td><input type="text" onKeyUp="this.value = FormatNumber(this.value);" name="option_attr_price[{{$index}}][]" value="{{$priceFixed}}" class="option-attr-price form-control" placeholder="Price"/></td>
<td><input type="text" onKeyUp="this.value = FormatNumber(this.value);" name="option_attr_special_price[{{$index}}][]" value="{{$special_price}}" class="option-attr-price form-control" placeholder="Special Price"/></td>
<td><input class="option_special_date form-control" name="option_attr_special_price_date[{{$index}}][]" type="text" value="{{$special_price_date}}"></td>
<td class="text-center"><input type="checkbox" value="{{$status_value}}" {{$status}} class="m-0" name="option_attr_status[{{$index}}][]"/></td>
<td class="text-center"><button type="button" class="btn btn-default p-0 m-0 text-danger" onclick="deleteOptionValue(this,{{$id}})" title="Delete Value"><i class="fa fa-trash-o"></i></button></td>
</tr>
@endforeach
@else
<tr>
    <td width="30"><i class="fa fa-th"></i></td>
    <td class="picture images-option @if($type!=1) d-none @endif">
        <div class="wp">
            <img class="uploadPreview d-none" src="" width="100%" alt="">
            <div class="icon-add"><i class="fa fa-file-image-o"></i></div>
            <input class="btn-add-images" name="option_picture" type="file" onchange="reviewImages(this)">
            <input type="hidden" name="option_attr_picture[{{$index}}][]" class="images" value=""/>
        </div>
    </td>
<input type="hidden" name="option_attr_task[{{$index}}][]" value="add-item">
<td class="color @if($type!=2) d-none @endif">
    <div class="input-group colorpicker-component colorrgba" style="width:130px;margin-bottom:0">
        <input type="text" name="option_attr_color[{{$index}}][]" value="#000" class="form-control"/>
        <span class="input-group-addon"><i></i></span>
    </div>	 
</td>
<td><input type="text" name="option_attr_title[{{$index}}][]" class="option-attr-title form-control" placeholder="Title"/></td>
<td><input type="text" onKeyUp="this.value = FormatNumber(this.value);" name="option_attr_price[{{$index}}][]" value="0" class="option-attr-price form-control" placeholder="Price"/></td>
<td><input type="text" onKeyUp="this.value = FormatNumber(this.value);" name="option_attr_special_price[{{$index}}][]" value="0" class="option-attr-price form-control" placeholder="Special Price"/></td>
<td><input class="option_special_date form-control" name="option_attr_special_price_date[{{$index}}][]" type="text" value=""></td>
<td class="text-center"><input type="checkbox" value="1" checked="true" class="m-0" name="option_attr_status[{{$index}}][]"/></td>
<td><button type="button" class="btn btn-default p-0 m-0 text-danger" onclick="deleteOptionValue(this)" title="Delete Value"><i class="fa fa-trash-o"></i></button></td>
</tr>
@endif
