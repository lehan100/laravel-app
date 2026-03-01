@php
$configPath = config('image.path.photo');
@endphp
<div class="header py-2">
    <div class="container">
        <div class="row align-items-center">
            @if($logo)
            @php
                $image_src = asset($configPath['path']."/".$logo->picture);
            @endphp
            <div class="col py-2 logo">
                <a href="{{url("/")}}">
                    <img src="{{$image_src}}" alt="{{$logo->name}}"/>
                </a>
            </div>
            @endif
            <div class="d-none d-md-block col-auto header-group">
                <a href="tel://{{$settings['hotline']}}">
                    <div class="hotline rounded py-2 px-3">
                        <p class="mb-0"><strong>{{$settings['hotline']}}</strong></p>
                        <span class="name">Hotline mua hàng</span>
                    </div>
                </a>
            </div>
        </div> 
    </div>
</div>