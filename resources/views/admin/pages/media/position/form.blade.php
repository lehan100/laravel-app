@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $itemsMode = config('configs.mode');
    $inputHiddenID = html()->hidden('id', @$item['id']);
    $inputHiddenRollback = html()->hidden('rollback', 0)->attributes( ['id' => 'rollback']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $var = ['0' => '--- Chọn ---'];
    $itemsMode = $var + $itemsMode;
    $modeSelect = html()->select('mode', $itemsMode, @$item['mode'])->attributes( ['class' => $errors->first('mode') ? $formInputAttr . ' is-invalid' : $formInputAttr]);
    $elements = [
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
            'label' => html()->label(for:'code', contents:'Code')->attributes( ['class' => $formLabelAttr]),
            'element' => html()->text('code', @$item['code'])->attributes( ['class' => $errors->first('code') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('code') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('code')) : '',
        ],
        [
            'label' => html()->label(for:'mode', contents:'Phân vùng')->attributes( ['class' => $formLabelAttr]),
            'element' => $modeSelect,
            'error' => $errors->first('mode') ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('mode')) : '',
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
        </div>
    </div>
</div>

@endsection
