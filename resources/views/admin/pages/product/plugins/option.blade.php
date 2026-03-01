@php
$option_type = config("product.option_type");
$title = @($item->title) ? $item->title : 'New Option';
$titleValue = @($item->title) ? $item->title : '';
$type = @$item->type;
$status = @($item->status && $item->status==0) ? '' : 'checked';
$status_value = @($item->status) ? $item->status : 1;
$task = @isset($item) ? 'edit-item' : 'add-item';
$option_id = @$item->id;
@endphp
<li data-index="{{$index}}" class="option d-block mb-4">
    <input name="option_index[]" type="hidden" value="{{$index}}">
    <input name="option_task[{{$index}}]" type="hidden" value="{{$task}}">
    @if(isset($item))
    <input name="option_id[{{$index}}]" type="hidden" value="{{$option_id}}">
    @endif
    <div class="card">
        <h5 class="card-header">
            <div class="row align-content-center">
                <div class="col-auto pr-0"><i class="fa fa-th"></i></div>
                <div class="col review-title-{{$index}}">{{$title}}</div>
                <div class="col-auto"><button type="button" onclick="deleteOption(this,{{$option_id}})" class="btn btn-default p-0 m-0 text-secondary" style="font-size:20px" title="Delete Option"><i class="fa fa-trash-o"></i></button></div>
            </div>
        </h5>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="font-weight-bold">Option Title</label>
                        <input required type="text" value="{{$titleValue}}" name="option_title[{{$index}}]" class="option-title form-control">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="font-weight-bold">Option Type</label>
                        <select name="option_type[{{$index}}]" class="option-type form-control">
                            <option value="">-- Please select --</option>
                            @foreach($option_type as $k=>$val)
                            <option {{(isset($type) && $type == $k) ? "selected" :""}} value="{{$k}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group text-center">
                        <label class="d-block font-weight-bold">Active</label>
                        <input type="checkbox" {{$status}} value="{{$status_value}}" class="mt-3" name="option_status[{{$index}}]"/>
                    </div>
                </div>
            </div>
            <div id="dataAttributes-{{$index}}" class="mt-4">
              @php
                $attributes = @$item->attributes;
             @endphp
             @if(isset($attributes) && count($attributes) > 0)
                  @include('admin.pages.product.plugins.option_attribute',['index'=>$index,'type'=>$type,'items'=>$attributes])
              @endif
            </div>
        </div>
    </div>
</li>