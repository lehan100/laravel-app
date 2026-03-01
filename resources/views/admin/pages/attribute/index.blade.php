@extends('admin.layouts.default')
@php
use App\Helpers\Template as Template;
$option_type = config("product.option_type");
$configPath = config('image.path.attribute_set');
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
<div class="x_panel">
        @include('admin.templates.x_title')
    <div class="x_content">
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
                    @include('admin.templates.thead.column',['name'=>'Tên ' .$title])
                     @include('admin.templates.thead.column',['name'=>'Attribute'])
                    @include('admin.templates.thead.column',['name'=>'Code','width'=>'120'])
                    @include('admin.templates.thead.column',['name'=>'Type','width'=>'120'])

                    @include('admin.templates.thead.active')
                    @include('admin.templates.thead.action')
                    @include('admin.templates.thead.id')
                </tr>
            </thead>
            <tbody>
            @foreach ($items as $key => $val)
                @php
                    $name = $val['name'];
                    $alias = $val['alias'];
                    $id = $val['id'];
                    $type_id = $val['type'];
                    $type = $option_type[$val['type']];
                    $status = Template::showStatus($controllerName, $val['status'], $id);
                    $buttonAction = Template::showButtomAction($controllerName, $id);
                    $linkEdit = route($controllerName . "/form", ['id' => $id]);
                    $attributes = $val->attributes;
                @endphp
                <tr class="dblclick" data-link='{{$linkEdit}}'>
                    <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                    <td>{{ $name }}</td>
                    <td>
                        @if (count($attributes) > 0)
                            @if ($type_id == 1 || $type_id == 2)
                                <div class="row">
                            @endif
                            @foreach ($attributes as $attr)
                                @if ($type_id == 0)
                                    <span class="badge badge-success">{{ $attr['name'] }}</span>
                                @endif
                                @if ($type_id == 1)
                                    @php
                                        $picture = $attr['picture'];
                                        $pictureUrl =
                                            $attr['picture'] != ''
                                                ? asset($configPath['path'] . '/' . $picture)
                                                : '';
                                    @endphp
                                    <div class="col-auto">
                                        <div class="row align-items-center">
                                            <div class="col-auto pr-0">
                                                <div class="rounded bg-white p-2">
                                                     <img src="{{ $pictureUrl }}" height="40px" alt="">
                                                </div>
                                            </div>
                                            <div class="col"><strong>{{ $attr['name'] }}</strong></div>
                                        </div>
                                    </div>
                                @endif
                                @if ($type_id == 2)
                                        <div class="col-auto">
                                        <div class="row align-items-center">
                                            <div class="col-auto pr-0">
                                                <div class="box-color rounded-circle" style="background-color:{{ $attr['color'] }}"></div>
                                            </div>
                                            <div class="col"><strong>{{ $attr['name'] }}</strong></div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            @if ($type_id == 1 || $type_id == 2)
                                </div>
                            @endif
                        @endif
                    </td>
                    <td>{{ $alias }}</td>
                    <td>{{ $type }}</td>

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
@section('style')
    <style>
        .box-color{width: 40px;height: 40px;}
    </style>
@endsection
