@php
$inputHiddenProductID = html()->hidden('product_ids', @$product_ids)->attributes( ['id' => 'data-product-ids']);
$inputHiddenConditionSales = html()->hidden('condition_sales', @$condition_sales)->attributes( ['id' => 'condition_sales']);
$inputHiddenProductIDDelete = html()->hidden('product_ids_delete', "")->attributes( ['id' => 'product_ids_delete']);
@endphp
{!!$inputHiddenProductID!!}
{!!$inputHiddenConditionSales!!}
{!!$inputHiddenProductIDDelete!!}
<button type="button" id="btn-add-product" class="btn btn-sm btn-success m-0"><i class="fa fa-plus-circle mr-2"></i>Chọn sản
    phẩm</button>
<div id="list-product" class="my-3">
    @if (isset($listProduct) && count($listProduct) > 0)
        <table id="reviewProductDataTable" class="table table-striped jambo_table" style="width:100%">
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th class="text-nowrap">Sản phẩm</th>
                    <th>Giá</th>
                    <th class="text-nowrap">Khuyến mãi</th>
                    <th class="text-nowrap">Thời hạn</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    @else
        <p class="alert alert-warning text-white">Chưa có sản phẩm nào được chọn!</p>
    @endif
</div>
