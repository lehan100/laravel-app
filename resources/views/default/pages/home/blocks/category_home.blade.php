@if(count($itemsCategoryTop)>0)
<div id="list-category-home" class="owl-carousel carousel-nav text-center bg-white rounded-2 mt-4 py-2">
    <div class="item">
    @foreach($itemsCategoryTop as $k=> $val)
        @php
            $link = url($val->url['path']);
            $picture = $val->picture;
            $name = $val->name;
            $pictureUrl = ( $picture != "") ? asset($configPath['category']['path'] . '/' . $picture) : "";
        @endphp
        <div class="mm-img">
            <a title="{{$name}}" href="{{$link}}">
                <div class="img"><img data-img="{{$pictureUrl}}" alt="{{$name}}" height="90" class="w-auto d-inline"/></div>
                <h2 class="name">{{$name}}</h2>
            </a>
        </div>
        @php
        echo (++$k % 2 ==0 && count($itemsCategoryTop) > $k) ? '</div><div class="item">':'';
       @endphp
    @endforeach
    </div>
</div>
@endif