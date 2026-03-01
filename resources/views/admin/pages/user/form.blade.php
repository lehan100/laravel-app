@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $userGroup = config('configs.user_group_name');
    $var = ['0' => '--- Please Select ---'];
    $userGroup = $var + $userGroup;
    $inputHiddenID = html()->hidden('id', @$item['id']);
    $inputHiddenPassword = html()->hidden('password_old', @$item['password']);
    $inputHiddenRollback = html()
        ->hidden('rollback', 0)
        ->attributes(['id' => 'rollback']);
    $status = isset($item['status']) && $item['status'] == 1 ? true : false;
    $elements = [
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
                ->label(for: 'fullname', contents: 'Họ và Tên')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('fullname', @$item['fullname'])
                ->attributes(['class' => $errors->first('fullname') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('fullname')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('fullname'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'email', contents: 'Email')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('email', @$item['email'])
                ->attributes(['class' => $errors->first('email') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('email')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('email'))
                : '',
        ],
    ];
    $elementsLogin = [
        [
            'label' => html()
                ->label(for: 'name', contents: 'Phân quyền')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->select('group', $userGroup, @$item['group'])
                ->attributes(['class' => $errors->first('group') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('group')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('group'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'username', contents: 'Tên đăng nhập')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->text('username', @$item['username'])
                ->attributes(['class' => $errors->first('username') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
            'error' => $errors->first('username')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('username'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'password', contents: 'Mật khẩu')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->password('password')
                ->attributes([
                    'autocomplete' => 'off',
                    'class' => $errors->first('password') ? $formInputAttr . ' is-invalid' : $formInputAttr,
                ]),
            'error' => $errors->first('password')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('password'))
                : '',
        ],
        [
            'label' => html()
                ->label(for: 'password_confirmation', contents: 'Nhập lại khẩu')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->password('password_confirmation')
                ->attributes([
                    'autocomplete' => 'off',
                    'class' => $errors->first('password_confirmation')
                        ? $formInputAttr . ' is-invalid'
                        : $formInputAttr,
                ]),
            'error' => $errors->first('password')
                ? sprintf('<div class="invalid-feedback">%s</div>', $errors->first('password'))
                : '',
        ],
        [
            'element' => $inputHiddenID . $inputHiddenRollback . $inputHiddenPassword,
        ],
    ];
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    {{ html()->form('POST', route("$controllerName/save"))->attributes([
            'accept-charset' => 'UTF-8',
            'enctype' => 'multipart/form-data',
            'id' => 'appForm',
        ])->open() }}
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="x_panel">
                @include('admin.templates.x_title')
                <div class="x_content">
                    @include ('admin.templates.notify')
                    <!--@include ('admin.templates.error') -->
                    {!! FormTemplate::show($elements) !!}
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="x_panel">
                @include('admin.templates.x_title', ['title' => 'Thông tin đăng nhập'])
                <div class="x_content">
                    {!! FormTemplate::show($elementsLogin) !!}
                </div>
            </div>
        </div>
    </div>
    {{ html()->form()->close() }}
@endsection
