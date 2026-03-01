<?php

return [
    'sizes' => [
        'product' => [
            'small' => [150, 150],
            'medium' => [400, 400],
            'large' => [800, 800],
        ],
        'option' => [800, 800],
        'attribute_set' => [320, 180],
        'category' => [480, 480],
        'post' => [900, 506],
        'photo' => [1200, null],
        'rating' => [900, null]
    ],
    'admin' => [
        'product' => [
            'width' => 80,
            'height' => 80
        ],
        'category' => [
            'width' => 30,
            'height' => 30
        ],
        'post' => [
            'width' => 80,
            'height' => 'auto'
        ],
        'photo' => [
            'width' => 80,
            'height' => 'auto'
        ],
        'rating' => [
            'width' => 80,
            'height' => 80
        ],
        'attribute_set' => [
            'width' => 80,
            'height' => 'auto'
        ]
    ],
    'path' => [
        'wysiwyg'=>[
            'path'=>'media/wysiwyg/images'
        ],
        'product' => [
            "temp" => 'var/product',
            'path' => 'media/product',
            'trash' => 'media/trash/product',
            'size'=>'product'
        ],
        'product_option' => [
            "temp" => 'var/option',
            'path' => 'media/product/option',
            'trash' => 'media/trash/product/option',
            'size'=>'product'
        ],
        'attribute_set' => [
            "temp" => 'var/attribute',
            'path' => 'media/attribute',
            'trash' => 'media/trash/attribute',
            'size'=>'attribute_set'
        ],
        'category' => [
            "temp" => 'var/temp',
            'path' => 'media/category',
            'trash' => 'media/trash/category',
            'size'=>'category'
        ],
        'post' => [
            "temp" => 'var/temp',
            'path' => 'media/post',
            'thumb' => 'media/thumb/post',
            'trash' => 'media/trash/post',
            'size'=>'post'
        ],
        'photo' => [
            "temp" => 'var/temp',
            'path' => 'media/photo',
            'trash' => 'media/trash/photo',
            'size'=>'photo'
        ],
        'rating' => [
            "temp" => 'var/temp',
            'path' => 'media/rating',
            'thumb' => 'media/thumb/rating',
            'trash' => 'media/trash/rating',
            'size'=>'rating'
        ]
    ]
];
