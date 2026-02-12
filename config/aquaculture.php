<?php

return [
    'default_species' => env('FISH_SPECIES', 'tilapia'),
    'species' => [
        'tilapia' => [
            'label' => 'Tilapia',
            'ph' => ['optimal' => [5.5, 8.5], 'safe' => [5.0, 9.0]],
            'temperature' => ['optimal' => [26, 30], 'safe' => [22, 34]],
            'tds' => ['optimal' => [100, 400], 'safe' => [0, 600]],
            'turbidity' => ['optimal' => [0, 15], 'safe' => [0, 25]],
        ],
        'catfish' => [
            'label' => 'Catfish',
            'ph' => ['optimal' => [6.5, 8.0], 'safe' => [6.0, 8.5]],
            'temperature' => ['optimal' => [24, 30], 'safe' => [20, 33]],
            'tds' => ['optimal' => [100, 500], 'safe' => [0, 700]],
            'turbidity' => ['optimal' => [0, 20], 'safe' => [0, 30]],
        ],
        'carp' => [
            'label' => 'Carp',
            'ph' => ['optimal' => [6.8, 8.0], 'safe' => [6.2, 8.5]],
            'temperature' => ['optimal' => [23, 28], 'safe' => [18, 32]],
            'tds' => ['optimal' => [100, 500], 'safe' => [0, 700]],
            'turbidity' => ['optimal' => [0, 20], 'safe' => [0, 30]],
        ],
    ],
    'trend' => [
        'turbidity' => 5,
        'tds' => 50,
        'ph' => 0.3,
        'temperature' => 1.0,
    ],
];
