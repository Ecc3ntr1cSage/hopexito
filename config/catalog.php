<?php

return [
    'commission_rate' => 0.15,
    'colors' => ['White', 'Black', 'Gray'],
    'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL'],
    'canvas' => ['width' => 850, 'height' => 900],
    'types' => [
        'shirt' => [
            'label' => 'Shirt',
            'price' => 35.00,
            'colors' => ['White', 'Black', 'Gray'],
            'front_position' => ['x' => 232, 'y' => 208, 'w' => 391, 'h' => 525],
            'back_position' => ['x' => 232, 'y' => 208, 'w' => 391, 'h' => 525],
        ],
        'sweat' => [
            'label' => 'Sweatshirt',
            'price' => 50.00,
            'colors' => ['White', 'Black', 'Gray'],
            'front_position' => ['x' => 241, 'y' => 190, 'w' => 367, 'h' => 525],
            'back_position' => ['x' => 241, 'y' => 176, 'w' => 367, 'h' => 525],
        ],
        'hoodie' => [
            'label' => 'Hoodie',
            'price' => 70.00,
            'colors' => ['Black', 'Gray', 'Navy', 'Green'],
            'front_position' => ['x' => 251, 'y' => 190, 'w' => 348, 'h' => 525],
            'back_position' => ['x' => 251, 'y' => 176, 'w' => 348, 'h' => 525],
        ],
    ],
];
