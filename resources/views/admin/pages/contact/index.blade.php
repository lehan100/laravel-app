@extends('admin.layouts.default')
@php
use App\Helpers\Template as Template;
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    <div class="x_panel p-0 border-0">
        <div class="x_content p-0 m-0">
            @if (count($items)>0)
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
                            @include('admin.templates.thead.column',['name'=>'Tiêu đề'])
                            @include('admin.templates.thead.column',['name'=>'Lời nhắn'])
                            @include('admin.templates.thead.column',['name'=>'Họ tên'])
                            @include('admin.templates.thead.column',['name'=>'Số điện thoại'])
                            @include('admin.templates.thead.column',['name'=>'Email'])
                            @include('admin.templates.thead.active')
                            @include('admin.templates.thead.id')
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $val)
                            @php
                                $name = $val['name'];
                                $phone = $val['phone'];
                                $email = $val['email'];
                                $title = $val['title'];
                                $msg = $val['message'];
                                $id = $val['id'];
                                $status = Template::showStatusContact($controllerName, $val['status'], $id);
                            @endphp
                           <tr class="dblclick">
                                <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                                <td>{{ $title }}</td>
                                <td>{{ $msg }}</td>
                                <td>{{ $name }}</td>
                                <td>{{ $phone }}</td>
                                <td>{{ $email }}</td>
                                <td class="text-center">{!! $status !!}</td>
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
