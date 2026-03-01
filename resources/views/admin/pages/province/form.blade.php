@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $inputHiddenID = html()->hidden('id', @$item['id']);
    $inputHiddenRollback = html()->hidden('rollback', 0)->attributes(['id' => 'rollback']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $dataType = config('configs.location.province');
    $elements = [
        [
            'label' => html()->label(for:'active', contents:'Duyệt')->class($formLabelAttr),
            'element' => html()->checkbox('status', @$item['status'], $status)->attributes(['class' => 'js-switch']),
        ],
        [
            'label' => html()->label(for:'name', contents:'Tên')->class($formLabelAttr),
            'element' => html()->text('name', @$item['name'])->attributes( ['class' => $errors->first('name') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('name') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('name')) : '',
        ],
        [
            'element' => $inputHiddenID . $inputHiddenRollback,
        ],
    ];
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="x_panel">
                @include('admin.templates.x_title')
                <div class="x_content">
                    @include ('admin.templates.notify')
                    <!--@include ('admin.templates.error') -->
                    {{ html()->form('POST', route("$controllerName/save"))->attributes([
                        'accept-charset' => 'UTF-8',
                        'enctype' => 'multipart/form-data',
                        'id' => 'appForm',
                    ])->open() }}
                    {!! FormTemplate::show($elements) !!}
                </div>
                {{ html()->form()->close() }}
                <!--
                                            <form class="form-horizontal form-label-left">
                                                <div class="form-group row align-items-center">
                                                    <label class="col-form-label col-md-3 col-sm-3 label-align">Active</label>
                                                    <div class="col-md-6 col-sm-6 ">
                                                        <label>
                                                            <input type="checkbox" class="js-switch" checked />
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">First Name <span
                                                            class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 ">
                                                        <input type="text" id="first-name" required="required" class="form-control  ">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="last-name">Last Name <span
                                                            class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 ">
                                                        <input type="text" id="last-name" name="last-name" required="required" class="form-control ">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="middle-name" class="col-form-label col-md-3 col-sm-3 label-align">Middle Name /
                                                        Initial</label>
                                                    <div class="col-md-6 col-sm-6 ">
                                                        <input id="middle-name" class="form-control col" type="text" name="middle-name">
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group row">
                                                    <label class="col-form-label col-md-3 col-sm-3 label-align">Date Of Birth <span
                                                            class="required">*</span>
                                                    </label>
                                                    <div class="col-md-6 col-sm-6 ">
                                                        <input id="birthday" class="date-picker form-control" required="required" type="text">
                                                    </div>
                                                </div>

                                            </form>-->
            </div>
        </div>
    </div>

@endsection
