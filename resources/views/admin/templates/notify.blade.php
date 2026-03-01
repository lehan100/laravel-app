@if (session('notify'))
    @php
        flash()->success(session('notify') ?? 'Thành công');
    @endphp
    {{-- <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="alert alert-success alert-fixed" role="alert">
                <strong>{!! session('notify') !!}</strong>
            </div>
        </div>
    </div> --}}
@endif
@if (session('notify_error'))
    @php
        flash()->error(session('notify_error') ?? 'Thất bại');
    @endphp
    {{-- <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="alert alert-danger alert-fixed" role="alert">
                <strong>{!! session('notify_error') !!}</strong>
            </div>
        </div>
    </div> --}}
@endif
