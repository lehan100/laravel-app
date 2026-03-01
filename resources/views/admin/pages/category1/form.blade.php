@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Category as Category;
    use App\Helpers\Form as FormTemplate;
    $configPath = config('image.path.category');
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $inputHiddenID = Form::hidden('id', @$item['id']);
    $inputHiddenRollback = Form::hidden('rollback', 0, ['id' => 'rollback']);
    $inputHiddenEntypeId = Form::hidden('entype_id', @$item['entype_id']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $var = ['0' => '+root'];
    $categorySellector = Category::generateDataSelector($categorySellector);
    if($categorySellector){
         $categorySellector = $var + $categorySellector;
    }else{
        $categorySellector = $var;
    }
    $content = @$item->contents;
    $picture = @$item->picture;
    $pictureUrl = (@$item->picture!="")? asset($configPath['path'].'/'.$picture) : "";
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
            'label' => Form::label('parent_id', 'Cấp danh mục', ['class' => $formLabelAttr]),
            'element' => Form::select('parent_id',$categorySellector, @$item['parent_id'], ['class' => $errors->first('parent_id') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('parent_id') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('parent_id')) : '',
        ],
        [
            'label' => Form::label('page', 'Loại trang', ['class' => $formLabelAttr]),
            'element' => Form::select('page', Category::getDataPage(), @$item['page'], ['class' => $errors->first('page') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('page') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('page')) : '',
        ],
        [
            'element' => $inputHiddenID . $inputHiddenRollback.$inputHiddenEntypeId,
        ],
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
            <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-seo-tab" data-toggle="pill" data-target="#v-pills-seo" type="button" role="tab" aria-controls="v-pills-seo" aria-selected="false"><i class="font-l fa fa-gg mr-2"></i>Search Engine Optimization</a>
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
                            <div class="wp">
                                <img  class="uploadPreview" src="{{$pictureUrl}}" width="100%" alt="">
                                <div class="icon-add" style="display: none"><i class="fa fa-file-image-o"></i></div>
                                <a class="delete fa fa-trash" onclick="doDeleteImg(this)"></a>
                                <input class="btn-add-images" style="display: none" name="picture" type="file" onchange="reviewImages(this)">
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
            <div class="tab-pane fade" id="v-pills-seo" role="tabpanel" aria-labelledby="v-pills-seo-tab"><div class="w-75">{!! FormTemplate::show($elementSeo) !!}</div></div>
        </div>
    </div>
</div>
    {{ Form::close() }}
@endsection
@section("script")
<script src="{{asset('admin/ckeditor/build/ckeditor.js')}}"></script>
<script src="{{asset('admin/ckfinder/ckfinder.js')}}"></script>
<script>
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
           var ac = '{{route('category.upload')}}';
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
                    var clone = $(formID).find(".wp").append(`<a class="delete fa fa-trash" onclick="callDeleteAttr(this,'` + f.picture + `')"></a>`).clone();
                    $(formID).parents(".images-view").empty().append(clone);
                }
            });
        }));
    }
    function callDeleteAttr($this, picture) {
        var elm = $($this).parent(".wp");
         var ac = '{{route('category.delete').'?_token='.csrf_token()}}';
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