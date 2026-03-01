<div class="news-sitebar">
    @if (count($postViewer) > 0)
        <div class="top-views">
            <div class="title">Xem nhiều</div>
            <ul class="block-top-views">
                @foreach ($postViewer as $item)
                    @php
                        $name = $item->name;
                        $link = url($item->url['path']);
                        $picture = $item->picture;
                        $img_src = $picture != '' ? asset($configPath['path'] . '/' . $picture) : '';
                    @endphp
                    <li>
                        <div class="image">
                            <img src="{{ $img_src }}" alt="{{ $name }}"
                            class="w-100" />
                        </div>
                        <a href="{{ $link }}">{{ $name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
