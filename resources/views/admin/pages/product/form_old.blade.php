@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    use Illuminate\Support\Carbon;
    use App\Helpers\Price as Price;
    use App\Helpers\Category as CategoryHelper;
    $configPath = config('image.path.product');
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $inputHiddenID = Form::hidden('id', @$item['id']);
    $inputHiddenEntypeId = Form::hidden('entype_id', @$item['entype_id']);   
    $inputHiddenOptionId = Form::hidden('option_de_id', '',array("id"=>"option_de_id"));
    $inputHiddenOptionValueId = Form::hidden('option_value_id', '',array("id"=>"option_value_id"));
    $categoryIDOLD = @isset($item->category) ? json_encode($item->category->toArray()) :null;
    $content = @$item->contents;
    $inputHiddenCATID = Form::hidden('category_old', @$categoryIDOLD);
    $inputHiddenRollback = Form::hidden('rollback', 0, ['id' => 'rollback']);
    $quantity = isset($item['quantity']) ? $item['quantity'] : 0;
    $dataStock = ['' => '--- Please Select ---','0'=>'Hết hàng','1'=>'Còn hàng'];

    $weight = isset($item['weight']) ? $item['weight'] : 0;
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $data_attibute_sets = @$item->attibute_sets;
    $catSellector = (new CategoryHelper())->generateSelector(@$categorySellector, @$item->category);
//    $special_price_from = @Carbon::parse($item->special_price_from)->format('m/d/Y');
//    $special_price_to = @Carbon::parse($item->special_price_to)->format('m/d/Y');
    $special_price_date ="";
    if(isset($item->special_price_from) && isset($item->special_price_to) && $item->special_price_from && $item->special_price_to){
        $special_price_from = @Carbon::parse($item->special_price_from)->format('m/d/Y');
        $special_price_to = @Carbon::parse($item->special_price_to)->format('m/d/Y');
        $special_price_date = $special_price_from.' - '.$special_price_to;
    }
//    print_r($catSellector);die();
    $elementsGeneral = [
        [
            'label' => Form::label('active', 'Duyệt', ['class' => $formLabelAttr]),
            'element' => Form::checkbox('status', @$item['status'], $status, ['class' => 'js-switch']),
        ],
        [
            'label' => Form::label('name', 'Tên '.$mainTitle, ['class' => $formLabelAttr]),
            'element' => Form::text('name', @$item['name'], ['class' => $errors->first('name') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('name') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('name')) : '',
        ],
        [
            'label' => Form::label('sku', 'Mã '.$mainTitle, ['class' => $formLabelAttr]),
            'element' => Form::text('sku', @$item['sku'], ['class' => $errors->first('sku') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('sku') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('sku')) : '',
        ],
        [
            'label' => Form::label('quantity', 'Số lượng', ['class' => $formLabelAttr]),
            'element' => Form::text('quantity', $quantity, ['class' => $errors->first('quantity') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('quantity') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('quantity')) : '',
        ],
        [
            'label' => Form::label('name', 'Tình trạng', ['class' => $formLabelAttr]),
            'element' => Form::select('stock', $dataStock, @$item['stock'], ['class' => $errors->first('stock') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('stock') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('stock')) : '',
        ],
        [
            'label' => Form::label('weight', 'Trọng lượng (g)', ['class' => $formLabelAttr]),
            'element' => Form::text('weight', $weight, ['class' => $errors->first('weight') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('weight') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('weight')) : '',
        ],
        [
            'label' => Form::label('cat_id', 'Danh mục', ['class' => $formLabelAttr]),
            'element' => $catSellector,
        ],
        [
            'element' => $inputHiddenID . $inputHiddenRollback.$inputHiddenCATID.$inputHiddenEntypeId.$inputHiddenOptionValueId.$inputHiddenOptionId,
        ],
    ];
    $price = @$item['price'] ?? 0;
    $special_price = @$item['special_price'] ?? 0;
    $price = Price::formatNumber($price);
    $special_price = Price::formatNumber($special_price);

    $elementsPrice = [
       
        [
            'label' => Form::label('price', 'Giá niêm yết', ['class' => $formLabelAttr]),
            'element' => Form::text('price', $price, ['class' => $errors->first('price') ? $formInputAttr . ' is-invalid' : $formInputAttr,'onKeyUp'=>'this.value = FormatNumber(this.value);']),
            'error' => $errors->first('price') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('price')) : '',
        ],
        [
            'label' => Form::label('special_price', 'Giá giảm', ['class' => $formLabelAttr]),
            'element' => Form::text('special_price', $special_price, ['class' => $errors->first('special_price') ? $formInputAttr . ' is-invalid' : $formInputAttr,'onKeyUp'=>'this.value = FormatNumber(this.value);']),
            'error' => $errors->first('special_price') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('special_price')) : '',
        ],
        [
            'label' => Form::label('special_date', 'Thời gian giảm', ['class' => $formLabelAttr]),
            'element' => Form::text('special_date', $special_price_date, ['class' => $errors->first('special_date') ? $formInputAttr." option_special_date" . ' is-invalid' : $formInputAttr." option_special_date"]),
            'error' => $errors->first('special_date') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('special_date')) : '',
        ]
        
    ];
    $elementSeo = [
             [
                'label' => Form::label('alias', 'Url Key', ['class' => $formLabelAttr]),
                'element' => Form::text('alias', @$item['alias'], ['class' => $errors->first('alias') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
                'error' => $errors->first('alias') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('alias')) : '',
            ],
            [
                'label' => Form::label('title', 'Tiêu đề', ['class' => $formLabelAttr]),
                'element' => Form::text('title', @$content->title, ['class' => $errors->first('title') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
                'error' => $errors->first('title') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('title')) : '',
            ],
            [
                'label' => Form::label('keyword', 'Từ khóa', ['class' => $formLabelAttr]),
                'element' => Form::textarea('keyword', @$content->keyword, ['class' => $errors->first('keyword') ? $formInputAttr . ' is-invalid' : $formInputAttr,'rows' => 4, 'cols' => 54]),
                'error' => $errors->first('keyword') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('keyword')) : '',
            ],
            [
                'label' => Form::label('description', 'Mô tả', ['class' => $formLabelAttr]),
                'element' => Form::textarea('description', @$content->description, ['class' => $errors->first('description') ? $formInputAttr . ' is-invalid' : $formInputAttr,'rows' => 4, 'cols' => 54]),
                'error' => $errors->first('description') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('description')) : '',
            ],
        ];
    $elementContent = [      
            [
                'label' => Form::label('sort_content', 'Mô tả ngắn', ['class' => "col-12 font-weight-bold"]),
                'element' => Form::textarea('sort_content', @$content->sort_content, ['class' => $errors->first('sort_content') ? $formInputAttr . ' is-invalid' : $formInputAttr,'rows' => 4, 'cols' => 54]),
                'error' => $errors->first('sort_content') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('sort_content')) : '',
            ],
            [
                'label' => Form::label('content', 'Nội dung',  ['class' => "col-12 font-weight-bold"]),
                'element' => Form::textarea('content', @$content->content, ['class' => $errors->first('content') ? $formInputAttr . ' is-invalid' : $formInputAttr,'rows' => 4, 'cols' => 54]),
                'error' => $errors->first('content') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('content')) : '',
            ]
        ];
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
        @include ('admin.templates.notify')
        {{ Form::open([
            'method' => 'POST',
            'url' => route("$controllerName/save"),
            'accept-charset' => 'UTF-8',
            'enctype' => 'multipart/form-data',
            'id' => 'appForm',
        ]) }}
<div class="row">
    <div class="col-12 col-md-3 mb-3">
        <div class="nav flex-column nav-pills bg-white" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="border rounded-0 py-3 nav-link active" id="v-pills-config-tab" data-toggle="pill" data-target="#v-pills-config" type="button" role="tab" aria-controls="v-pills-config" aria-selected="true"><i class="font-l fa fa-cogs mr-2"></i>General</a>
            <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-price-tab" data-toggle="pill" data-target="#v-pills-price" type="button" role="tab" aria-controls="v-pills-price" aria-selected="true"><i class="font-l fa fa-dollar mr-2"></i>Advanced Pricing</a>
            <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-image-tab" data-toggle="pill" data-target="#v-pills-image" type="button" role="tab" aria-controls="v-pills-image" aria-selected="false"><i class="font-l fa fa-picture-o mr-2"></i>Images</a>
            <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-seo-tab" data-toggle="pill" data-target="#v-pills-seo" type="button" role="tab" aria-controls="v-pills-seo" aria-selected="false"><i class="font-l fa fa-gg mr-2"></i>Search Engine Optimization</a>
            <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-content-tab" data-toggle="pill" data-target="#v-pills-content" type="button" role="tab" aria-controls="-pills-content" aria-selected="false"><i class="font-l fa fa-clipboard mr-2"></i>Content</a>
            <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-options-tab" data-toggle="pill" data-target="#v-pills-options" type="button" role="tab" aria-controls="v-pills-options" aria-selected="false"><i class="font-l fa fa-list-alt mr-2"></i>Customizable Options</a>
            @if(count($listAttributeSet) > 0)
            <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-attributes-tab" data-toggle="pill" data-target="#v-pills-attributes" type="button" role="tab" aria-controls="v-pills-attributes" aria-selected="false"><i class="font-l fa fa-columns mr-2"></i>Attribute Sets</a>
            @endif
        </div>
    </div>
    <div class="col-12 col-md-9">
        <div class="tab-content p-4 border bg-white" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-config" role="tabpanel" aria-labelledby="v-pills-config-tab"><div class="w-75">{!! FormTemplate::show($elementsGeneral) !!}</div></div>
            <div class="tab-pane fade" id="v-pills-price" role="tabpanel" aria-labelledby="v-pills-price-tab"><div class="w-75">{!! FormTemplate::show($elementsPrice) !!}</div></div>
            <div class="tab-pane fade" id="v-pills-image" role="tabpanel" aria-labelledby="v-pills-image-tab">
                <div class="review-images">
                    <ul id="review" class="mb-0 mt-2">
                      @php
                           if(@isset($item->picture)){
                               $picture = json_decode($item->picture,true);
                               if (!empty($picture)){
                             @endphp
                               @foreach ($picture as $key => $val)
                        <li class="item  ui-state-default">
                            <div class="wp">
                                <img  src="{{asset($configPath['path']."/small/".$val)}}" height="100%" alt="">
                                <a class="delete fa fa-trash" onclick="doDeleteImg(this)"></a>
                                <input type='hidden' name='images[]' class='images' value="{{  $val }}"/>
                                <input type="hidden" name="image_dl[]" class="image_dl" value=""/>
                            </div>
                        </li>  
                                @endforeach
                             @php
                               }
                            }
                        @endphp
                        @include('admin.pages.product.plugins.image_upload')
                    </ul>
                </div>
            </div>
            <div class="tab-pane fade" id="v-pills-seo" role="tabpanel" aria-labelledby="v-pills-seo-tab"><div class="w-75">{!! FormTemplate::show($elementSeo) !!}</div></div>
            <div class="tab-pane fade" id="v-pills-content" role="tabpanel" aria-labelledby="v-pills-content-tab">{!! FormTemplate::show($elementContent) !!}</div>
            <div class="tab-pane fade" id="v-pills-options" role="tabpanel" aria-labelledby="v-pills-options-tab">
                <p><button type="button" id="add_option" class="btn btn-sm btn-success text-white"><i class="fa fa-plus-circle mr-2"></i>Add Option</button></p>
                <ul id="sortoptions">
                    @php
                        $option = @$item->options; 
                    @endphp
                    @if(isset($option) && count($option) > 0)
                        @foreach($option as $index => $item)
                            @include('admin.pages.product.plugins.option',['index'=>$index,'item'=>$item])
                        @endforeach
                    @endif
                </ul>
            </div>
            @if(count($listAttributeSet) > 0)
            <div class="tab-pane fade" id="v-pills-attributes" role="tabpanel" aria-labelledby="v-pills-attributes-tab">
                <div class="w-75">
                    @foreach($listAttributeSet as $item)
                    @php
                        $name = "attribute_sets[".$item->alias."][]";
                        $alias = $item->alias;
                        $label = Form::label($item->alias, $item->name, ['class' => $formLabelAttr]);
                        $element = Template::showAttributeSelector($item->attributes,$name,$alias,$data_attibute_sets);
                        echo $formControl = FormTemplate::formGroup(['label'=>$label,'element'=>$element],null);
                    @endphp
                   @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!--</div>-->
<!--</div>-->
    {{ Form::close() }}
<!--</div>-->
@endsection
@section("script")
<style>
    .review-images .item .icon-add i{font-size:25px}
</style>
<script src="{{asset('admin/ckeditor/build/ckeditor.js')}}"></script>
<script src="{{asset('admin/ckfinder/ckfinder.js')}}"></script>
<!-- Select2 -->
<link href="{{asset('admin/vendors/select2/dist/css/select2.min.css')}}" rel="stylesheet">
<script src="{{asset('admin/vendors/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    let sortContentEditor;
    let contentEditor;
    ClassicEditor
        .create(document.querySelector('#sort_content'),{
            toolbar: [ 'bold', 'italic', 'link', 'numberedList', 'bulletedList','insertTable']
        }).then( editor => {
            sortContentEditor = editor;
        }).catch(error => {
                console.error(error);
        });
    ClassicEditor
        .create(document.querySelector('#content'),{
                ckfinder: {
                        uploadUrl: '/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json',
//                        uploadUrl: '{{route('image.upload').'?_token='.csrf_token()}}',
                } 
        }).then( editor => {
            contentEditor = editor;
        })
        .catch(error => {
                console.error(error);
        });
</script>
<script>
    var arrOption = new Array();
    var arrOptionValue = new Array();
    var maxLengthUpload = 8;
    var str_app = `@include('admin.pages.product.plugins.image_upload')`;
    $(document).ready(function(){
       $(document).on('submit',"#appForm",function(){
            $("#sort_content").val(sortContentEditor.getData());
            $("#content").val(contentEditor.getData());
       });
        $("#review").sortable();
        $("#review").disableSelection();
        $("#sortoptions").sortable({
            connectWith: '.card-header',
            cancel: '.card-body'
        });
        sortTable();
        $('.colorrgba').colorpicker();
        $(".select2_multiple").select2({width:"100%"});
        $(document).on("change", ".btn-add-file", function () {
            var $selector = $(this);
            var count = $("#review .item").length - 1;
            var oFReader = new FileReader();
            oFReader.readAsDataURL($(this)[0].files[0]);
            oFReader.onload = function (oFREvent) {
                $selector.prev("img").attr("src", oFREvent.target.result)
//                $("#review .item img", count).attr("src", oFREvent.target.result);
            };
            var formID = $("#review form").length + 1;
            var token = '<input name="_token" type="hidden" value="{{csrf_token()}}">';
            $(".review-images .item.add-images .wp").wrapAll('<form method="post" action="" enctype="multipart/form-data" id="myform' + formID + '"></form>').append(token+"<input type='hidden' name='images[]' class='images'/><div class='progress-bar progress-bar-striped active' style='width: 100%'><span>Upload!</span></div>");
            doUpload("#myform" + formID);
            $("#myform" + formID).submit();
            $(this).after('<a class="delete fa fa-trash" onclick="callDelete(this)"></a>');
            $(this).prev(".icon-add").remove();
            $(this).prev("img").removeClass("d-none");
            $(this).addClass("hide");
            $(this).parents(".item").removeClass("add-images");
            if ((count + 1) < maxLengthUpload) {
                $("#review").append(str_app);
            } else {
                $("#error-max-upload").remove();
                $("#review").append('<div id="error-max-upload" class="col-md-12 no-padding-right"><p class="alert alert-danger"><i class="fa fa-warning"></i>&nbsp;Số lượng ảnh đạt tối đa!</p></div>');
            }
        });
    });
    
    function doUpload(formID) {
        $(document).on('submit', formID, (function (e) {
            e.preventDefault();
            var ac = '{{route('product.upload')}}';
            $.ajax({
                url: ac,
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    console.log("upload proccess");
                },
                success: function (f) {
                    $(formID).find("img").attr("src", f.url);
                    $(formID).find(".images").val(f.picture);
                    $(formID).find(".progress-bar").remove();
                    var clone = $(formID).find(".wp").clone();
                    $(formID).parents("li.item").empty().append(clone);
                }
            });
        }));
    }
    
    function doDeleteImg($this) {
        var elm = $($this).parents(".item").hide();
        $("#review .add-images").remove();
        $("#error-max-upload").remove();
        
        var img_name = elm.find(".images").val();
        elm.find(".images").remove();
        elm.find(".image_dl").val(img_name);
        
        var str_app = `@include('admin.pages.product.plugins.image_upload')`;
        $("#review").append(str_app);
    }
    function callDelete($this){
        var picture = $($this).nextAll(".images").val();
        var ac = '{{route('product.delete').'?_token='.csrf_token()}}';
        $.ajax({
            url: ac,
            type: "POST",
            data: {picture: picture},
            dataType: "json",
            beforeSend: function () {
                console.log("delete proccess");
            },
            success: function (f) {
                $($this).parents(".item").remove();
                $("#review .add-images").remove();
                $("#error-max-upload").remove();
                var str_app = `@include('admin.pages.product.plugins.image_upload')`;
                $("#review").append(str_app);
            }
        });
    }
</script>
<!-- js Options -->
<script>
   $(document).ready(function(){
        callbackDateRangePicker();
        $("#add_option").click(function(){
            var index = $("#sortoptions li").length;
            if(index<0) index = 0;
            var ac = '{{route('product/option')}}';
            $.ajax({
                url: ac,
                type: "GET",
                data: {index: index},
                dataType: "json",
                beforeSend: function () {
                    console.log("load option");
                },
                success: function (f) {
                    $("#sortoptions").append(f.html);
                }
            });
        });
         $(document).on('keyup',".option-title",function(){
            var value = $(this).val();
            var index = $(this).parents("li").data('index');
            if(value==""){
                value ="New Option";
            }
            $(".review-title-"+index).html(value);
             
        });
        $(document).on('change','.option-type',function(){
            var index = $(this).parents("li").data('index');
            var type = $(this).val();
            var ac = '{{route('product/attribute')}}';
            $.ajax({
                url: ac,
                type: "GET",
                data: {index: index,type:type},
                dataType: "json",
                beforeSend: function () {
                    console.log("load attribute");
                },
                success: function (f) {
                    $("#dataAttributes-"+index).html(f.html);
                    $('.colorrgba').colorpicker();
                    callbackDateRangePicker();
                }
            });
        });
        $(document).on('click','.add_option_value',function(){
            var index = $(this).parents("li").data('index');
            var type = $(this).data("type");
            var ac = '{{route('product/value')}}';
            $.ajax({
                url: ac,
                type: "GET",
                 data: {index: index,type:type},
                dataType: "json",
                beforeSend: function () {
                    console.log("load attribute value");
                },
                success: function (f) {
                    $("#loadValue-"+index).append(f.html);
                    $('.colorrgba').colorpicker();
                    callbackDateRangePicker();
                }
            });
        }); 
    });
    function callbackDateRangePicker(){
        $('.option_special_date').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('.option_special_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });

        $('.option_special_date').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    }
    function reviewImages(element){
        var oFReader = new FileReader();
        var $element = $(element).parent(".wp");
        oFReader.readAsDataURL($(element)[0].files[0]);
        oFReader.onload = function (oFREvent) {
            $element.find(".uploadPreview").removeClass("d-none").attr("src", oFREvent.target.result);
        };
        var formID = Date.now();
        var token = '<input name="_token" type="hidden" value="{{csrf_token()}}">';
        if ($element.find(".images")) {
            $element.wrapAll('<form method="post" action="" enctype="multipart/form-data" id="myoption' + formID + '"></form>').append(token + "<div class='progress-bar progress-bar-striped active' style='width: 100%'><span>Upload!</span></div>");
        }
        doUploadOption("#myoption" + formID);
        $("#myoption" + formID).submit();
     }
     function doUploadOption(formID) {
        $(document).on('submit', formID, (function (e) {
            e.preventDefault();
           var ac = '{{route('product.option')}}';
            $.ajax({
                url: ac,
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    console.log("upload picture var option proccess");
                },
                success: function (f) {
                    $(formID).find("img").attr("src", f.url);
                    $(formID).find(".images").val(f.picture);
                    $(formID).find(".progress-bar").remove();
                    var clone = $(formID).find(".wp").append(`<a class="delete fa fa-trash" onclick="callDeleteAttr(this,'` + f.picture + `')"></a>`).clone();
                    $(formID).parents("td").empty().append(clone);
                }
            });
        }));
    }
    function callDeleteAttr($this, picture) {
        var elm = $($this).parent(".wp");
         var ac = '{{route('product.option.delete').'?_token='.csrf_token()}}';
        jQuery.ajax({
            url: ac,
            type: 'post',
            data: {picture: picture},
            dataType: "json",
            beforeSend: function () {
                console.log("delete var picture option");
            },
            success: function (f) {
                if (f.status == true) {
                    elm.find(".uploadPreview").hide();
                    elm.find(".images").val("");
                    elm.find(".delete").remove();
                }
            }
        });
    }
    function doDeleteImgAttr($this) {
        var elm = $($this).hide().parents(".wp");
        
        elm.find("img").addClass("d-none");
    }
    function deleteOption($this, $value = 0){
        if($value!=0){
            if(arrOption.indexOf($value)< 0){
                arrOption.push($value);
                var text = arrOption.join(",");
                $("#option_de_id").val(text);
                $($this).parents("li").remove();
            }
        }else{
            $($this).parents("li").remove();
        }
    }
    function deleteOptionValue($this, $value = 0){
        if($value!=0){
            if(arrOptionValue.indexOf($value)< 0){
                arrOptionValue.push($value);
                var text = arrOptionValue.join(",");
                $("#option_value_id").val(text);
                 $($this).parents("tr").remove();
            }
        }else{
            $($this).parents("tr").remove();
        }
    }
    function sortTable(){
        $("#sortoptions table").each(function(){
            var id = $(this).attr('id');
            (new TableDnD).init(id);
        });
    }
</script>
@endsection
