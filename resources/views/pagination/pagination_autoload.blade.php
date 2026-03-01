@if ($listItems->lastPage() > 1)
@php
$totalItems = $listItems->total();
$totalPages = $listItems->lastPage();
$currentPage = $listItems->currentPage();
$nextPage = $currentPage + 1;
@endphp
    @if ($nextPage<=$totalPages)
      <button class=" btn btn-outline-info paginator-page-autoload" data-maxpage="{{$totalPages}}" data-page="{{$nextPage}}">Next Page</button>
    @endif
@endif
