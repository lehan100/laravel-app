@php
    use Illuminate\Support\Carbon;
    use App\Helpers\Content;
    $configPath = config('image.path.post');
@endphp
@extends('default.layouts.1-column')
@section('content')
    <div class="block-breadcrumb py-2">
        <div class="container">
            {!! $breadcrumbs !!}
        </div>
    </div>
    @php
        $name = $postItem->name;
        $id = $postItem->id;
        $picture = $postItem->picture;
        $content = $postItem->contents->content;
        if ($picture != "") {
            $image_src = asset($configPath['path'] . "/" . $picture);
        }
    @endphp
    <div class="main container py-4">
        <div class="row">
            <div class="col-12 col-md-9 mb-4 news-content">
                <h1 class="post-title mb-4">
                    {{$name}}
                </h1>
                <div class="post-image mb-3">
                        <img src="{{$image_src }}" alt="{{$name}}" class="rounded" width="100%">
                </div>
                <div class="main">
                    {!! $content !!}
                </div>
            </div>
            <div class="col-12 col-md-3">
                @include('default.pages.news.blocks.sitebar')
            </div>
        </div>
    </div>
@endsection
@section('styles')
    <link href="{{ asset('default/css/news.css') }}" rel="stylesheet" />
@endsection
@section('script')
<script>
    var dataCounter = {
        url: "{{ route('news/viewer') }}",
        'id': {{ $id }}
    };
</script>
    <script defer="defer" async="async" type="text/javascript" src="{{ asset('default/js/news.js') }}"></script>
@endsection
