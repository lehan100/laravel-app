@if (count($listRatings) > 0)
    @foreach ($listRatings as $k => $rating)
        @php
            $ratingName = $rating->name;
            $ratingContent = $rating->content;
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
            <div class="content">{{ $ratingContent }}</div>
            <div class="date text-end text-secondary mb-1 small ">
                {{ $date }}
            </div>
        </div>
    @endforeach
@else
<p class="alert alert-warning p-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Không có đánh giá nào!</p>
@endif
