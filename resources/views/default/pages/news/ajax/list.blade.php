@php
    use Illuminate\Support\Carbon;
    use App\Helpers\Content;
    $configPath = config('image.path.post');
@endphp
@if (count($newsItems) > 0)
    @php
        $total = count($newsItems);
        $dataOne = $dataTwo = $dataThree = [];
        if ($total > 5) {
            $dataOne = array_slice($newsItems->toArray()['data'], 0, 1);
            $dataTwo = array_slice($newsItems->toArray()['data'], 1, 4);
            $dataThree = array_slice($newsItems->toArray()['data'], 5);
        } else {
            $dataThree = $newsItems->toArray()['data'];
        }
    @endphp
    {{-- Featured --}}
    <div class="row block-featured">
        @if (count($dataOne) > 0)
            @php
                $name = $dataOne[0]['name'];
                $link = $dataOne[0]['url']['path'];
                $picture = $dataOne[0]['picture'];
                $img_src = $picture != '' ? asset($configPath['path'] . '/' . $picture) : '';
                $createdAt = Carbon::parse($dataOne[0]['created_at']);
                $created_at = $createdAt->format('d/m/Y');
                $sort_content = Content::miniString($dataOne[0]['contents']['sort_content']);
            @endphp
            <div class="blog-featured-left col-12 col-md-6">
                <div class="post-item card border-0 rounded-0 bg-transparent">
                    <a href="/{{ $link }}">
                        <img src="{{ $img_src }}" alt="{{ $name }}" class="rounded-3 border" />
                    </a>
                    <div class="caption">
                        <div class="date-meta">
                            <i class="bi bi-clock mr2"></i> {{ $created_at }}
                        </div>
                        <div class="post-name">
                            <a href="/{{ $link }}">
                                {{ $name }}
                            </a>
                        </div>
                        <div class="sort-content">
                            {{ $sort_content }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (count($dataTwo) > 0)
            <div class="blog-featured-right col-12 col-md-6">
                @foreach ($dataTwo as $item)
                    @php
                        $name = $item['name'];
                        $link = $item['url']['path'];
                        $picture = $item['picture'];
                        $img_src = $picture != '' ? asset($configPath['path'] . '/' . $picture) : '';
                        $createdAt = Carbon::parse($item['created_at']);
                        $created_at = $createdAt->format('d/m/Y');
                    @endphp
                    <div class="post-item mb-3">
                        <div class="row align-items-end">
                            <div class="col order-1 order-md-0">
                                <div class="caption py-0">
                                    <div class="date-meta mb-1">
                                        <i class="bi bi-clock mr2"></i> {{ $created_at }}
                                    </div>
                                    <div class="post-name mb-0">
                                        <a href="/{{ $link }}">
                                            {{ $name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5 col-md-4">
                                <a href="/{{ $link }}">
                                    <img src="{{ $img_src }}" alt="{{ $name }}"
                                        class="rounded-3 border" />
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    {{-- Featured --}}
    @if (count($dataThree) > 0)
        <div class="post-list my-4">
            @foreach ($dataThree as $item)
                @php
                    $name = $item['name'];
                    $link = $item['url']['path'];
                    $picture = $item['picture'];
                    $img_src = $picture != '' ? asset($configPath['path'] . '/' . $picture) : '';
                    $createdAt = Carbon::parse($item['created_at']);
                    $created_at = $createdAt->format('d/m/Y');
                    $sort_content = Content::miniString($item['contents']['sort_content'], 30);
                @endphp
                <div class="post-item mb-3">
                    <div class="row align-items-end align-items-md-start">
                        <div class="col-5 col-md-4">
                            <a href="/{{ $link }}">
                                <img src="{{ $img_src }}" alt="{{ $name }}" class="rounded-3 border" />
                            </a>
                        </div>
                        <div class="col">
                            <div class="caption py-0">
                                <div class="date-meta">
                                    <i class="bi bi-clock mr2"></i> {{ $created_at }}
                                </div>
                                <div class="post-name">
                                    <a href="/{{ $link }}">
                                        {{ $name }}
                                    </a>
                                </div>
                                <div class="d-none d-md-block sort-content">
                                    {{ $sort_content }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif
@else
    <div class="font-2 alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi flex-shrink bi-exclamation-triangle-fill me-2"></i>
        <div>
            Không tìm thấy dữ liệu
        </div>
    </div>
@endif
