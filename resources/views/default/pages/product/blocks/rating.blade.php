@php
    $configPath = config('image.path.rating');
@endphp
<div id="block_review" class="row mt-4 mb-5">
    <div class="col-12 col-md-8">
        @if (count($listRatings) > 0)
            <div class="block-rating border rounded p-3">
                <h3 class="title mb-3">Đánh giá {{ $productName }}</h3>
                <div class="box-star">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-5">
                            {!! $ratingObject->ratingToStringPoint() !!}
                            <div class="summary-ratings">
                                {!! $ratingObject->summaryRatingToString() !!}
                            </div>
                        </div>
                        <div class="col-12 col-md-7 text-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#ratingModal"
                                class="btn btn-info px-3 py-1 text-white">Viết đánh giá</button>
                        </div>
                    </div>
                </div>
                <div id="review-photo"></div>
                <div class="review-list border-top pt-3 mt-3">
                    <div class="review-toolbar mb-3">
                        <div class="row align-items-center">
                            <div class="col-auto"><strong>Lọc đánh giá</strong></div>
                            <div class="col-auto"><button data-star="5" class="btn-filter btn btn-outline-secondary text-warning">5<i
                                        class="bi bi-star-fill ms-2"></i></button></div>
                            <div class="col-auto"><button data-star="4" class="btn-filter btn btn-outline-secondary text-warning">4<i
                                        class="bi bi-star-fill ms-2"></i></button></div>
                            <div class="col-auto"><button data-star="3" class="btn-filter btn btn-outline-secondary text-warning">3<i
                                        class="bi bi-star-fill ms-2"></i></button></div>
                            <div class="col-auto"><button data-star="2" class="btn-filter btn btn-outline-secondary text-warning">2<i
                                        class="bi bi-star-fill ms-2"></i></button></div>
                            <div class="col-auto"><button data-star="1" class="btn-filter btn btn-outline-secondary text-warning">1<i
                                        class="bi bi-star-fill ms-2"></i></button></div>
                        </div>
                    </div>
                    <div id="loadDataRating">
                        @foreach ($listRatings as $k => $rating)
                            @php
                                $ratingName = $rating->name;
                                $ratingContent = $rating->content;
                                $ratingImages = $rating->images;
                                if($ratingImages !=""){
                                    $ratingImages = explode(",", $ratingImages);
                                }
                                
                                $is_purchase = $rating->is_purchase;
                                $rating_star = \App\Helpers\Product\Rating::ratingCustomerReview($rating->rating);
                                $date = \App\Helpers\Format::dateToString($rating->created_at);
                            @endphp
                            <div class="item pt-3 mb-2 border-bottom">
                                <div class="name mb-1">
                                    <strong>{{ $ratingName }}</strong>
                                    @if ($is_purchase == 1)
                                        <span class="text-success ms-2"><i class="bi bi-check2-circle me-2"></i>Đã mua
                                            hàng</span>
                                    @endif
                                </div>
                                <div class="star text-warning mb-2">
                                    {!! $rating_star !!}
                                </div>
                                <div class="content">
                                    {{ $ratingContent }}
                                    @if ($ratingImages !="")
                                        <div class="row mt-3">
                                            @foreach ($ratingImages as $image)
                                            @php
                                                $image_src_zoom = asset($configPath['path']."/".$image);
                                                $image_src = asset($configPath['thumb']."/".$image);
                                            @endphp
                                                <div class="col-auto">
                                                    <a class="fancybox-zoom" data-src='{{$image}}'>
                                                    <img src="{{$image_src}}" alt="{{$productName}}" class="border rounded" width="80px"/>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                </div>
                                <div class="date text-end text-secondary mb-1 small ">
                                    {{ $date }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div id="loadDataRatingPagination">
                    @include('pagination.pagination', ['listItems' => $listRatings])
                </div>
            </div>
        @else
            <div class="block-rating border rounded p-4 text-center bg-light">
                <h3 class="title mb-3">Đánh giá {{ $productName }}</h3>
                <p>Nếu đã mua sản phẩm này. Hãy đánh giá ngay để giúp hàng ngàn người chọn mua hàng tốt nhất bạn
                    nhé!</p>
                <p class="mt-4"><button type="button" data-bs-toggle="modal" data-bs-target="#ratingModal"
                        class="btn btn-info px-3 py-1 text-white">Đánh giá ngay</button></p>
            </div>
        @endif

    </div>
</div>
{{-- Modal Review --}}
@section('modal')
    <div class="modal" id="ratingModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white">
                 {{ html()->form('POST', route("product/post-ranting"))->attributes([
                    'accept-charset' => 'UTF-8',
                    'enctype' => 'multipart/form-data',
                    'id' => 'formRate',
                ])->open() }}
                @php
                    $image = $IMAGE->getLinkDefault($item, 'small');
                    $starHiddenID = html()->hidden('rating', 0)->attributes( ['id' => 'rating_star']);
                    $productHiddenID = html()->hidden('product_id', $id)->attributes( ['id' => 'product_id']);
                    $inputName = html()->text('name', '')->attributes(['class' => 'form-control', 'placeholder' => 'Họ tên (bắt buộc)']);
                    $inputPhone = html()->text('phone', '')->attributes(['class' => 'form-control', 'placeholder' => 'Số điện thoại (bắt buộc)']);
                    $inputContent = html()->textarea('content', '')->attributes(['class' => 'form-control rounded', 'rows' => 4, 'cols' => 54, 'placeholder' => 'Mời bạn chia sẻ thêm cảm nhận...']);
                @endphp
                {!! $starHiddenID !!}
                {!! $productHiddenID !!}
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center">Đánh giá sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body px-3 text-center">
                    <p>
                        <img src="{{ $image }}" alt="{{ $productName }}">
                    </p>
                    <p><strong>{{ $productName }}</strong></p>
                    <ul class="rating-star row justify-content-center">
                        <button type="button" class="col-auto btn px-2" data-star="1">
                            <p class="mb-1 text-warning"><i class="bi bi-star"></i></p>
                            <p class="m-0 title">Rất tệ</p>
                        </button>
                        <button type="button" class="col-auto btn px-2" data-star="2">
                            <p class="mb-1 text-warning"><i class="bi bi-star"></i></p>
                            <p class="m-0 title">Tệ</p>
                        </button>
                        <button type="button" class="col-auto btn px-2" data-star="3">
                            <p class="mb-1 text-warning"><i class="bi bi-star"></i></p>
                            <p class="m-0 title">Tạm ổn</p>
                        </button>
                        <button type="button" class="col-auto btn px-2" data-star="4">
                            <p class="mb-1 text-warning"><i class="bi bi-star"></i></p>
                            <p class="m-0  title">Tốt</p>
                        </button>
                        <button type="button" class="col-auto btn px-2" data-star="5">
                            <p class="mb-1 text-warning"><i class="bi bi-star"></i></p>
                            <p class="m-0 title">Rất tốt</p>
                        </button>
                    </ul>
                    <div id="toggleformRate" class="d-none mt-4">
                        <div class="form-input mb-3">
                            {!! $inputContent !!}
                        </div>
                        <div class="form-input mb-3 upload-img">
                            <a href="javascript:void(0)" class="send-img text-info">
                                <i class="bi bi-camera me-2"></i>
                                <div>Gửi ảnh thực tế <span>(tối đa 3 ảnh)</span></div>
                            </a>
                            <input id="fileRatingUpload" name="ratingImg" type="file" data-token="{{csrf_token()}}" data-link-upload="{{route('rating.upload')}}" multiple accept="image/x-png, image/gif, image/jpeg" data-max_length="3" class="upload__inputfile d-none">
                            <input type="hidden" name="images" id="ratingImg" value="">
                            <div class="review row"></div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md">
                                <div class="form-input mb-3">
                                    {!! $inputName !!}
                                </div>
                            </div>
                            <div class="col-12 col-md">
                                <div class="form-input mb-3">
                                    {!! $inputPhone !!}
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-custom hangle-button btn-checkout w-100 py-2">Gửi
                            đánh giá</button>
                    </div>
                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
@endsection
{{-- Modal Review --}}
