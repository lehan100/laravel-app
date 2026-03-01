<div class="row my-3 mt-4">
    @if(count($blockAds)>0)
    <div class="banner-group col-12 col-xl-4 order-1 order-xl-0">
        <ul class="h-100 row">
            @foreach($blockAds as $val)
            @php
            $name = $val->name;
            $alias_link = ($val['alias_link']!="") ? $val['alias_link'] : 'javascript:;';
            $image_src = asset($configPath['photo']['path']."/".$val->picture);
           @endphp
            <li class="mm-img show col-12 col-md-6 col-xl-12">
                <a href="{{$alias_link}}">
                    <img class="owl-lazy" alt="{{$name}}" width="100%" src="{{$image_src}}"/>
                </a>
            </li>
           @endforeach
        </ul>
    </div>
     @endif
    <div class="col-12 col-xl-8 order-0 order-xl-1">
            @include('default.layouts.elements.carousel',['view'=>'slider'])
    </div>
</div>