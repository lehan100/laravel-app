<?php

namespace App\Helpers\Product;

class Rating
{
    protected $DATA_RATING;
    protected $DATA_RATING_TO_STAR;
    public function __construct()
    {
        $this->DATA_RATING_TO_STAR = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0
        ];
    }
    public function setTotalStar($total = 0)
    {
        $this->DATA_RATING['total_star'] = $total;
    }
    public function setTotalRating($total = 0)
    {
        $this->DATA_RATING['total_rating'] = $total;
    }
    public function getTotalStar()
    {
        return $this->DATA_RATING['total_star'];
    }
    public function getTotalRating()
    {
        return $this->DATA_RATING['total_rating'];
    }
    public function calcRating()
    {
        try {
            return round($this->getTotalStar() / $this->getTotalRating(), 1);
        } catch (\Throwable $th) {
            return 0;
        }
    }
    public function getRangting()
    {
        $rating = $this->calcRating();
        return floor($rating);
    }
    public function getStarBlank()
    {
        return (5 - $this->getRangting());
    }
    public function getStarHalf()
    {
        return ($this->calcRating() - $this->getRangting());
    }
    public function ratingToString($itemprop = false)
    {
        $rating = $this->calcRating();
        if ($rating == 0) {
            return '<i class="bi bi-star star-mx"></i><i class="bi bi-star star-mx"></i><i class="bi bi-star star-mx"></i><i class="bi bi-star star-mx"></i><i class="bi bi-star star-mx"></i>';
        }
        $star_fill = $this->getRangting();
        $star_half = $this->getStarHalf();
        $star_blank = $this->getStarBlank();
        $xhtml = "";
        for ($i = 0; $i < $star_fill; $i++) {
            $xhtml .= '<i class="bi bi-star-fill star-mx"></i>';
        }
        if ($star_half >= 0.5) {
            $xhtml .= '<i class="bi bi-star-half star-mx"></i>';
        } else if ($star_half < 0.5 && $star_half > 0) {
            $xhtml .= '<i class="bi bi-star star-mx"></i>';
        }
        if ($star_blank > 0) {
            for ($j = 1; $j < $star_blank; $j++) {
                $xhtml .= '<i class="bi bi-star star-mx"></i>';
            }
        }
		 if ($rating >0 && $itemprop) {
			$xhtml .= ' <span  itemprop="aggregateRating" itemtype="https://schema.org/AggregateRating" itemscope>';
			$xhtml .= sprintf('<meta itemprop="reviewCount" content="%s" />', $this->getTotalRating());
			 if ($star_half >= 0.5) {
				 $star_fill += 0.5;
			}
			$xhtml .= sprintf('<meta itemprop="ratingValue" content="%s" />', $star_fill);
			$xhtml .= ' </span>';
		 }
        return $xhtml;
    }
    public function ratingToStringInfo()
    {
        $xhtml = $this->ratingToString(true);
        $xhtml .= sprintf('<a href="javascript:;" class="review_now text-info ms-2">%s đánh giá </a>', $this->getTotalRating());
        return $xhtml;
    }
    public function ratingToStringProduct()
    {
        $xhtml = $this->ratingToString();
        $xhtml .= sprintf('<span class="review_avg text-secondary ms-1">%s</span>', $this->getTotalRating());
        return $xhtml;
    }
    public function ratingToStringPoint()
    {
        $rating = $this->calcRating();

        if ($rating == 0) {
            return false;
        }
        $xhtml = '<div class="point row text-warning fw-bold">';
        $xhtml .= '<div class="col-auto">';
        $xhtml .= sprintf('<span>%s</span>', $rating);
        $xhtml .= '</div>';
        $xhtml .= '<div class="col-auto px-0">';
        $xhtml .= $this->ratingToString();
        $xhtml .= '</div>';
        $xhtml .= '<div class="col-auto">';
        $xhtml .= sprintf('<a href="javascript:;" class="review_now text-info">%s đánh giá </a>', $this->getTotalRating());
        $xhtml .= '</div>';
        $xhtml .= '</div>';
        return $xhtml;
    }
   
    public static function ratingCustomerReview($rating = 0, $review = 0)
    {
        if ($rating > 0) {
            $star_blank = 5 - $rating;
            $xhtml = "";
            for ($i = 0; $i < $rating; $i++) {
                $xhtml .= '<i class="bi bi-star-fill star-mx"></i>';
            }
            if ($star_blank > 0) {
                for ($i = 0; $i < $star_blank; $i++) {
                    $xhtml .= '<i class="bi bi-star star-mx"></i>';
                }
            }
            return $xhtml;
        }
    }
    public static function adminRatingCustomerReview($rating = 0, $review = 0)
    {
        if ($rating > 0) {
            $star_blank = 5 - $rating;
            $xhtml = "";
            for ($i = 0; $i < $rating; $i++) {
                $xhtml .= '<i class="fa fa-star mx-1"></i>';
            }
            if ($star_blank > 0) {
                for ($i = 0; $i < $star_blank; $i++) {
                    $xhtml .= '<i class="fa fa-star-o mx-1"></i>';
                }
            }
            return $xhtml;
        }
    }
    // Data Rating To Star
    public function setDataRatingToStar($star = 0, $rating = 0)
    {
        if ($star > 0) {
            $this->DATA_RATING_TO_STAR[$star] = $rating;
        }
    }
    public function getDataRatingToStar()
    {
        return $this->DATA_RATING_TO_STAR;
    }
    public function summaryRatingToString()
    {
        $xhtml = "";
        $totalStar = $this->getTotalRating();
        foreach ($this->getDataRatingToStar() as $star => $rating) {
            $percentRating = round(($rating /  $totalStar) * 100);
            $xhtml .= '<div class="rating row align-items-center my-2">';
            $xhtml .= '<div class="rating-title col-auto">';
            $xhtml .= sprintf(' %s <i class="bi bi-star-fill"></i>', $star);
            $xhtml .= '</div>';
            $xhtml .= '<div class="rating-bar col px-0">';
            $xhtml .= sprintf('<div class="progress" role="progressbar" aria-label="%s Sao" aria-valuenow="%s" aria-valuemin="0" aria-valuemax="100">', $star, $percentRating);
            $xhtml .= sprintf(' <div class="progress-bar text-bg-warning" style="width: %s"></div>', $percentRating . "%");
            $xhtml .= '</div>';
            $xhtml .= '</div>';
            $xhtml .= sprintf('<div class="rating-count col-1">%s</div>',  $percentRating . "%");
            $xhtml .= '</div>';
        }
        return $xhtml;
    }
}
