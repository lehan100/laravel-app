@extends('admin.layouts.default')
@section('title', $metaTitle)
@section('content')
<div class="page-title row">
    <div class="title_left col">
        <h3 class="text-uppercase">{{ ucfirst($title) }}</h3>
    </div>
    <div class="title_right col-auto">
        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning"><i class="fa fa-pencil mr-2"></i>Chỉnh sửa</a>
        <a href="{{ route('roles.index') }}" class="btn btn-info"><i class="fa fa-mail-reply mr-2"></i>Quay về</a>
    </div>
</div>
<div class="x_panel">
        @include('admin.templates.x_title')
    <div class="x_content">
        <table class="table table-striped jambo_table">
            <thead>
            <th scope="col" width="20%">Name</th>
            <th scope="col" width="1%">Guard</th> 
            </thead>

                @foreach($rolePermissions as $permission)
            <tr>
                <td>{{ $permission->name }}</td>
                <td>{{ $permission->guard_name }}</td>
            </tr>
                @endforeach
        </table>
    </div>
</div>
@endsection