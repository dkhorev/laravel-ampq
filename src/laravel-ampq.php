<?php

declare(strict_types=1);

use dkhorev\LaravelAmpq\Listeners\ExampleListenerCallback;

return [
    'servers' => [
        'local' => [
            'host'     => env('AMPQ_HOST', ''),
            'user'     => env('AMPQ_USER', ''),
            'password' => env('AMPQ_PASSWORD', ''),
            'port'     => env('AMPQ_PORT', 5672),
        ],

        'public' => [
            'host'     => env('AMPQ_HOST_PUBLIC', ''),
            'user'     => env('AMPQ_USER_PUBLIC', ''),
            'password' => env('AMPQ_PASSWORD_PUBLIC', ''),
            'port'     => env('AMPQ_PORT_PUBLIC', 5672),
        ],
    ],

    'callbacks' => [
        'example_stack' => [
            'exchange' => [
                'topic' => ExampleListenerCallback::class,
            ],
        ],
    ],
];
