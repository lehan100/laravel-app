@php
    use App\Helpers\Category as Category;
@endphp
 {{ html()->form('POST', route("$controllerName/filter"))->attributes([
    'accept-charset' => 'UTF-8',
    'enctype' => 'multipart/form-data',
    'class' => 'p-3',
    'id' => 'appFilter',
])->open() }}
@php
    $name = html()->text('name', @$filter['name'])->attributes( ['class' => 'form-control']);
    $sku = html()->text('sku', @$filter['sku'])->attributes( ['class' => 'form-control']);
    $var = ['0' => '--- Chọn danh mục ---'];
    $categorySellector = $var + Category::generateDataSelector($categorySellector);
    $category_id = html()->select('category_id',$categorySellector, @$filter['category_id'][0])->attributes( ['class' => 'form-control']);
    $dataTypeActive = config('configs.location.active_status');
    $active = html()->select('status', $dataTypeActive, @$filter['status'])->attributes( ['class' => 'form-control']);
    $dataTypeStock = config('configs.location.stock_status');
    $stock = html()->select('stock', $dataTypeStock, @$filter['stock'])->attributes( ['class' => 'form-control']);
@endphp
<div class="row align-items-end">
    <div class="col-6 col-md-3 mb-3">
        <div class="row align-items-center">
            <div class="col-12 font-weight-bold mb-2">
                Danh mục
            </div>
            <div class="col">
                {!! $category_id  !!}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="row align-items-center">
            <div class="col-12 font-weight-bold mb-2">
                Tên sản phẩm
            </div>
            <div class="col">
                {!! $name !!}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="row align-items-center">
            <div class="col-12 font-weight-bold mb-2">
                Mã sản phẩm
            </div>
            <div class="col">
                {!! $sku !!}
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-3 mb-3">
        <div class="row align-items-center">
            <div class="col-12 font-weight-bold mb-2">
                Trạng thái
            </div>
            <div class="col">
                {!! $active !!}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="row align-items-center">
            <div class="col-12 font-weight-bold mb-2">
                Tình trạng
            </div>
            <div class="col">
                {!! $stock !!}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-auto mb-3">
        <button type="submit" name="button" value="submit" class="btn btn-warning mb-0"><i class="fa fa-filter mr-2"></i>Lọc dữ liệu</button>
        <button type="submit" name="button" value="reset" class="btn btn-danger mb-0"><i class="fa fa-close mr-2"></i>Reset</button>
    </div>
</div>
{{ html()->form()->close() }}
