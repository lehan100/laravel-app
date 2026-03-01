@extends('admin.layouts.default')
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
<div class="x_panel">
        @include('admin.templates.x_title')
    <div class="x_content">
        @include('admin.layouts.elements.messages')
        <table class="table table-striped jambo_table">
            <thead>
                <tr>
                @include('admin.templates.thead.column',['name'=>'No','width'=>'60'])
                @include('admin.templates.thead.column',['name'=>'Nhóm ' .$title])
                @include('admin.templates.thead.column',['name'=>'Chức năng','width'=>'250'])
                </tr>
            </thead>
            @foreach ($roles as $key => $role)
            <tr>
                <td class="text-center">{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    <a class="btn btn-info btn-sm" href="{{ route('roles.show', $role->id) }}"><i class="fa fa-eye mr-2"></i>Show</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('roles.edit', $role->id) }}"><i class="fa fa-pencil mr-2"></i>Sửa</a>
                    {{ html()->form('DELETE')->route('roles.destroy', $role->id)->attributes([
                        'style' => 'display:inline'
                    ])->open() }}
                   
                {!! html()->submit('Xóa')->attributes( ['class' => 'btn btn-danger btn-sm']) !!}
                {!! html()->form()->close() !!}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="d-flex">
            {!! $roles->links() !!}
        </div>
    </div>
</div>
@endsection