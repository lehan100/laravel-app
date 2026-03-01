@php
$totalItems = $listItems->total();
$totalPages = $listItems->lastPage();
$paginator = $listItems;
$link_limit = $params['pagination']['pageLimit'];
@endphp
@if ($paginator->lastPage() > 1)
<ul class="pagination justify-content-center">
    {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled d-none" aria-disabled="true" aria-label="@lang('pagination.first')">
                <span class="page-link" aria-hidden="true">Trang đầu</span>
            </li>
            <li class="page-item disabled d-none" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="page-link" aria-hidden="true">&lsaquo;</span>
            </li>
        @else
            <li class="page-item d-none">
                <a class="page-link paginator-page" href="{{ $paginator->Url(1) }}">Trang đầu</a>

            </li>
             @php
                //$link_previous = $paginator->withQueryString()->previousPageUrl();
                //$link_previous = str_replace("&lazyload=true", "", $link_previous);
                $page = $paginator->currentPage() - 1;
                @endphp
            <li class="page-item">
                <a class="paginator-page page-link icon" data-page="{{$page}}">&lsaquo;</a>
            </li>
        @endif
        @php
            $half_total_links = floor($link_limit / 2);
            $from = $paginator->currentPage() - $half_total_links;
            $to = $paginator->currentPage() + $half_total_links;
            if ($paginator->currentPage() < $half_total_links) {
                $to += $half_total_links - $paginator->currentPage();
            }
            if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
            }
        @endphp
        @if($from >= 1)
            <li class="page-item disabled" aria-disabled="true"><span class="page-link dot">...</span></li>
        @endif 
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            @if ($from < $i && $i < $to)
                @php
                // $link = $paginator->withQueryString()->url($i);
                // $link = str_replace("&lazyload=true", "", $link);
                $page = $i;
                @endphp
                <li class="page-item {{ $paginator->currentPage() == $i ? 'active' : '' }}">
                    <a class="paginator-page page-link {{ $paginator->currentPage() == $i ? 'active' : '' }}" data-page="{{$page }}">{{ $i }}</a>
                </li>
            @endif
        @endfor
        @if($to < $paginator->lastPage())
            <li class="page-item disabled" aria-disabled="true"><span class="page-link dot">...</span></li>
        @endif 
    {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            @php
                // $link_next = $paginator->nextPageUrl();
                // $link_next = str_replace("&lazyload=true", "", $link_next);
                $page = $paginator->currentPage() + 1;
                @endphp
            <li class="page-item">
                <a class="paginator-page page-link icon" data-page="{{$page}}" rel="next"
                   aria-label="@lang('pagination.next')">&rsaquo;</a>
            </li>
            <li class="page-item">
                <a class="page-link d-none" href="{{ $paginator->Url($paginator->lastPage()) }}">Trang cuối</a>
            </li>
          @else
            <li class="page-item disabled d-none">
                <span class="page-link" aria-hidden="true">&rsaquo;</span>
            </li>
            <li class="page-item disabled d-none">
                <a class="page-link" href="{{ $paginator->Url($paginator->lastPage()) }}" rel="last"
                   aria-label="@lang('pagination.next')">Trang cuối</a>
            </li>
          @endif
</ul>
@endif
