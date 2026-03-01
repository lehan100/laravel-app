@php
$option_type = config("product.option_type");
$type = @$item->type;
@endphp
<div class="form-inline">
    <div class="form-group">
        <label class="font-weight-bold mr-2">Attribute Type</label>
        <select name="attribute_set_type" class="option-type form-control">
            <option value="">-- Please select --</option>
                            @foreach($option_type as $k=>$val)
            <option @selected((isset($type) && $type == $k) || $k == 0) value="{{$k}}">{{$val}}</option>
                            @endforeach
        </select>
    </div>
</div>
<div id="dataAttributesValue" class="mt-4">
     @php
        $attributes = @$item->attributes;
     @endphp
     @if(isset($attributes) && count($attributes) > 0)
          @include('admin.pages.attribute.plugins.option_attribute',['type'=>$type,'items'=>$attributes])
    @else
         @include('admin.pages.attribute.plugins.option_attribute')
    @endif
</div>