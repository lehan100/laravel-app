@extends('admin.layouts.default')
@php
    use App\Helpers\Template as Template;
@endphp
@section('title', $metaTitle)
@section('content')
    <div class="page-title row mb-3">
        <div class="title_left col">
            <h3 class="text-uppercase">{{ $title }}</h3>
        </div>
        <div class="title_right col-auto">
            <a href="{{route('cache/refresh', ['type' => 'all'])}}" class="btn btn-success"><i class="fa fa-refresh mr-2"></i>Refresh All Cache</a>
        </div>
    </div>
    <div class="x_panel p-0 border-0">
        <div class="x_content p-0 m-0">
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
                        @include('admin.templates.thead.column', ['name' => 'Cache Type'])
                        @include('admin.templates.thead.action', ['name' => 'Action'])
                    </tr>
                </thead>
                <tbody>
                    <tr class="d-none">
                        <td>Database Cache</td>
                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('cache/flush') }}"><i
                                    class="fa fa-refresh mr-2"></i>Refresh</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Files</td>
                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('cache/refresh', ['type' => 'cache']) }}"><i
                                    class="fa fa-refresh mr-2"></i>Refresh</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Configs</td>
                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('cache/refresh', ['type' => 'config']) }}"><i
                                    class="fa fa-refresh mr-2"></i>Refresh</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Views</td>
                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('cache/refresh', ['type' => 'view']) }}"><i
                                    class="fa fa-refresh mr-2"></i>Refresh</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Events</td>
                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('cache/refresh', ['type' => 'event']) }}"><i
                                    class="fa fa-refresh mr-2"></i>Refresh</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Routes</td>
                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('cache/refresh', ['type' => 'route']) }}"><i
                                    class="fa fa-refresh mr-2"></i>Refresh</a>
                        </td>
                    </tr>
                    <tr class="d-none">
                        <td>Queues</td>
                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('cache/queue') }}"><i
                                    class="fa fa-refresh mr-2"></i>Refresh</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Temp Images</td>
                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('cache/image') }}"><i
                                    class="fa fa-trash mr-2"></i>Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Create Permissions Admin Rule</td>
                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('cache/permission') }}"><i
                                    class="fa fa-check mr-2"></i>Create</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            {{ html()->form()->close() }}
        </div>
    </div>
@endsection
