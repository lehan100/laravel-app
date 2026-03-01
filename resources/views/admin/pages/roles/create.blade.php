@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
@endphp
@section('title', $metaTitle)
@section('content')
<div class="page-title row">
    <div class="title_left col">
        <h3 class="text-uppercase">{{ ucfirst($title) }}</h3>
    </div>
    <div class="title_right col-auto">
        <a href="javascript:onSubmitActon('appForm')" class="btn btn-warning "><i class="fa fa-save mr-2"></i>Lưu và Đóng</a>
        <a href="{{ route('roles.index') }}" class="btn btn-info "><i class="fa fa-mail-reply mr-2"></i>Quay về</a>
    </div>
</div>
<div class="x_panel">
    @include('admin.templates.x_title')
    <div class="x_content">
         @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                    @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                    @endforeach
            </ul>
        </div>
            @endif
        <form id="appForm" method="POST" action="{{ route('roles.store') }}">
                @csrf
            <div class="mb-3 border bg-green p-3">
               @php
                 $name = html()->text('name', old('name'))->attributes( ['class' => $formInputAttr]);
                 $elementsGeneral = [
                    [
                        'label' => html()->label(for:'name', contents:'Nhóm phân quyền')->attributes( ['class' => "col-auto mb-0"]),
                        'element' => $name
                    ]
                ];
                @endphp
                {!! FormTemplate::show($elementsGeneral) !!}
            </div>
            <label for="permissions" class="form-label">Assign Permissions</label>
            <table class="table table-striped jambo_table">
                <thead>
                <th scope="col" width="1%"><input type="checkbox" id="checkAll" name="all_permission"></th>
                <th scope="col" width="20%">Name</th>
                <th scope="col" width="1%">Guard</th> 
                </thead>
                    @foreach($permissions as $permission)
                <tr>
                    <td>
                        <input type="checkbox" 
                               name="permission[{{ $permission->name }}]"
                               value="{{ $permission->name }}"
                               class='permission'>
                    </td>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->guard_name }}</td>
                </tr>
                    @endforeach
            </table>
        </form>
    </div>
</div>
@endsection