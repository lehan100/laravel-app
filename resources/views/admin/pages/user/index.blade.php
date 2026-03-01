@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    <div class="x_panel">
        @include('admin.templates.x_title')
        <div class="x_content">
            @if (count($items) > 0)
                @include ('admin.templates.notify')
                {{ html()->form('POST', '')->attributes([
                        'accept-charset' => 'UTF-8',
                        'enctype' => 'multipart/form-data',
                        'class' => 'form-horizontal form-label-left',
                        'id' => 'appForm',
                    ])->open() }}
                <table class="table table-striped jambo_table ">
                    <thead>
                        <tr>
                            @include('admin.templates.thead.check_all')
                            @include('admin.templates.thead.column', [
                                'name' => 'User Role',
                                'width' => '160',
                            ])
                            @include('admin.templates.thead.column', ['name' => 'Tên đăng nhập'])
                            @include('admin.templates.thead.column', ['name' => 'Họ và tên'])
                            @include('admin.templates.thead.column', ['name' => 'Email'])
                            @include('admin.templates.thead.active')
                            @include('admin.templates.thead.action')
                            @include('admin.templates.thead.id')
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $val)
                            @php
                                $group = Template::getUserGroup($val['group']);
                                $fullname = $val['fullname'];
                                $username = $val['username'];
                                $email = $val['email'];
                                $id = $val['id'];
                                $status = Template::showStatus($controllerName, $val['status'], $id);
                                $buttonAction = Template::showButtomAction($controllerName, $id);
                                $linkEdit = route($controllerName . '/form', ['id' => $id]);
                            @endphp
                            <tr class="dblclick" data-link='{{ $linkEdit }}'>
                                <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                                <td>{!! $group !!}</td>
                                <td>{!! $username !!}</td>
                                <td>{{ $fullname }}</td>
                                <td>{{ $email }}</td>
                                <td class="text-center">{!! $status !!}</td>
                                <td class="text-center">{!! $buttonAction !!}</td>
                                <td class="text-center">{{ $id }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ html()->form()->close() }}
                @include('pagination.pagination_admin')
            @else
                @include('admin.templates.list_empty')
            @endif
        </div>
    </div>
@endsection
