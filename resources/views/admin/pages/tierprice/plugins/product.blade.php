@php
$inputHiddenProductID = html()->hidden('product_ids', @$product_ids)->attributes( ['id' => 'data-product-ids']);
$inputHiddenProductIDDelete = html()->hidden('product_ids_delete', "")->attributes( ['id' => 'product_ids_delete']);
@endphp
{!!$inputHiddenProductID!!}
{!!$inputHiddenProductIDDelete!!}
<div class="card bg-warning-custom text-center">
  <div class="card-body">
         <h5 class="card-title mb-3">Chọn các sản phẩm áp dụng!</h5>
        <button id="selectProduct" class="btn btn-warning" type="button"><i class="fa fa-plus-circle mr-2"></i>Chọn sản phẩm</button>
  </div>
</div>
<div id="loadDataProduct" class="mt-5">
</div>
