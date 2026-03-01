@php
    $name = @isset($name) ? $name : "Chức năng";
    $width = @isset($width) ? $width : '170';
@endphp
@include('admin.templates.thead.column',['name'=>$name,'width'=>$width,'class'=>'text-center'])