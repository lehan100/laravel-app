@php
use App\Helpers\Product\Price as ProductPriceHelper;
@endphp
<ul class="policy mt-5">
    <li class="mb-1">
        <div class="row align-items-center">
            <div class="col-auto pe-1"><i class="bi bi-headset text-info me-2"></i></div>
            <div class="col ps-0">Tổng đài <a class="text-info" href="tel://{{$settings['hotline']}}"><strong>{{$settings['hotline']}}</strong></a><br/>({!! $settings['time']!!})</div>
        </div>
    </li>
    <li>
        <div class="row align-items-center">
            <div class="col-auto pe-1"> <i class="bi bi-truck text-info me-2"></i></div>
            <div class="col ps-0">Miễn phí vận chuyển cho đơn hàng từ <strong>{!! ProductPriceHelper::format_price($settings['freeshipping_price'])!!}</strong></div>
        </div>
    </li>
</ul>