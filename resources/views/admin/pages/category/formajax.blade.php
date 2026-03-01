@php
    use App\Helpers\Template as Template;
    use App\Helpers\Category as Category;
    use App\Helpers\Form as FormTemplate;
    $configPath = config('image.path.category');
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $inputHiddenID = html()->hidden('id', @$item['id']);

    $inputHiddenRollback = html()
        ->hidden('rollback', 0)
        ->attributes(['id' => 'rollback']);
    $inputHiddenEntypeId = html()->hidden('entype_id', @$item['entype_id']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $position_menu = isset($item['position_menu']) && $item['position_menu'] == 1 ? true : false;
    $position_top = isset($item['position_top']) && $item['position_top'] == 1 ? true : false;
    $position_main = isset($item['position_main']) && $item['position_main'] == 1 ? true : false;
    $position_footer_a = isset($item['position_footer_a']) && $item['position_footer_a'] == 1 ? true : false;
    $position_footer_b = isset($item['position_footer_b']) && $item['position_footer_b'] == 1 ? true : false;
    $var = ['0' => '+root'];
    $categorySellector = Category::generateDataSelector($categorySellector);
    if ($categorySellector) {
        $categorySellector = $var + $categorySellector;
    } else {
        $categorySellector = $var;
    }
    $content = @$item->contents;
    $picture = @$item->picture;
    $pictureUrl = @$item->picture != '' ? asset($configPath['path'] . '/' . $picture) : '';
    $parent_id = isset($item['parent_id']) ? $item['parent_id'] : $parent_id;
    $elementsGeneral = [
        [
            'label' => html()
                ->label(for: 'active', contents: 'Duyệt')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->checkbox('status', @$item['status'], $status)
                ->attributes(['class' => 'js-switch']),
        ],
        [
            'label' => html()
                ->label(for: 'name', contents: 'Tên ' . $mainTitle)
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('name', @$item['name'])
                ->attributes(['class' => $errors->first('name') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('name')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('name'))
                : '',
        ],
        [
            'control' => 'd-none',
            'label' => html()
                ->label(for: 'parent_id', contents: 'Cấp danh mục')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->select('parent_id', $categorySellector, $parent_id)
                ->attributes([
                    'class' => $errors->first('parent_id') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                ]),
            'error' => $errors->first('parent_id')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('parent_id'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'page', contents: 'Loại trang')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->select('page', Category::getDataPage(), @$item['page'])
                ->attributes(['class' => $errors->first('page') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('page')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('page'))
                : '',
        ],
        [
            'element' => $inputHiddenID . $inputHiddenRollback . $inputHiddenEntypeId,
        ],
    ];
    $elementsPosition = [
        [
            'label' => html()
                ->label(for: 'position_menu', contents: 'Menu Header')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->checkbox('position_menu', @$item['position_menu'], $position_menu)
                ->attributes(['class' => 'js-switch']),
        ],
        [
            'label' => html()
                ->label(for: 'position_top', contents: 'Menu Top')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->checkbox('position_top', @$item['position_top'], $position_top)
                ->attributes(['class' => 'js-switch']),
        ],
        [
            'label' => html()
                ->label(for: 'position_main', contents: 'Menu Center')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->checkbox('position_main', @$item['position_main'], $position_main)
                ->attributes(['class' => 'js-switch']),
        ],
        [
            'label' => html()
                ->label(for: 'position_footer_a', contents: 'Menu Footer (Block A)')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->checkbox('position_footer_a', @$item['position_footer_a'], $position_footer_a)
                ->attributes(['class' => 'js-switch']),
        ],
        [
            'label' => html()
                ->label(for: 'position_footer_b', contents: 'Menu Footer (Block B)')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->checkbox('position_footer_b', @$item['position_footer_b'], $position_footer_b)
                ->attributes(['class' => 'js-switch']),
        ],
    ];
    $elementSeo = [
        [
            'label' => html()
                ->label(for: 'alias', contents: 'Url Key')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('alias', @$item['alias'])
                ->attributes(['class' => $errors->first('alias') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('alias')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('alias'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'title', contents: 'Tiêu đề')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('title', @$content->title)
                ->attributes(['class' => $errors->first('title') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('title')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('title'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'keyword', contents: 'Từ khóa')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->textarea('keyword', @$content->keyword)
                ->attributes([
                    'class' => $errors->first('keyword') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                    'rows' => 4,
                    'cols' => 54,
                ]),
            'error' => $errors->first('keyword')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('keyword'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'description', contents: 'Mô tả')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->textarea('description', @$content->description)
                ->attributes([
                    'class' => $errors->first('description') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                    'rows' => 4,
                    'cols' => 54,
                ]),
            'error' => $errors->first('description')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('description'))
                : '',
        ],
    ];
@endphp
@include ('admin.templates.notify')
{{ html()->form('POST', route("$controllerName/save"))->attributes([
        'accept-charset' => 'UTF-8',
        'enctype' => 'multipart/form-data',
        'id' => 'appForm',
    ])->open() }}
<div id="accordion">
    <div class="card mb-3">
        <div class="card-header" id="headingOne">
            <h5 class="mb-0">
                <button class="btn btn-link font-weight-bold" type="button" data-toggle="collapse"
                    data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    General
                </button>
            </h5>
        </div>
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="panel-body py-4">
                <div class="w-75">
                    {!! FormTemplate::show($elementsGeneral) !!}
                    <div class="form-group row align-items-center">
                        <label for="category_id"
                            class="col-form-label col-md-3 col-sm-3 label-align font-weight-bold">Hình ảnh</label>
                        <div class="col images-view border-0">
                            @if ($picture != '')
                                <div class="wp">
                                    <img class="uploadPreview" src="{{ $pictureUrl }}" width="100%" alt="">
                                    <div class="icon-add" style="display: none"><i class="fa fa-file-image-o"></i></div>
                                    <a class="delete fa fa-trash" onclick="doDeleteImg(this)"></a>
                                    <input class="btn-add-images" style="display: none" name="picture" type="file"
                                        onchange="reviewImages(this)" accept="image/png, image/gif, image/jpeg">
                                    <input type="hidden" name="image" class="image" value="{{ $picture }}" />
                                    <input type="hidden" name="image_dl" class="image_dl" value="" />
                                </div>
                            @else
                                <div class="wp">
                                    <img class="uploadPreview d-none" src="{{ $pictureUrl }}" width="100%"
                                        alt="">
                                    <div class="icon-add"><i class="fa fa-file-image-o"></i></div>
                                    <input class="btn-add-images" name="picture" type="file"
                                        onchange="reviewImages(this)">
                                    <input type="hidden" name="image" class="image" value="{{ $picture }}" />
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed font-weight-bold" type="button" data-toggle="collapse"
                    data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Display Position
                </button>
            </h5>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
            <div class="card-body py-4">
                <div class="w-75">{!! FormTemplate::show($elementsPosition) !!}</div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header" id="headingThree">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed font-weight-bold" type="button" data-toggle="collapse"
                    data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Search Engine Optimization
                </button>
            </h5>
        </div>
        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body py-4">
                <div class="w-75">{!! FormTemplate::show($elementSeo) !!}</div>
            </div>
        </div>
    </div>
</div>
{{ html()->form()->close() }}
<script>
    function reviewImages(element) {
        var oFReader = new FileReader();
        var $element = $(element).parent(".wp");
        oFReader.readAsDataURL($(element)[0].files[0]);
        oFReader.onload = function(oFREvent) {
            $element.find(".uploadPreview").removeClass("d-none").attr("src", oFREvent.target.result);
        };
        var formID = Date.now();
        var token = '<input name="_token" type="hidden" value="{{ csrf_token() }}">';
        if ($element.find(".images")) {
            $element.wrapAll('<form method="post" action="" enctype="multipart/form-data" id="myPicture' + formID +
                '"></form>').append(token +
                "<div class='progress-bar progress-bar-striped active' style='width: 100%'><span>Upload!</span></div>"
                );
        }
        doUpload("#myPicture" + formID);
        $("#myPicture" + formID).submit();
    }

    function doUpload(formID) {
        $(document).on('submit', formID, (function(e) {
            e.preventDefault();
            var ac = '{{ route('category.upload') }}';
            $.ajax({
                url: ac,
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    console.log("upload pictureproccess");
                },
                success: function(f) {
                    $(formID)[0].reset();
                    $(formID).find("img").show().attr("src", f.url);
                    $(formID).find(".image").val(f.picture);
                    $(formID).find(".progress-bar").remove();
                    $(formID).find(".icon-add").hide();
                    $(formID).find(".btn-add-images").hide();
                    var clone = $(formID).find(".wp").append(
                        `<a class="delete fa fa-trash" onclick="callDeleteAttr(this,'` + f
                        .picture + `')"></a>`).clone();
                    $(formID).parents(".images-view").empty().append(clone);
                }
            });
        }));
    }

    function callDeleteAttr($this, picture) {
        var elm = $($this).parent(".wp");
        var ac = '{{ route('category.delete') . '?_token=' . csrf_token() }}';
        jQuery.ajax({
            url: ac,
            type: 'post',
            data: {
                picture: picture
            },
            dataType: "json",
            beforeSend: function() {
                console.log("delete var picture");
            },
            success: function(f) {
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
