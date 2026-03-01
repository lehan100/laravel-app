@extends('admin.layouts.default')
@php
    use Illuminate\Support\Carbon;
    use App\Helpers\Template as Template;
    use App\Helpers\Format as Format;
    $IMAGE = new \App\Helpers\Product\Image();
    $configPath = config('image.path.rating');
@endphp
@section('title', $metaTitle)
@section('content')
    @include('admin.templates.page_title')
    <div class="x_panel p-0 border-0">
        <div class="x_content p-0 m-0">
            @if (count($items) > 0)
                @include ('admin.templates.notify')
                {{ html()->form('POST', '')->attributes([
                'accept-charset' => 'UTF-8',
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal form-label-left',
                'id' => 'appForm',
            ])->open() }}
                <table class="table table-striped jambo_table m-0">
                    <thead>
                        <tr>
                            @include('admin.templates.thead.check_all')
                            @include('admin.templates.thead.column', ['name' => 'Thời gian'])
                            @include('admin.templates.thead.column', ['name' => 'Khách hàng'])
                            @include('admin.templates.thead.column', ['name' => 'Đánh giá'])
                            @include('admin.templates.thead.active')
                            @include('admin.templates.thead.action')
                            @include('admin.templates.thead.id')
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $val)
                            @php
                                $name = $val['name'];
                                $phone = Format::formatPhone($val['phone']);
                                $content = $val['content'];
                                $rating_star = \App\Helpers\Product\Rating::adminRatingCustomerReview($val->rating);
                                $id = $val['id'];
                                $is_purchase = $val->is_purchase;
                                $createdAt = Carbon::parse($val->created_at);
                                $created_at = $createdAt->format('d/m/Y h:m:s');
                                $ratingImages = $val->images;
                                if ($ratingImages != '') {
                                    $ratingImages = explode(',', $ratingImages);
                                }

                                $product = $val->product;
                                $productName = $product->name;
                                $image = $IMAGE->getLinkDefault($product, 'small');
                                $link = url($product->url['path']);

                                $status = Template::showStatusContact($controllerName, $val['status'], $id);
                                $buttonAction = Template::showButtomAction($controllerName, $id);
                                //$linkEdit = route($controllerName . "/form", ['id' => $id]);
                            @endphp
                            <tr>
                                <td><input type="checkbox" name="aid[]" value="{{ $id }}"></td>
                                <td>{{ $created_at }}</td>
                                <td>
                                    <p class="mb-1"><strong>{{ $name }}</strong>, {{ $phone }}</p>
                                    @if ($is_purchase == 1)
                                        <p><span class="text-success ms-2"><i class="fa fa-check-circle mr-2"></i>Đã mua
                                                hàng</span></p>
                                    @endif
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-auto">
                                            <p><img width="80" src="{{ $image }}" alt="{{ $productName }}"></p>
                                        </div>
                                        <div class="col">
                                            <p class="mb-1"><strong>{{ $productName }} <a target="_blank"
                                                        href="{{ $link }}"><i
                                                            class="ml-2 text-info fa fa-eye"></i></a></strong></p>
                                            <p class="text-warning">{!! $rating_star !!}</p>
                                            <p>{{ $content }}</p>
                                            @if ($ratingImages != '')
                                                <div class="row mt-3">
                                                    @foreach ($ratingImages as $image)
                                                        @php
                                                            $image_src = asset($configPath['thumb'] . '/' . $image);
                                                            $image_src_zoom = asset($configPath['path'] . '/' . $image);
                                                        @endphp
                                                        <div class="col-auto">
                                                            <a data-fancybox="gallery"
                                                                data-src="{{$image_src_zoom}}">
                                                                <img src="{{ $image_src }}" alt="{{ $productName }}"
                                                                    width="80px" />
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </td>
                                <td class="text-center">{!! $status !!}</td>
                                <td class="text-center">{!! $buttonAction !!}</td>
                                <td class="text-center">{{ $id }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ html()->form()->close() }}
                @include('pagination.pagination_admin')
            @else
                @include('admin.templates.list_empty')
            @endif
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('admin/build/js/fancybox.umd.js') }}"></script>
    <script>
        Fancybox.bind('[data-fancybox="gallery"]', {
          //
        });    
      </script>
@endsection
@section('style')
    <link href="{{ asset('admin/build/css/fancybox.css') }}" rel="stylesheet" />
@endsection
