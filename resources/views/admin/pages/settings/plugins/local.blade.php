@php
   $label_key = @$key;
   $label_value = @$val;
@endphp
<div class="row mb-3 item-label">
    <div class="col-3">
        <input type="text" name="label_key[]" value="{{$label_key}}" placeholder="Keyword" class="form-control"/>
    </div>
    <div class="col">
        <input type="text" name="label_value[]" value="{{$label_value}}" placeholder="Value" class="form-control"/>
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-default p-0 m-0 h-100 text-danger" onclick="deleteLabel(this)" title="Delete Label"><i class="fa fa-trash-o"></i></button>
    </div>
</div>