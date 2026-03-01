@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    $configPath = config('image.path.post');
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $inputHiddenID = html()->hidden('id', @$item['id']);
    $inputHiddenOptionId = html()->hidden('attribute_set_de_id', '')->attributes(["id"=>"attribute_set_de_id"]);
    $inputHiddenRollback = html()->hidden('rollback', 0)->attributes( ['id' => 'rollback']);
    $inputHidden_attribute_set_alias = html()->hidden('attribute_set_alias_old', @$item['alias']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $elementsGeneral = [
        [
            'label' => html()->label(for:'active', contents:'Duyệt')->attributes(['class' => $formLabelAttr]),
            'element' => html()->checkbox('attribute_set_status', @$item['status'], $status)->attributes( ['class' => 'js-switch']),
        ],
        [
            'label' => html()->label(for:'name', contents:'Tên '.$mainTitle)->attributes( ['class' => $formLabelAttr]),
            'element' => html()->text('attribute_set_name', @$item['name'])->attributes( ['class' => $errors->first('attribute_set_name') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('attribute_set_name') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('attribute_set_name')) : '',
        ],
        [
            'label' => html()->label(for:'alias', contents:'Code')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->text('attribute_set_alias', @$item['alias'])->attributes( ['class' => $errors->first('attribute_set_alias') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('attribute_set_alias') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('attribute_set_alias')) : '',
        ],
        [
            'element' => $inputHiddenID . $inputHiddenRollback.$inputHiddenOptionId.$inputHidden_attribute_set_alias,
        ],
    ];
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
        @include ('admin.templates.notify')
        {{ html()->form('POST', route("$controllerName/save"))->attributes([
                        'accept-charset' => 'UTF-8',
                        'enctype' => 'multipart/form-data',
                        'id' => 'appForm',
                    ])->open() }}
<div class="row">
    <div class="col-12 col-md-3 mb-3">
        <div class="nav flex-column nav-pills bg-white" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <a class="border rounded-0 py-3 nav-link active" id="v-pills-config-tab" data-toggle="pill" data-target="#v-pills-config" type="button" role="tab" aria-controls="v-pills-config" aria-selected="true"><i class="font-l fa fa-cogs mr-2"></i>General</a>
            <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-content-tab" data-toggle="pill" data-target="#v-pills-content" type="button" role="tab" aria-controls="-pills-content" aria-selected="false"><i class="font-l fa fa-clipboard mr-2"></i>Attribute Values</a>
        </div>
    </div>
    <div class="col-12 col-md-9">
        <div class="tab-content p-4 border bg-white" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-config" role="tabpanel" aria-labelledby="v-pills-config-tab">
                <div class="w-75">
                    {!! FormTemplate::show($elementsGeneral) !!}
                </div>
            </div>
            <div class="tab-pane fade" id="v-pills-content" role="tabpanel" aria-labelledby="v-pills-content-tab">
                @include('admin.pages.attribute.plugins.option')
            </div>
        </div>
    </div>
</div>
    {{ html()->form()->close() }}              
@endsection
@section("script")
<script>
    var arrOptionValue = new Array();
    $(document).ready(function(){
        $('.colorrgba').colorpicker();
       $(document).on('change','.option-type',function(){
            var type = $(this).val();
            var ac = '{{route('attribute/attribute')}}';
            $.ajax({
                url: ac,
                type: "GET",
                data: {type:type,item:'{!!@json_encode($item)!!}'},
                dataType: "json",
                beforeSend: function () {
                    console.log("load attribute");
                },
                success: function (f) {
                    $("#dataAttributesValue").html(f.html);
                    $('.colorrgba').colorpicker();
                }
            });
        });
        $(document).on('click','.add_option_value',function(){
            var type = $(this).data("type");
            var ac = '{{route('attribute/value')}}';
            $.ajax({
                url: ac,
                type: "GET",
                 data: {type:type},
                dataType: "json",
                beforeSend: function () {
                    console.log("load attribute value");
                },
                success: function (f) {
                    $("#loadValue").append(f.html);
                    $('.colorrgba').colorpicker();
                }
            });
        }); 
    });
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
           var ac = '{{route('attribute.option')}}';
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
         var ac = '{{route('attribute.option.delete').'?_token='.csrf_token()}}';
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
        var img = elm.find(".images").val();
        elm.find(".images").val('');
        elm.find("img").addClass("d-none");
        elm.find(".picture_del").val(img);
    }
    function deleteOptionValue($this, $value = 0){
        if($value!=0){
            if(arrOptionValue.indexOf($value)< 0){
                arrOptionValue.push($value);
                var text = arrOptionValue.join(",");
                $("#attribute_set_de_id").val(text);
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
<style>
.images-option .icon-add {
    display: list-item;
}
.images-option .wp img, .images-view .wp img{
    margin-top: 15px;
    border:none;
}
.images-option .wp, .images-view .wp{
    background:#fff;
    border: 1px solid #ddd;
}
</style>
@endsection
