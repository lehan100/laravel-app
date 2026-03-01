<div class="footer py-3 mt-4">
    <div class="container">
        <div class="row">
            @if(count($blockFooterOne) > 0)
            <div class="col-12 col-md-3 info">
                <div class="title">Về chúng tôi</div>
                <ul>
                    @foreach($blockFooterOne as $val)
                        @php
                            $link = url($val->url['path']);
                            $name = $val->name;
                        @endphp
                    <li><a href="{{$link}}">{{$name}}</a></li>
                    @endforeach
                </ul>
            </div>
             @endif
           @if(count($blockFooterTwo) > 0)
            <div class="col-12 col-md-3 info">
                <div class="title">Chăm sóc khách hàng</div>
                <ul>
                    @foreach($blockFooterTwo as $val)
                        @php
                            $link = url($val->url['path']);
                            $name = $val->name;
                        @endphp
                    <li><a href="{{$link}}">{{$name}}</a></li>
                    @endforeach
                </ul>
            </div>
             @endif
            <div class="col col-md">
                <p class="note my-2">Đăng ký nhận thông tin ưu đãi và khuyến mãi</p>
                <form id="eLetter" class="mb-3">
                    <input type="text" name="email" placeholder="Email của bạn" class="form-control input-email">
                    <input type="submit" value="Gửi" class="btn btn-custom">
                </form>
                <p class="note"><b>Copyright © {{date('Y',time())}} {{ucfirst($settings['domain'])}}.</b></p>
                <p class="note">Email:&nbsp;<b>{{$settings['email']}}</b></p>
                <p class="note">Hotline:&nbsp;<b>{{$settings['hotline']}}</b> hoặc gửi yêu cầu<a href="{{url('ho-tro/lien-he-gop-y.html')}}" class="text-info"> tại đây</a></p>
            </div>
            <div class="col-12 col-md-auto">
                <div class="title mb-0">Kết nối với {{ $locals['company'] }}</div>
                <div class="social">
                    <a title="Facebook" rel="nofollow" href="https://www.facebook.com/ukimuacom" class="facebook bi bi-facebook"></a>
                    <a title="Youtube" rel="nofollow" href="#" class="youtube bi bi-youtube"></a>
                    <a title="Pinterest" rel="nofollow" href="#" class="pinterest bi bi-pinterest"></a>
                </div>
                <div class="title mt-3 mb-0">Được chứng nhận</div>
               <p class="mb-1"><a href="//www.dmca.com/Protection/Status.aspx?ID=7105b0f2-0353-4283-bdaa-d3813ad2ef26" title="DMCA.com Protection Status" class="dmca-badge"> <img src ="https://images.dmca.com/Badges/dmca_protected_sml_120l.png?ID=7105b0f2-0353-4283-bdaa-d3813ad2ef26"  alt="DMCA.com Protection Status" /></a>  <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"> </script></p>
                {{-- <p class="mb-1"><a href="javascript:if(window.open('https://secure.trust-provider.com/ttb_searcher/trustlogo?v_querytype=W&amp;v_shortname=POSDV&amp;v_search=https://dealy.vn/&amp;x=6&amp;y=5','tl_wnd_credentials'+(new Date()).getTime(),'toolbar=0,scrollbars=1,location=1,status=1,menubar=1,resizable=1,width=374,height=660,left=60,top=120')){};" ondragstart="return false;"><img src="https://www.positivessl.com/images/seals/positivessl_trust_seal_sm_124x32.png" alt="ssl" border="0"></a></p> --}}
            </div>
        </div>
    </div>
</div>
<div id="goTop" style="display: block;"><i class="bi bi-arrow-up-circle-fill"></i></div>