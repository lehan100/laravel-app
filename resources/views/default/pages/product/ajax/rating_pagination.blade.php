@if (count($listRatings) > 0)
    @include('pagination.pagination', ['listItems' => $listRatings])
@endif
