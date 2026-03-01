@php
    $width = @isset($width) ? $width : 'auto';
    $name = @$name;
    $class  = @$class;
@endphp
<th class="border-top-0 border-bottom-0 {{$class}}" width="{{$width}}">{!!$name!!}</th>