@if(count($listItems)>0)
@if(!$autopage)
<div class="list-products all-products">
    <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4">
@endif
    @foreach($listItems as $product)
        @include("default.blocks.item",['product'=>$product])
    @endforeach
@if(!$autopage)
    </div>
</div>
@include('pagination.pagination')
@endif
@else
<div class="alert alert-warning d-flex align-items-center" role="alert">
    <i class="bi flex-shrink bi-exclamation-triangle-fill me-2"></i>
    <div>
        Không tìm thấy dữ liệu
    </div>
</div>
@endif