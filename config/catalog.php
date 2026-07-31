<?php

return [
    'commission_rate' => 0.15,
    'colors' => ['White', 'Black', 'Gray'],
    'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL'],
    'types' => [
        'shirt' => [
            'label' => 'Shirt',
            'price' => 35.00,
            'front_position' => ['x' => 240, 'y' => 208, 'w' => 405, 'h' => 525],
            'back_position' => ['x' => 240, 'y' => 208, 'w' => 405, 'h' => 525],
        ],
        'sweat' => [
            'label' => 'Sweatshirt',
            'price' => 50.00,
            'front_position' => ['x' => 250, 'y' => 190, 'w' => 380, 'h' => 525],
            'back_position' => ['x' => 250, 'y' => 176, 'w' => 380, 'h' => 525],
        ],
        'hoodie' => [
            'label' => 'Hoodie',
            'price' => 70.00,
            'front_position' => ['x' => 260, 'y' => 190, 'w' => 360, 'h' => 525],
            'back_position' => ['x' => 260, 'y' => 176, 'w' => 360, 'h' => 525],
        ],
    ],
];
