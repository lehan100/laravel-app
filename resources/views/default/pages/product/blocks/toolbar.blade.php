@php
use App\Helpers\Product\Sort as Sort;
$sortToolbar = Sort::tostring($sort);
@endphp
{!!$sortToolbar !!}