<?php

namespace App\Helpers\Product;

class Sort {

    protected $_dataSort = [
        'position' => ['label' => 'Mới nhất', 'sort' => 'created_at.ASC'],
        'hot' => ['label' => 'Nổi bật', 'sort' => 'created_at.ASC'],
        'best_seller' => ['label' => 'Bán chạy', 'sort' => 'created_at.ASC'],
        'price_heigh_to_low' => ['label' => 'Giá cao đến thấp', 'sort' => 'price.ASC'],
        'price_low_to_high' => ['label' => 'Giá thấp đến cao', 'sort' => 'price.DESC']
    ];

    public static function getData() {
        return (new Sort())->_dataSort;
    }

    public static function toString($filter = 'position') {
        $xhtml = '<div class="toolbar row align-items-center justify-content-end pb-3 mx-1">';
        $xhtml .= '<div class="col-auto d-none d-xl-block">Sắp xếp theo</div>';
        foreach (self::getData() as $key => $item) {
            if ($key == $filter) {
                $xhtml .= sprintf(' <div class="col-6 col-md-auto px-1"><button data-sort="%s" class="sort-item active btn btn-info text-white rounded-5 px-3">%s</button></div>', $key, $item['label']);
            } else {
                $xhtml .= sprintf('<div class="col-6 col-md-auto px-1"><button data-sort="%s" class="sort-item btn btn-outline-info rounded-5 px-3">%s</button></div>', $key, $item['label']);
            }
        }
        $xhtml .= sprintf('<div class="col-6 col-md-auto px-1 d-block d-xl-none"><button class="sort-filter btn btn-outline-info rounded-5 px-3">Lọc<i class="ms-2 bi bi-funnel"></i></button></div>');
        $xhtml .= '</div>';
        return $xhtml;
    }
}
