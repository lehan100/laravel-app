@extends('admin.layouts.default')
@php
    use Illuminate\Support\Arr;
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    use Illuminate\Support\Carbon;
    use App\Helpers\Price as Price;
    use App\Helpers\Category as CategoryHelper;
    $configPath = config('image.path.product');
    $configPathOption = config('image.path.product_option');
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $inputHiddenID = html()->hidden('id', @$item['id']);
    $inputHiddenEntypeId = html()->hidden('entype_id', @$item['entype_id']);
    $inputHiddenOptionId = html()
        ->hidden('option_de_id', '')
        ->attributes(['id' => 'option_de_id']);
    $OptionEntriesId = @isset($item->option_entries)
        ? Arr::join(Arr::pluck($item->option_entries->toArray(), 'id'), ',')
        : '';
    $inputHiddenOptionEntriesId = html()
        ->hidden('dataOptionEntries', $OptionEntriesId)
        ->attributes(['id' => 'dataOptionEntries']);
    $inputHiddenOptionValueId = html()
        ->hidden('option_value_id', '')
        ->attributes(['id' => 'option_value_id']);
    $categoryIDOLD = @isset($item->category) ? json_encode($item->category->toArray()) : null;
    $content = @$item->contents;
    $inputHiddenCATID = html()->hidden('category_old', @$categoryIDOLD);
    $inputHiddenRollback = html()
        ->hidden('rollback', 0)
        ->attributes(['id' => 'rollback']);
    $quantity = isset($item['quantity']) ? $item['quantity'] : 0;
    $dataStock = ['' => '--- Please Select ---', '0' => 'Hết hàng', '1' => 'Còn hàng'];

    $weight = isset($item['weight']) ? $item['weight'] : 0;
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $use_coupon = isset($item['use_coupon']) && $item['use_coupon'] == 1 ? true : false;
    $data_attibute_sets = @$item->attibute_sets;
    $catSellector = new CategoryHelper()->generateSelector(@$categorySellector, @$item->category);
    //    $special_price_from = @Carbon::parse($item->special_price_from)->format('m/d/Y');
    //    $special_price_to = @Carbon::parse($item->special_price_to)->format('m/d/Y');
    $special_price_date = '';
    if (
        isset($item->special_price_from) &&
        isset($item->special_price_to) &&
        $item->special_price_from &&
        $item->special_price_to
    ) {
        $special_price_from = @Carbon::parse($item->special_price_from)->format('m/d/Y');
        $special_price_to = @Carbon::parse($item->special_price_to)->format('m/d/Y');
        $special_price_date = $special_price_from . ' - ' . $special_price_to;
    }
    //    print_r($catSellector);die();
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
                ->label(for: 'use_coupon', contents: 'Mã giảm giá')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->checkbox('use_coupon', @$item['use_coupon'], $use_coupon)
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
            'label' => html()
                ->label(for: 'sku', contents: 'Mã ' . $mainTitle)
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('sku', @$item['sku'])
                ->attributes(['class' => $errors->first('sku') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('sku')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('sku'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'quantity', contents: 'Số lượng')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('quantity', $quantity)
                ->attributes(['class' => $errors->first('quantity') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('quantity')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('quantity'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'name', contents: 'Tình trạng')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->select('stock', $dataStock, @$item['stock'])
                ->attributes(['class' => $errors->first('stock') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('stock')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('stock'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'weight', contents: 'Trọng lượng (g)')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('weight', $weight)
                ->attributes(['class' => $errors->first('weight') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('weight')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('weight'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'cat_id', contents: 'Danh mục')
                ->attributes(['class' => $formLabelAttr]),
            'element' => $catSellector,
        ],
        [
            'element' =>
                $inputHiddenID .
                $inputHiddenRollback .
                $inputHiddenCATID .
                $inputHiddenEntypeId .
                $inputHiddenOptionValueId .
                $inputHiddenOptionEntriesId .
                $inputHiddenOptionId,
        ],
    ];
    $price = @$item['price'] ?? 0;
    $special_price = @$item['special_price'] ?? 0;
    $price = Price::formatNumber($price);
    $special_price = Price::formatNumber($special_price);

    $elementsPrice = [
        [
            'label' => html()
                ->label(for: 'price', contents: 'Giá niêm yết')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('price', $price)
                ->attributes([
                    'class' => $errors->first('price') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                    'onKeyUp' => 'this.value = FormatNumber(this.value);',
                ]),
            'error' => $errors->first('price')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('price'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'special_price', contents: 'Giá giảm')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('special_price', $special_price)
                ->attributes([
                    'class' => $errors->first('special_price') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                    'onKeyUp' => 'this.value = FormatNumber(this.value);',
                ]),
            'error' => $errors->first('special_price')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('special_price'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'special_date', contents: 'Thời gian giảm')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('special_date', $special_price_date)
                ->attributes([
                    'class' => $errors->first('special_date')
                        ? $formInputAttr . ' option_special_date' . ' is-invalid'
                        : $formInputAttr . ' option_special_date',
                ]),
            'error' => $errors->first('special_date')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('special_date'))
                : '',
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
    $elementContent = [
        [
            'label' => html()
                ->label(for: 'sort_content', contents: 'Mô tả ngắn')
                ->attributes(['class' => 'col-12 font-weight-bold']),
            'element' => html()
                ->textarea('sort_content', @$content->sort_content)
                ->attributes([
                    'class' => $errors->first('sort_content') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                    'rows' => 4,
                    'cols' => 54,
                ]),
            'error' => $errors->first('sort_content')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('sort_content'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'content', contents: 'Nội dung')
                ->attributes(['class' => 'col-12 font-weight-bold']),
            'element' => html()
                ->textarea('content', @$content->content)
                ->attributes([
                    'class' => $errors->first('content') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                    'rows' => 4,
                    'cols' => 54,
                ]),
            'error' => $errors->first('content')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('content'))
                : '',
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
                <a class="border rounded-0 py-3 nav-link active" id="v-pills-config-tab" data-toggle="pill"
                    data-target="#v-pills-config" type="button" role="tab" aria-controls="v-pills-config"
                    aria-selected="true"><i class="font-l fa fa-cogs mr-2"></i>General</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-price-tab" data-toggle="pill"
                    data-target="#v-pills-price" type="button" role="tab" aria-controls="v-pills-price"
                    aria-selected="true"><i class="font-l fa fa-money mr-2"></i>Advanced Pricing</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-image-tab" data-toggle="pill"
                    data-target="#v-pills-image" type="button" role="tab" aria-controls="v-pills-image"
                    aria-selected="false"><i class="font-l fa fa-picture-o mr-2"></i>Images</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-seo-tab" data-toggle="pill"
                    data-target="#v-pills-seo" type="button" role="tab" aria-controls="v-pills-seo"
                    aria-selected="false"><i class="font-l fa fa-gg mr-2"></i>Search Engine Optimization</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-content-tab" data-toggle="pill"
                    data-target="#v-pills-content" type="button" role="tab" aria-controls="-pills-content"
                    aria-selected="false"><i class="font-l fa fa-clipboard mr-2"></i>Content</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-options-tab" data-toggle="pill"
                    data-target="#v-pills-options" type="button" role="tab" aria-controls="v-pills-options"
                    aria-selected="false"><i class="font-l fa fa-list-alt mr-2"></i>Customizable Options</a>
                @if (count($listAttributeSet) > 0)
                    <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-attributes-tab" data-toggle="pill"
                        data-target="#v-pills-attributes" type="button" role="tab" aria-controls="v-pills-attributes"
                        aria-selected="false"><i class="font-l fa fa-columns mr-2"></i>Attribute Sets</a>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="tab-content p-4 border bg-white" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-config" role="tabpanel"
                    aria-labelledby="v-pills-config-tab">
                    <div class="w-75">{!! FormTemplate::show($elementsGeneral) !!}</div>
                </div>
                <div class="tab-pane fade" id="v-pills-price" role="tabpanel" aria-labelledby="v-pills-price-tab">
                    <div class="w-75">{!! FormTemplate::show($elementsPrice) !!}</div>
                </div>
                <div class="tab-pane fade" id="v-pills-image" role="tabpanel" aria-labelledby="v-pills-image-tab">
                    <div class="review-images">
                        <ul id="review" class="mb-0 mt-2">
                            @if (@isset($item->picture))
                                @php
                                    $picture = json_decode($item->picture, true);
                                @endphp
                                @if (!empty($picture))
                                    @foreach ($picture as $key => $val)
                                        <li class="item ui-state-default">
                                            <div class="wp">
                                                <img src="{{ asset($configPath['path'] . '/small/' . $val) }}"
                                                    height="100%" alt="">
                                                <a class="delete fa fa-trash" onclick="doDeleteImg(this)"></a>
                                                <input type='hidden' name='images[]' class='images'
                                                    value="{{ $val }}" />
                                                <input type="hidden" name="image_dl[]" class="image_dl"
                                                    value="" />
                                            </div>
                                        </li>
                                    @endforeach
                                    @php
                                    @endphp
                                @endif
                            @endif
                            @include('admin.pages.product.plugins.image_upload')
                        </ul>
                    </div>
                </div>
                <div class="tab-pane fade" id="v-pills-seo" role="tabpanel" aria-labelledby="v-pills-seo-tab">
                    <div class="w-75">{!! FormTemplate::show($elementSeo) !!}</div>
                </div>
                <div class="tab-pane fade" id="v-pills-content" role="tabpanel" aria-labelledby="v-pills-content-tab">
                    {!! FormTemplate::show($elementContent) !!}</div>
                <div class="tab-pane fade" id="v-pills-options" role="tabpanel" aria-labelledby="v-pills-options-tab">
                    <div class="p-3 bg-light border rounded mb-4">
                        <button type="button" id="add_option_entries" class="btn btn-sm btn-info text-white mb-0"><i
                                class="fa fa-plus-circle mr-2"></i><strong>Modify Option Entries</strong></button>
                        <button type="button" id="add_option" class="btn btn-sm btn-success text-white mb-0"><i
                                class="fa fa-plus-circle mr-2"></i>Add Option</button>
                    </div>
                    <ul id="loadOptionEntries"></ul>
                    <ul id="sortoptions">
                        @php
                            $option = @$item->options;
                        @endphp
                        @if (isset($option) && count($option) > 0)
                            @foreach ($option as $index => $item)
                                @include('admin.pages.product.plugins.option', [
                                    'index' => $index,
                                    'item' => $item,
                                ])
                            @endforeach
                        @endif
                    </ul>
                </div>
                @if (count($listAttributeSet) > 0)
                    <div class="tab-pane fade" id="v-pills-attributes" role="tabpanel"
                        aria-labelledby="v-pills-attributes-tab">
                        <div class="w-75">
                            @foreach ($listAttributeSet as $item)
                                @php
                                    $name = 'attribute_sets[' . $item->alias . '][]';
                                    $alias = $item->alias;
                                    $label = html()
                                        ->label($item->alias, $item->name)
                                        ->attributes(['class' => $formLabelAttr]);
                                    $element = Template::showAttributeSelector(
                                        $item->attributes,
                                        $name,
                                        $alias,
                                        $data_attibute_sets,
                                    );
                                    echo $formControl = FormTemplate::formGroup(
                                        ['label' => $label, 'element' => $element],
                                        null,
                                    );
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
    {{ html()->form()->close() }}
    <!--</div>-->
@endsection
@section('script')
    <style>
        .review-images .item .icon-add i {
            font-size: 30px
        }

        .add-images .spin {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: red;
            animation: spin 1s ease-in-out infinite;
            -webkit-animation: spin 1s ease-in-out infinite;
            position: absolute;
            left: 50%;
            top: 50%;
            margin-left: -25px;
            margin-top: -25px;
        }

        .add-images .spinner::before {
            content: "";
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            background: rgba(0, 0, 0, .7)
        }

        @keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @-webkit-keyframes spin {
            to {
                -webkit-transform: rotate(360deg);
            }
        }

        .accordion .panel {
            margin-bottom: 5px;
            border-radius: 0;
            border-bottom: 1px solid #efefef
        }

        .accordion .panel .panel-body {
            padding: 10px;
            background: #F2F5F7;

        }

        .accordion .panel-heading {
            background: #d0dae1;
            padding: 13px;
            width: 100%;
            display: block;
            position: relative;
        }

        .accordion .panel:hover {
            background: #F2F5F7
        }

        .panel-heading a.panel-title::after {
            position: absolute;
            right: 10px;
            width: 1.25rem;
            height: 1.25rem;
            margin-left: auto;
            content: "";
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-size: 1.25rem;
            transition: transform .2s ease-in-out;
        }

        .panel-heading a.panel-title:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230c63e4'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            transform: rotate(-180deg);
        }

        .box-color {
            width: 40px;
            height: 40px;
        }
    </style>
    <script src="{{ asset('admin/ckeditor/build/ckeditor.js') }}"></script>
    @include('ckfinder::setup')
    <!-- Select2 -->
    <link href="{{ asset('admin/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('admin/vendors/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        let sortContentEditor;
        let contentEditor;
        ClassicEditor
            .create(document.querySelector('#sort_content'), {
                toolbar: ['bold', 'italic', 'link', 'numberedList', 'bulletedList', 'insertTable']
            }).then(editor => {
                sortContentEditor = editor;
            }).catch(error => {
                console.error(error);
            });
        ClassicEditor
            .create(document.querySelector('#content'), {
                ckfinder: {
                    uploadUrl: '/admin/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json',
                    //                        uploadUrl: '{{ route('image.upload') . '?_token=' . csrf_token() }}',
                }
            }).then(editor => {
                contentEditor = editor;
            })
            .catch(error => {
                console.error(error);
            });
    </script>
    <script>
        var arrOption = new Array();
        var arrOptionValue = new Array();
        var str_app = `@include('admin.pages.product.plugins.image_upload')`;

        const ImageUpload = {
            selector: {
                input: '.btn-add-file',
                review: "#review"
            },
            maxLengthUpload: 20,
            doUpload: function(files, index, length = 0) {
                if (length > 0 && index < length) {
                    var file = files[index];
                    var token = '{{ csrf_token() }}';
                    var form = new FormData();
                    form.append('_token', token);
                    form.append('picture', file);
                    console.log(form);
                    var ac = '{{ route('product.upload') }}';
                    var id = $(this.selector.review + " .item").length + 1;
                    var str_warning = `<li class="item add-images" id="${id}">
                                    <div class="wp">
                                    <div class="spinner">
                                        <div class="spin"></div>
                                    </div>
                                    <div class="icon-add"><i class="fa fa-file-image-o"></i></div>
                                    </div>
                                </li>`;
                    $(this.selector.review).append(str_warning);
                    $.ajax({
                        url: ac,
                        type: "POST",
                        data: form,
                        processData: false,
                        contentType: false,
                        cache: false,
                        //processType: false,
                        dataType: "json",
                        // xhr: function () {
                        //     var xhr = new window.XMLHttpRequest();
                        //     xhr.upload.addEventListener("progress", function (evt) {
                        //         if (evt.lengthComputable) {
                        //             var percentComplete = evt.loaded / evt.total;
                        //             percentComplete = parseInt(percentComplete * 100);
                        //         }
                        //     }, false);
                        //     return xhr;
                        // },
                        success: function(f) {
                            if (f.status == true) {
                                let str_update = `
                                <img  src="${f.url}" height="100%" alt="">
                                <a class="delete fa fa-trash"></a>
                                <input type='hidden' name='images[]' class='images' value="${f.picture}"/>
                                <input type="hidden" name="image_dl[]" class="image_dl" value=""/>
                       `;
                                $("#" + id).addClass("ui-sortable-handle").find(".wp").html(str_update);
                            }
                            ImageUpload.doUpload(files, ++index, length);
                        }
                    });
                } else {
                    $(this.selector.review).append(str_app);
                }
            },
            doDelete: function($this) {
                var elm = $($this).parents(".item").hide();
                var img_name = elm.find(".images").val();
                $("#error-max-upload").remove();
                elm.find(".images").remove();
                elm.find(".image_dl").val(img_name);
            },
            init: function() {
                $(document).on("change", this.selector.input, function() {
                    let files = $(this)[0].files;
                    $(ImageUpload.selector.review + " .elm-upload").remove();
                    ImageUpload.doUpload(files, 0, files.length);
                });
                $(document).on("click", this.selector.review + " .item a.delete", function() {
                    ImageUpload.doDelete(this);
                });
            }
        }

        $(document).ready(function() {
            ImageUpload.init();
            $(document).on('submit', "#appForm", function() {
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
            $(".select2_multiple").select2({
                width: "100%"
            });
        });
    </script>
    <!-- js Options -->
    <script>
        $(document).ready(function() {
            callbackDateRangePicker();
            $("#add_option").click(function() {
                var index = $("#sortoptions li").length;
                if (index < 0) index = 0;
                var ac = '{{ route('product/option') }}';
                $.ajax({
                    url: ac,
                    type: "GET",
                    data: {
                        index: index
                    },
                    dataType: "json",
                    beforeSend: function() {
                        console.log("load option");
                    },
                    success: function(f) {
                        $("#sortoptions").append(f.html);
                    }
                });
            });

            $(document).on('keyup', ".option-title", function() {
                var value = $(this).val();
                var index = $(this).parents("li").data('index');
                if (value == "") {
                    value = "New Option";
                }
                $(".review-title-" + index).html(value);

            });
            $(document).on('change', '.option-type', function() {
                var index = $(this).parents("li").data('index');
                var type = $(this).val();
                var ac = '{{ route('product/attribute') }}';
                $.ajax({
                    url: ac,
                    type: "GET",
                    data: {
                        index: index,
                        type: type
                    },
                    dataType: "json",
                    beforeSend: function() {
                        console.log("load attribute");
                    },
                    success: function(f) {
                        $("#dataAttributes-" + index).html(f.html);
                        $('.colorrgba').colorpicker();
                        callbackDateRangePicker();
                    }
                });
            });
            $(document).on('click', '.add_option_value', function() {
                var index = $(this).parents("li").data('index');
                var type = $(this).data("type");
                var ac = '{{ route('product/value') }}';
                $.ajax({
                    url: ac,
                    type: "GET",
                    data: {
                        index: index,
                        type: type
                    },
                    dataType: "json",
                    beforeSend: function() {
                        console.log("load attribute value");
                    },
                    success: function(f) {
                        $("#loadValue-" + index).append(f.html);
                        $('.colorrgba').colorpicker();
                        callbackDateRangePicker();
                    }
                });
            });
        });

        function callbackDateRangePicker() {
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
                $element.wrapAll('<form method="post" action="" enctype="multipart/form-data" id="myoption' + formID +
                    '"></form>').append(token +
                    "<div class='progress-bar progress-bar-striped active' style='width: 100%'><span>Upload!</span></div>"
                );
            }
            doUploadOption("#myoption" + formID);
            $("#myoption" + formID).submit();
        }

        function doUploadOption(formID) {
            $(document).on('submit', formID, (function(e) {
                e.preventDefault();
                var ac = '{{ route('product.option') }}';
                $.ajax({
                    url: ac,
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: "json",
                    beforeSend: function() {
                        console.log("upload picture var option proccess");
                    },
                    success: function(f) {
                        $(formID).find("img").attr("src", f.url);
                        $(formID).find(".images").val(f.picture);
                        $(formID).find(".progress-bar").remove();
                        var clone = $(formID).find(".wp").append(
                            `<a class="delete fa fa-trash" onclick="callDeleteAttr(this,'` + f
                            .picture + `')"></a>`).clone();
                        $(formID).parents("td").empty().append(clone);
                    }
                });
            }));
        }

        function callDeleteAttr($this, picture) {
            var elm = $($this).parent(".wp");
            var ac = '{{ route('product.option.delete') . '?_token=' . csrf_token() }}';
            jQuery.ajax({
                url: ac,
                type: 'post',
                data: {
                    picture: picture
                },
                dataType: "json",
                beforeSend: function() {
                    console.log("delete var picture option");
                },
                success: function(f) {
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

        function deleteOption($this, $value = 0) {
            if ($value != 0) {
                if (arrOption.indexOf($value) < 0) {
                    arrOption.push($value);
                    var text = arrOption.join(",");
                    $("#option_de_id").val(text);
                    $($this).parents("li").remove();
                }
            } else {
                $($this).parents("li").remove();
            }
        }

        function deleteOptionValue($this, $value = 0) {
            if ($value != 0) {
                if (arrOptionValue.indexOf($value) < 0) {
                    arrOptionValue.push($value);
                    var text = arrOptionValue.join(",");
                    $("#option_value_id").val(text);
                    $($this).parents("tr").remove();
                }
            } else {
                $($this).parents("tr").remove();
            }
        }

        function sortTable() {
            $("#sortoptions table").each(function() {
                var id = $(this).attr('id');
                (new TableDnD).init(id);
            });
        }
        //Option Entries
        var optionEntries = {
            selector: {
                buttonNew: "#add_option_entries",
                loadData: "#loadOptionEntries",
                buttonDelete: ".btn-delete-optionEntries",
                modal: {
                    popup: "#modaloptionEntries",
                    loadData: "#loadData",
                    checkbox: ".statusOptionEntries",
                    save: ".modalSaveOptionEntries"
                },
            },
            data: {
                fullData: [],
                dataOption: [],
                dataOptionTemp: [],
                input: "#dataOptionEntries"
            },
            Loading: {
                show: function() {
                    $("#loading").show();
                },
                hide: function() {
                    $("#loading").hide();
                }
            },
            getListData: function(modal = true) {
                var ac = '{{ route('product/optionEntries') }}';
                var data = null;
                $.ajax({
                    url: ac,
                    type: "GET",
                    data: {},
                    dataType: "json",
                    beforeSend: function() {
                        console.log("load option Entries");
                        if (modal == true) {
                            optionEntries.Loading.show();
                        }

                    },
                    success: function(f) {
                        optionEntries.Loading.hide();
                        if (f.status == true) {
                            data = f.data;
                            optionEntries.data.fullData = f.data;
                            if (modal == true) {
                                optionEntries.Modal.showListSelect(data);
                            }
                            if (modal == false) {
                                optionEntries.generateData();
                            }
                        }

                    }
                });
            },
            Modal: {
                showListSelect: function(data = null) {
                    if (data != null && data.length > 0) {
                        if (optionEntries.data.dataOption.length > 0) {
                            optionEntries.data.dataOptionTemp = optionEntries.data.dataOption;
                        }
                        optionEntries.Modal.generateHTML(data);
                        $(optionEntries.selector.modal.popup).modal("show");
                    }
                },
                generateHTML: function(data) {
                    var xhtml = `<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">`;
                    $.each(data, function(i, e) {
                        var id = e.id;
                        var index = optionEntries.data.dataOptionTemp.indexOf(id.toString());
                        var checked = (index > -1) ? "checked" : '';
                        var value = (index > -1) ? "1" : '0';
                        xhtml += `<div class="panel mb-4">
                           
                                <div class="panel-heading">
                                <div class="row align-content-center">
                                    <div class="col-auto pr-0"><input data-id="${e.id}" ${checked} class="js-switch statusOptionEntries" type="checkbox" name="statusOptionEntries" value="${value}"></div>
                                    <div class="col"><a role="tab" id="heading${e.id}" class="collapsed d-block" data-toggle="collapse" data-parent="#accordion" href="#collapse${e.id}" aria-expanded="true" aria-controls="collapse${e.id}"><strong>${e.title}</strong></a></div> 
                                </div>
                                </div>
                           <div id="collapse${e.id}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading${e.id}">
                                <div class="panel-body">
                                ${optionEntries.Modal.generateOptionHTML(e.attributes, e.type)}
                                </div>
                            </div>
                        </div>`;
                    });
                    xhtml += "</div>";
                    $(optionEntries.selector.modal.loadData).html(xhtml);

                    if ($("#modaloptionEntries .js-switch")[0]) {
                        var elems = Array.prototype.slice.call(document.querySelectorAll(
                            '#modaloptionEntries .js-switch'));
                        elems.forEach(function(html) {
                            var switchery = new Switchery(html, {
                                color: '#26B99A'
                            });
                        });
                    }

                },
                generateOptionHTML: function(data, type = 0) {
                    var xhtml = `<div class="row">`;
                    $.each(data, function(i, e) {
                        var price = (e.price > 0) ?
                            `<span class="text-danger font-italic"> +${FormatNumber(e.price)} ₫</span>` :
                            '';
                        xhtml += `<div class="col-auto">`;
                        if (type == 0) {
                            xhtml +=
                                `<div class="py-2 px-4 rounded border bg-light"><strong>${e.title+price}</strong></div>`;
                        }
                        if (type == 1) {
                            var path = "@php echo asset($configPathOption['path']) @endphp";
                            xhtml += `<div class="row align-items-center">
                                                        <div class="col-auto pr-0"><img class="rounded-circle" src="${path + "/" + e.picture}" width="45px" alt=""></div>
                                                        <div class="col"><strong>${e.title+price}</strong></div>
                                                    </div>
                                        `;
                        }
                        if (type == 2) {
                            xhtml += `
                                        <div class="row align-items-center">
                                            <div class="col-auto pr-0">
                                                <div class="box-color rounded-circle" style="background-color:${e.color}"></div>
                                            </div>
                                            <div class="col pr-0"><strong>${e.title+price}</strong></div>
                                        </div>
                                        `;
                        }
                        xhtml += "</div>";
                    });
                    xhtml += "</div>";
                    return xhtml;
                },
                hideListSelect: function() {
                    $(optionEntries.selector.modal.popup).modal("hide");
                }
            },
            generateOptionEntriesHTML: function() {
                if (this.data.dataOption.length > 0) {
                    var xhtml = ``;
                    $.each(this.data.dataOption, function(i, e) {
                        let id = e.toString();
                        let item = optionEntries.data.fullData.find(function(e) {
                            return e.id == id;
                        });
                        xhtml += `<li class="d-block mb-4"><div class="accordion" id="accordion_${item.id}" role="tablist" aria-multiselectable="true">
                                <div class="panel">
                                    <div class="panel-heading">
                                         <div class="row align-items-center">
                                            <div class="col-auto pr-0"><i class="fa fa-th"></i></div>
                                            <div class="col-auto pr-0"><a href="javascript:;" data-id="${item.id}" class="btn-delete-optionEntries btn-danger btn btn-sm m-0"><strong><i class="fa fa-trash"></i></strong> Delete</a></div>
                                            <div class="col">
                                                 <a role="tab" id="heading_${item.id}" class="panel-title collapsed d-block" data-toggle="collapse" data-parent="#accordion_${item.id}" href="#collapse_${item.id}" aria-expanded="true" aria-controls="collapse_${item.id}"><strong>${item.title}</strong></a>
                                             </div>
                                         </div>
                                    </div>
                                <div id="collapse_${item.id}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_${item.id}">
                                        <div class="panel-body">
                                         <input type="hidden" name="optionEntriesId[]" value="${item.id}">
                                        ${optionEntries.Modal.generateOptionHTML(item.attributes, item.type)}
                                        </div>
                                    </div>
                                </div></div></li>`;
                    });

                    $(optionEntries.selector.loadData).html(xhtml).sortable({
                        connectWith: '.card-header',
                        cancel: '.card-body'
                    });
                }
            },
            delete: function(id = null) {
                if (id != null) {
                    var index = optionEntries.data.dataOptionTemp.indexOf(id.toString());
                    console.log(optionEntries.data.dataOptionTemp);
                    if (index > -1) {
                        optionEntries.data.dataOptionTemp.splice(index, 1);
                        optionEntries.data.dataOption = optionEntries.data.dataOptionTemp;
                        $(optionEntries.data.input).val(optionEntries.data.dataOption.join(","));
                    }
                }
            },
            action: function() {
                $(this.selector.buttonNew).click(function() {
                    optionEntries.getListData();
                });
                $(document).on("change", this.selector.modal.checkbox, function() {
                    var id = $(this).data("id");
                    var index = optionEntries.data.dataOptionTemp.indexOf(id.toString());
                    if ($(this).is(':checked')) {
                        console.log(index);
                        if (index == -1) {
                            optionEntries.data.dataOptionTemp.push(id);
                        }
                    } else {
                        if (index > -1) {
                            optionEntries.data.dataOptionTemp.splice(index, 1);
                        }
                    }
                });
                $(document).on("click", this.selector.modal.save, function() {
                    optionEntries.data.dataOption = optionEntries.data.dataOptionTemp;
                    $(optionEntries.data.input).val(optionEntries.data.dataOption.join(","));
                    optionEntries.generateOptionEntriesHTML()
                    optionEntries.Modal.hideListSelect();
                });
                $(document).on("click", this.selector.buttonDelete, function() {
                    var id = $(this).data("id");
                    optionEntries.delete(id);
                    $(this).parents("li").remove();
                });
            },
            loadData: function() {
                this.getListData(false);

            },
            generateData: function() {
                var dataOptionEntries = $(this.data.input).val();
                if (dataOptionEntries != '') {
                    optionEntries.data.dataOption = optionEntries.data.dataOptionTemp = dataOptionEntries.split(
                    ",");
                    optionEntries.generateOptionEntriesHTML();
                }

            },
            init: function() {
                this.action();
                this.loadData();
            }
        };
        $(document).ready(function() {
            optionEntries.init();
        });
    </script>
    <div class="modal" id="modaloptionEntries" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <p class="text-right p-2 bg-light"><button type="button"
                        class="modalSaveOptionEntries btn btn-primary">Hoàn
                        tất</button></p>
                <div class="modal-body" id="loadData"></div>
                <div class="modal-footer">

                    <button type="button" id="modalSaveOptionEntries"
                        class="modalSaveOptionEntries btn btn-primary">Hoàn tất</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection
