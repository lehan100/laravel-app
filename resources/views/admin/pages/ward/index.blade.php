@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
    use App\Helpers\Form as FormTemplate;
    $dataTypeProvince = config("configs.location.province");
    $dataTypeDistrict = config("configs.location.district");
    $dataTypeWard = config("configs.location.ward");
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
<div class="x_panel">
        @include('admin.templates.x_title')
    <div class="x_content">
            @php
                $statusValue = ['default' => 'Select status'];
                $formInputAttr = config('configs.template.form_input');
                $var = ['0' => '--- Tất cả ---'];
                $districtItems = $var + $districtItems;
                $provinceItems = $var + $provinceItems;
                $url_filter = route($controllerName . '/filter');
//                $acFilter = "addFilter('wards.district_id',this)";
//                $valueFilter = '';
                $filter = $filter->getFilter();
//                if(isset($filter['wards.district_id'])){
//                    $valueFilter = $filter['wards.district_id'];
//                }
                //$districtFilter = Form::select('id_district', $districtItems, $valueFilter, ['class' => $formInputAttr, 'onchange' => $acFilter, 'data-url' => $url_filter]);
                $valueFilter = '';
                $acFilter = "addFilter('districts.province_id',this)";
                if(isset($filter['districts.province_id'])){
                    $valueFilter = $filter['districts.province_id'];
                }
                $provinceFilter = html()->select('id_city', $provinceItems, $valueFilter)->attributes( ['class' => $formInputAttr, 'onchange' => $acFilter, 'data-url' => $url_filter]);
            @endphp
        <div class="row align-items-center mb-3">
            <div class="col-auto">Lọc theo Tỉnh/TP</div>
            <div class="col-auto">{{ $provinceFilter }}</div>
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
                            @include('admin.templates.thead.column',['name'=>'Tên ' .$title])
                            @include('admin.templates.thead.column',['name'=>'Tỉnh/Thành Phố'])
                            @include('admin.templates.thead.active')
                            @include('admin.templates.thead.action')
                            @include('admin.templates.thead.id')
                </tr>
            </thead>
            <tbody>
            @foreach ($items as $key => $val)
                @php
                    $name = $val['name'];
                    $district = isset($val->district->name)? $val->district->name.", ".$val->district->province->name:'';
                    //$district = '';
                    $id = $val['id'];
                    $status = Template::showStatus($controllerName, $val['status'], $id);
                    $buttonAction = Template::showButtomAction($controllerName, $id);
                    $linkEdit = route($controllerName . "/form", ['id' => $id]);
                @endphp
                <tr class="dblclick" data-link='{{$linkEdit}}'>
                    <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                    <td>{{ $name }}</td>
                    <td>{{ $district }}</td>
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
