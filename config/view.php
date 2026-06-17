<?php

return [
    'paths' => [
        resource_path('views'),
    ],

    // Pastikan baris ini mengarah ke folder storage di /tmp
    'compiled' => env('VIEW_COMPILED_PATH', '/tmp/storage/framework/views'),
];