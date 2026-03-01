@if ($paginator->hasPages())
<nav class="d-flex justify-items-center justify-content-between">
    <div class="d-flex justify-content-between flex-fill d-sm-none">
        <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">@lang('pagination.previous')</span>
            </li>
                @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
            </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
            </li>
                @else
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">@lang('pagination.next')</span>
            </li>
                @endif
        </ul>
    </div>

    <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
        <div>
            <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.first')">
                    <span class="page-link" aria-hidden="true">Trang đầu</span>
                </li>
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
                    @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->Url(1) }}" rel="first"
                       aria-label="@lang('pagination.previous')">Trang đầu</a>

                </li>
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }} " rel="prev"
                       aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
                    @endif

                    {{-- Pagination Elements --}}
                  @php
                    $start = $paginator->currentPage() - 1; // show 3 pagination links before current
                    $end = $paginator->currentPage() + 1; // show 3 pagination links after current
                    if($start < 1) {
                        $start = 1; // reset start to 1
                        $end += 1;
                    } 
                    if($end >= $paginator->lastPage() ) $end = $paginator->lastPage(); // reset end to last page
                @endphp

                @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $paginator->url(1) }}">{{1}}</a>
                            </li>
                    @if($paginator->currentPage() != 4)
                        {{-- "Three Dots" Separator --}}
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                    @endif
                @endif
                @for ($i = $start; $i <= $end; $i++)
                        <li class="page-item {{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                            <a class="page-link" href="{{ $paginator->url($i) }}">{{$i}}</a>
                        </li>
                @endfor
                @if($end < $paginator->lastPage())
                    @if($paginator->currentPage() + 3 != $paginator->lastPage())
                        {{-- "Three Dots" Separator --}}
                            <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                    @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{$paginator->lastPage()}}</a>
                            </li>
                @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                       aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->Url($paginator->lastPage()) }}" rel="last"
                       aria-label="@lang('pagination.next')">Trang cuối</a>
                </li>
                      @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.last')">
                    <a class="page-link" href="{{ $paginator->Url($paginator->lastPage()) }}" rel="last"
                       aria-label="@lang('pagination.next')">Trang cuối</a>
                </li>
                      @endif
            </ul>
        </div>
    </div>
</nav>
@endif



