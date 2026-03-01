@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $elementInfo = [
        [
            'label' => html()->label(for:'domain', contents:'Website')->class($formLabelAttr),
            'element' => html()
                ->text('domain', @$item['domain'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'hotline', contents:'Số Hotline')->class($formLabelAttr),
            'element' => html()
                ->text('hotline', @$item['hotline'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'hotline_zalo', contents:'Số Zalo')->class($formLabelAttr),
            'element' => html()
                ->text('hotline_zalo', @$item['hotline_zalo'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'email', contents:'Email')->class($formLabelAttr),
            'element' => html()
                ->text('email', @$item['email'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'time', contents:'Thời gian làm việc')->class($formLabelAttr),
            'element' => html()
                ->text('time', @$item['time'])
                ->class($formInputAttr),
        ],
    ];
    $elementSocial = [
        [
            'label' => html()->label(for:'facebook-link', contents:'Link Facebook')->class($formLabelAttr),
            'element' => html()
                ->text('facebook-link', @$item['facebook-link'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'youtube-link', contents:'Link Youtube')->class($formLabelAttr),
            'element' => html()
                ->text('youtube-link', @$item['youtube-link'])
                ->class($formInputAttr),
        ],
    ];
    $elementLabelShipping = [
        [
            'label' => html()->label(for:'freeshipping_price', contents:'Free Shipping Over')->class($formLabelAttr),
            'element' => html()
                ->text('freeshipping_price', @$item['freeshipping_price'])
                ->class($formInputAttr),
        ],
    ];
    $elementLabelContact = [
        [
            'label' => html()->label(for:'contact_fullname', contents:'Fullname')->class($formLabelAttr),
            'element' => html()
                ->text('contact_fullname', @$item['contact_fullname'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'contact_phone', contents:'Phone')->class($formLabelAttr),
            'element' => html()
                ->text('contact_phone', @$item['contact_phone'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'contact_email', contents:'Email')->class($formLabelAttr),
            'element' => html()
                ->text('contact_email', @$item['contact_email'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'contact_address', contents:'Address')->class($formLabelAttr),
            'element' => html()
                ->text('contact_address', @$item['contact_address'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'contact_message', contents:'Message')->class($formLabelAttr),
            'element' => html()
                ->text('contact_message', @$item['contact_message'])
                ->class($formInputAttr),
        ],
        [
            'label' => html()->label(for:'contact_buttom_send', contents:'Buttom Send')->class($formLabelAttr),
            'element' => html()
                ->text('contact_buttom_send', @$item['contact_buttom_send'])
                ->class($formInputAttr),
        ],
    ];
    $elementSeo = [
        [
            'label' => html()->label(for:'title', contents:'Tiêu đề')->class($formLabelAttr),
            'element' => html()
                ->text('title', @$item['title'])
                ->class($errors->first('title') ? $formInputAttr . ' is-invalid' : $formInputAttr),
            'error' => $errors->first('title')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('title'))
                : '',
        ],
        [
            'label' => html()->label(for:'keyword', contents:'Từ khóa')->class($formLabelAttr),
            'element' => html()
                ->textarea('keyword', @$item['keyword'])
                ->class($formInputAttr)
                ->attributes(['rows' => 4, 'cols' => 54]),
        ],
        [
            'label' => html()->label(for:'description', contents:'Mô tả')->class($formLabelAttr),
            'element' => html()
                ->textarea('description', @$item['description'])
                ->class($formInputAttr)
                ->attributes(['rows' => 4, 'cols' => 54]),
        ],
    ];
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    @include ('admin.templates.notify')
    {{ html()->form('POST', route('settings/save'))->attributes([
            'accept-charset' => 'UTF-8',
            'enctype' => 'multipart/form-data',
            'class' => 'form-horizontal form-label-left',
            'id' => 'appForm',
        ])->open() }}

    <div class="row">
        <div class="col-12 col-md-3 mb-3">
            <div class="nav flex-column nav-pills bg-white" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="border rounded-0 py-3 nav-link active" id="v-pills-general-tab" data-toggle="pill"
                    data-target="#v-pills-general" type="button" role="tab" aria-controls="v-pills-general"
                    aria-selected="false"><i class="font-l fa fa-cogs mr-2"></i>General</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-seo-tab" data-toggle="pill"
                    data-target="#v-pills-seo" type="button" role="tab" aria-controls="v-pills-seo"
                    aria-selected="false"><i class="font-l fa fa-gg mr-2"></i>Search Engine Optimization (default)</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-shipping-tab" data-toggle="pill"
                    data-target="#v-pills-shipping" type="button" role="tab" aria-controls="v-pills-shipping"
                    aria-selected="false"><i class="font-l fa fa-truck mr-2"></i>Shippings</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-payment-tab" data-toggle="pill"
                    data-target="#v-pills-payment" type="button" role="tab" aria-controls="v-pills-payment"
                    aria-selected="false"><i class="font-l fa fa-credit-card mr-2"></i>Payments</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-label-tab" data-toggle="pill"
                    data-target="#v-pills-label" type="button" role="tab" aria-controls="v-pills-label"
                    aria-selected="false"><i class="font-l fa fa-language mr-2"></i>Localize Labels</a>
                <a class="border border-top-0 py-3 rounded-0 nav-link" id="v-pills-search-tab" data-toggle="pill"
                    data-target="#v-pills-search" type="button" role="tab" aria-controls="v-pills-search"
                    aria-selected="false"><i class="font-l fa fa-search-minus mr-2"></i>Search and Replace</a>

            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="tab-content p-4 border bg-white" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel"
                    aria-labelledby="v-pills-general-tab">
                    <div id="accordion">
                        <div class="card mb-3">
                            <div class="card-header" id="headingOne">
                                <h5 class="mb-0">
                                    <button class="btn btn-link font-weight-bold" type="button" data-toggle="collapse"
                                        data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        SITE INFORMATION
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                data-parent="#accordion">
                                <div class="panel-body py-4">
                                    <div class="w-75">{!! FormTemplate::show($elementInfo) !!}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header" id="headingTwo">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed font-weight-bold" type="button"
                                        data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo">
                                        SOCIAL MEDIA
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                <div class="card-body py-4">
                                    <div class="w-75">{!! FormTemplate::show($elementSocial) !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="v-pills-seo" role="tabpanel" aria-labelledby="v-pills-seo-tab">
                    <div class="w-75">{!! FormTemplate::show($elementSeo) !!}</div>
                </div>
                <div class="tab-pane fade" id="v-pills-shipping" role="tabpanel" aria-labelledby="v-pills-shipping-tab">
                    <div class="w-75">{!! FormTemplate::show($elementLabelShipping) !!} </div>
                </div>
                <div class="tab-pane fade" id="v-pills-payment" role="tabpanel" aria-labelledby="v-pills-payment-tab">
                    <div class="w-75">Building</div>
                </div>
                <div class="tab-pane fade" id="v-pills-label" role="tabpanel" aria-labelledby="v-pills-label-tab">
                    <div id="accordion2">
                        <div class="card mb-3">
                            <div class="card-header" id="headingContact">
                                <h5 class="mb-0">
                                    <button class="btn btn-link font-weight-bold" type="button" data-toggle="collapse"
                                        data-target="#collapseContact" aria-expanded="true"
                                        aria-controls="collapseContact">
                                        CONTACT FORM
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseContact" class="collapse show" aria-labelledby="headingContact"
                                data-parent="#accordion2">
                                <div class="panel-body py-4">
                                    <div class="w-75">{!! FormTemplate::show($elementLabelContact) !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="v-pills-search" role="tabpanel" aria-labelledby="v-pills-search-tab">
                    <p><button type="button" id="add_label" class="btn btn-sm btn-success text-white"><i
                                class="fa fa-plus-circle mr-2"></i>Add Key Search</button></p>
                    <div class="bg-secondary px-2 py-3 text-white mb-3 ">
                        <div class="row">
                            <div class="col-3">
                                Key Search
                            </div>
                            <div class="col">
                                Text Replace
                            </div>
                            <div class="col-auto">
                                Xóa
                            </div>
                        </div>
                    </div>
                    @if (isset($locals) && count($locals) > 0)
                        @foreach ($locals as $key => $val)
                            @include('admin.pages.settings.plugins.local', ['val' => $val, 'key' => $key])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{ html()->form()->close() }}
@endsection
@section('script')
    <script type="text/javascript">
        var label = ` @include('admin.pages.settings.plugins.local', ['val' => '', 'key' => '']) `;
        $(document).ready(function() {
            $("#add_label").click(function() {
                $("#v-pills-search").append(label);
            });
        });

        function deleteLabel(elm) {
            $(elm).parents(".item-label").remove();
        }
    </script>
@endsection
