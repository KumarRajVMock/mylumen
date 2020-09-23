<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'registration',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'registration',
        ],
    ],

    'providers' => [
        'registration' => [
            'driver' => 'eloquent',
            // 'model' => \App\User::class

            'model' => \App\Models\Registration::class,
        ]
    ]
];
