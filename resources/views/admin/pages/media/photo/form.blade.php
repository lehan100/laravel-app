@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    use App\Helpers\Category as Category;
    $configPath = config('image.path.photo');
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $inputHiddenID = html()->hidden('id', @$item['id']);
    $inputHiddenRollback = html()->hidden('rollback', 0)->attributes( ['id' => 'rollback']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $var = ['0' => '--- Please Select ---'];
    $positionSellect = Category::generateDataSelector($positionSellector);
    $categorySellector = Category::generateDataSelector($categorySellector);
    $positionSellect = ($positionSellect) ?  $var + $positionSellect : $var;
    $categorySellector = ( $categorySellector)? $var + $categorySellector : $var;
    $picture = @$item->picture;
    $position = @$item->position;
    $controlClass = ($position && $position['mode']==2) ? 'category_tooogle' : 'category_tooogle d-none';
//echo "<pre>";
//print_r($item);
//die();
    $pictureUrl = (@$item->picture!="")? asset($configPath['path'].'/'.$picture) : "";
    $elementsGeneral = [
        [
            'label' => html()->label(for:'active', contents:'Duyệt')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->checkbox('status', @$item['status'], $status)->attributes( ['class' => 'js-switch']),
        ],
        [
            'label' => html()->label(for:'name', contents:'Tên '.$mainTitle)->attributes( ['class' => $formLabelAttr]),
            'element' => html()->text('name', @$item['name'])->attributes( ['class' => $errors->first('name') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('name') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('name')) : '',
        ],
        [
            'label' => html()->label(for:'alias_link', contents:'Liên kết')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->text('alias_link', @$item['alias_link'])->attributes( ['class' =>  $formInputAttr]),
        ],
        [
            'label' => html()->label(for:'position_id', contents:'Vị trí')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->select('position_id',$positionSellect, @$item['position_id'])->attributes( ['class' => $errors->first('position_id') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('position_id') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('position_id')) : '',
        ],
        [
            'label' => html()->label(for:'category_id', contents:'Danh mục')->attributes( ['class' => $formLabelAttr]),
            'control'=> $controlClass,
            'element' => html()->select('category_id',$categorySellector, @$item['category_id'])->attributes( ['class' => $errors->first('category_id') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('category_id') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('category_id')) : '',
        ],
        [
            'element' => $inputHiddenID . $inputHiddenRollback,
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
        </div>
    </div>
    <div class="col-12 col-md-9">
        <div class="tab-content p-4 border bg-white" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-config" role="tabpanel" aria-labelledby="v-pills-config-tab">
                <div class="w-75">
                    {!! FormTemplate::show($elementsGeneral) !!}
                    <div class="form-group row align-items-center">
                        <label for="category_id" class="col-form-label col-md-3 col-sm-3 label-align font-weight-bold">Hình ảnh</label>
                        <div class="col images-view border-0">
                            @if($picture != "")
                            <div class="wp" style="width: 50%;height: auto">
                                <img  class="uploadPreview" src="{{$pictureUrl}}" width="100%" alt="">
                                <div class="icon-add" style="display: none"><i class="fa fa-file-image-o"></i></div>
                                <a class="delete fa fa-trash" onclick="doDeleteImg(this)"></a>
                                <input class="btn-add-images" style="display: none" name="picture" type="file" onchange="reviewImages(this)" accept="image/png, image/gif, image/jpeg">
                                <input type="hidden" name="image" class="image" value="{{$picture}}"/>
                                <input type="hidden" name="image_dl" class="image_dl" value=""/>
                            </div>
                            @else
                            <div class="wp">
                                <img  class="uploadPreview d-none" src="{{$pictureUrl}}" width="100%" alt="">
                                <div class="icon-add"><i class="fa fa-file-image-o"></i></div>
                                <input class="btn-add-images" name="picture" type="file" onchange="reviewImages(this)">
                                <input type="hidden" name="image" class="image" value="{{$picture}}"/>
                            </div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
    {{ html()->form()->close() }}              
@endsection
@section("script")

<script>
    var positionSellect = new Array();
    @foreach($positionSellector as $item)
        positionSellect[{{$item['id']}}] = {{$item['mode']}};
    @endforeach
     $(document).ready(function(){
        $("#position_id").click(function(){
            var id = $(this).val();
            var mode = positionSellect[id];
            if(mode==2){
                $(".category_tooogle").removeClass("d-none");
            }else{
                $(".category_tooogle").addClass("d-none");
            }
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
            $element.wrapAll('<form method="post" action="" enctype="multipart/form-data" id="myPicture' + formID + '"></form>').append(token + "<div class='progress-bar progress-bar-striped active' style='width: 100%'><span>Upload!</span></div>");
        }
        doUpload("#myPicture" + formID);
        $("#myPicture" + formID).submit();
     }
     function doUpload(formID) {
        $(document).on('submit', formID, (function (e) {
            e.preventDefault();
           var ac = '{{route('post.upload')}}';
            $.ajax({
                url: ac,
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                beforeSend: function () {
                    console.log("upload pictureproccess");
                },
                success: function (f) {
                    $(formID)[0].reset();
                    $(formID).find("img").show().attr("src", f.url);
                    $(formID).find(".image").val(f.picture);
                    $(formID).find(".progress-bar").remove();
                    $(formID).find(".icon-add").hide();
                    $(formID).find(".btn-add-images").hide();
                    var clone = $(formID).find(".wp").css({width:'50%',height:'auto'}).append(`<a class="delete fa fa-trash" onclick="callDeleteAttr(this,'` + f.picture + `')"></a>`).clone();
                    $(formID).parents(".images-view").empty().append(clone);
                }
            });
        }));
    }
    function callDeleteAttr($this, picture) {
        var elm = $($this).parent(".wp").removeAttr("style");
         var ac = '{{route('post.delete').'?_token='.csrf_token()}}';
        jQuery.ajax({
            url: ac,
            type: 'post',
            data: {picture: picture},
            dataType: "json",
            beforeSend: function () {
                console.log("delete var picture");
            },
            success: function (f) {
                if (f.status == true) {
                    elm.find(".uploadPreview").hide();
                    elm.find(".images").val("");
                    elm.find(".delete").remove();
                    elm.find(".icon-add").show();
                    elm.find(".btn-add-images").show();
                }
            }
        });
    }
    function doDeleteImg($this) {
        var elm = $($this).hide().parents(".wp").removeAttr("style");
        var img_name = elm.find(".image").val();
        elm.find(".icon-add").show();
        elm.find(".btn-add-images").show();
        elm.find(".image").val("");
        elm.find(".image_dl").val(img_name);
        elm.find("img").addClass("d-none");
    }
</script>
@endsection
