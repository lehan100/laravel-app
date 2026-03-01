@extends('admin.layouts.default')
@php
use App\Helpers\Template as Template;
use App\Helpers\Category as Category;
$configPath = config('image.path.category');
$imageSize = config("image.admin.category");
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
<div class="x_panel">
        @include('admin.templates.x_title')
    <div class="x_content">
            @if (count($result)>0)
                @include ('admin.templates.notify')
                {{ Form::open([
                    'method' => 'POST',
                    'url' => '',
                    'accept-charset' => 'UTF-8',
                    'enctype' => 'multipart/form-data',
                    'class' => 'form-horizontal form-label-left',
                    'id' => 'appForm',
                ]) }}
        <table id="tblsort" class="table table-bordered jambo_table dataTable">
            <thead>
                <tr>
                    @include('admin.templates.thead.check_all')
                    @include('admin.templates.thead.column',['name'=>'Tên ' .$title])
                    @include('admin.templates.thead.column',['name'=>'Hình ảnh','width'=>'100'])
                    @include('admin.templates.thead.column',['name'=>'Loại trang','width'=>'150'])
                    @include('admin.templates.thead.active')
                    @include('admin.templates.thead.action')
                    @include('admin.templates.thead.column',['name'=>'Parent ID','width'=>'120','class'=>'text-center d-none'])
                    @include('admin.templates.thead.column',['name'=>'Sort','width'=>'70','class'=>'d-none'])
                    @include('admin.templates.thead.id')
                </tr>
            </thead>
            <tbody>
                        @foreach ($result as $key => $val)
                            @php
                                $name = $val['name'];
                                $id = $val['id'];
                                $parent_id = $val['parent_id'];
                                $status = Template::showStatus($controllerName, $val['status'], $id);
                                $page = Category::getPage($val['page']);
                                $buttonAction = Template::showButtomAction($controllerName, $id);
                                $img = "";
                                if($val['picture'] != ""){
                                    $picture = $val['picture'];
                                     $image_src = asset($configPath['path']."/".$picture);
                                     $img = Template::showImageAminLists($image_src,$imageSize['width'],$imageSize['height']);
                                }
                                
                            @endphp
                <tr>
                    <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                    <td>{!! $name !!}</td>
                    <td class="text-center">{!!  $img !!}</td>
                    <td>{{ $page }}</td>
                    <td class="text-center">{!! $status !!}</td>
                    <td class="text-center">{!! $buttonAction !!}</td>
                    <td class="text-center d-none">{{ $parent_id }}</td>
                    <td class="text-center d-none">
                        <input value="{{ $key }}" id="listorder_{{ $key }}" readonly name="listorder[]" class="form-control border-0 p-0 text-center bg-transparent">
                        <input type="hidden" value="{{ $id }}" id="listid_{{ $key }}" readonly name="listorderid[]">
                    </td>
                    <td class="text-center">{{ $id }}</td>
                </tr>
                        @endforeach
            </tbody>
        </table>
                {{ Form::close() }}
                @include('pagination.pagination_admin')
            @else
                @include('admin.templates.list_empty')
            @endif
    </div>
</div>
@endsection