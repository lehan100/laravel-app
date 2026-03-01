@if (Session::get('success', false))
    @php
        $data = Session::get('success');
    @endphp
    @if (is_array($data))
        @foreach ($data as $msg)
            <div class="alert d-print-none alert-success" role="alert">
                <i class="fa fa-check mr-2"></i>
                {{ $msg }}
            </div>
        @endforeach
    @else
        <div class="alert d-print-none alert-success" role="alert">
            <i class="fa fa-check mr-2"></i>
            {{ $data }}
        </div>
    @endif
@endif
@if (Session::get('error', false))
    @php
        $data = Session::get('error');
    @endphp
    @if (is_array($data))
        @foreach ($data as $msg)
            <div class="alert d-print-none alert-danger" role="alert">
                <i class="fa fa-close mr-2"></i>
                {{ $msg }}
            </div>
        @endforeach
    @else
        <div class="alert d-print-none alert-danger" role="alert">
            <i class="fa fa-close mr-2"></i>
            {{ $data }}
        </div>
    @endif
@endif
