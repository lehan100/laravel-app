@switch($view)
    @case("slider")
        @if(count($sliders)>0)
        <div id="owl-slider" class="dot-active carousel-nav owl-carousel owl-theme">
            @foreach($sliders as $val)
            @php
            $name = $val->name;
            $alias_link = ($val['alias_link']!="") ? $val['alias_link'] : 'javascript:;';
            $image_src = asset($configPath['photo']['path']."/".$val->picture);
           @endphp
            <div class="item">
                <a href="{{$alias_link}}">
                    <img class="owl-lazy" src="{{asset('media/1x.jpg')}}" data-src="{{$image_src}}" alt="{{$name}}"/>
                </a>
            </div>
            @endforeach
        </div>
        @endif
    @break
@endswitch
