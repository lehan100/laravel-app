@extends('admin.layouts.default')
@php
use App\Helpers\Template as Template;
use Illuminate\Support\Carbon;
use App\Helpers\Price as Price;
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    <div class="x_panel p-0 border-0">
        {{-- @include('admin.templates.x_title', [
            'metaTitle' => 'Tất cả đơn hàng',
        ]) --}}
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
                            @include('admin.templates.thead.column',['name'=>'Tên chương trình'])
                            @include('admin.templates.thead.column',['name'=>'Mô tả'])
                            @include('admin.templates.thead.column',['name'=>'Ngày bắt đầu'])
                            @include('admin.templates.thead.column',['name'=>'Ngày kết thúc'])
                            @include('admin.templates.thead.column',['name'=>'Trang chủ'])
                            @include('admin.templates.thead.active')
                            @include('admin.templates.thead.action')
                            @include('admin.templates.thead.id')
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $val)
                            @php
                                $name = $val['name'];
                                $decsription = $val['description'];
                                $is_homepage = ($val['is_homepage'] ==1) ? '<span class="btn btn-success btn-xs"><i class="fa fa-globe mr-2"></i>Hiển thị</span>':'<span class="btn btn-danger btn-xs"><i class="fa fa-lock mr-2"></i>Hiển thị</span>';
                                $date_from = Carbon::parse($val['date_from'])->format('d/m/Y');
                                $date_to = Carbon::parse($val['date_to'])->format('d/m/Y');
                                $id = $val['id'];
                                $status = Template::showStatus($controllerName, $val['status'], $id);
                                $buttonAction = Template::showButtomAction($controllerName, $id);
                                $linkEdit = route($controllerName . "/form", ['id' => $id]);
                            @endphp
                           <tr class="dblclick" data-link='{{$linkEdit}}'>
                                <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                                <td>{{ $name }}</td>
                                <td>{{ $decsription }}</td>
                                <td>{{ $date_from }}</td>
                                <td>{{ $date_to }}</td>
                                <td>{!!$is_homepage!!}</td>
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
