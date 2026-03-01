@extends('admin.layouts.default')
@php
use App\Helpers\Template as Template;
$imageSize = config("image.admin.photo");
$configPath = config('image.path.photo');
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
<div class="x_panel">
        @include('admin.templates.x_title')
    <div class="x_content">
            @php
                $formInputAttr = config('configs.template.form_input');
                $var = ['0' => '--- Tất cả ---'];
                $itemsPosition = $var + $itemsPosition;
                $url_filter = route($controllerName . '/filter');
                $acFilter = "addFilter('media_banners.position_id',this)";
                $valueFilter = 0;
                $filter = $filter->getFilter();
                if(isset($filter['media_banners.position_id'])){
                    $valueFilter = $filter['media_banners.position_id'];
                }
                $positionFilter = html()->select('position_id', $itemsPosition, $valueFilter)->attributes( ['class' => $formInputAttr, 'onchange' => $acFilter, 'data-url' => $url_filter]);
            @endphp
        <div class="row align-items-center mb-3">
            <div class="col-auto">Lọc vị trí</div>
            <div class="col-auto">{{ $positionFilter }}</div>
        </div>
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
                    @include('admin.templates.thead.column',['name'=>'Hình ảnh','width'=>'120'])
                    @include('admin.templates.thead.column',['name'=>'Tên ' .$title])
                    @include('admin.templates.thead.column',['name'=>'Vị trí','width'=>'180'])
                    @include('admin.templates.thead.active')
                    @include('admin.templates.thead.action')
                    @include('admin.templates.thead.id')
                </tr>
            </thead>
            <tbody>
            @foreach ($items as $key => $val)
                @php
                    $name = $val['name'];
                    $position = $val->position['name'];
                    $id = $val['id'];
                    $image_src = "";
                    if($val['picture'] != ""){
                         $image_src = asset($configPath['path']."/".$val['picture']);
                    }
                    $img = Template::showImageAminLists($image_src,$imageSize['width'],'auto');
                    $status = Template::showStatus($controllerName, $val['status'], $id);
                    $buttonAction = Template::showButtomAction($controllerName, $id);
                    $linkEdit = route($controllerName . "/form", ['id' => $id]);
                @endphp
                <tr class="dblclick" data-link='{{$linkEdit}}'>
                    <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                    <td class="text-center">{!!  $img !!}</td>
                    <td>{{ $name }}</td>
                    <td>{{ $position }}</td>
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
