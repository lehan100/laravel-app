<table id="loadValueTable-{{$index}}" class="table table-striped options-type-{{$type}}">
    <thead class="thead-dark">
        <tr>
            <th></th>
            @if($type==1)
            <th class="picture" width="80">Picture</th>
            @endif
            @if($type==2)   
            <th class="color">Color</th>
             @endif
            <th>Title</th>
            <th width="160">Price</th>
            <th width="160">Special Price</th>
            <th>Special Date</th>
            <th width="60px" class="text-center">Active</th>
            <th width="50px">Xóa</th>
        </tr>
    </thead>
    <tbody id="loadValue-{{$index}}">
      @if(isset($items) && count($items)>0)
        @include('admin.pages.product.plugins.option_value',['index'=>$index,'type'=>$type,'items'=>$items])
      @else
          @include('admin.pages.product.plugins.option_value',['index'=>$index,'type'=>$type])
      @endif
    </tbody>
</table>
<p class="mb-0"><button type="button" data-type='{{$type}}' class="add_option_value btn btn-sm btn-dark text-white"><i class="fa fa-plus-circle mr-2"></i>Add Value</button></p>