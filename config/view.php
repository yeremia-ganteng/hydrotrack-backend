<?php

return [
    'paths' => [
        resource_path('views'),
    ],

    // Pastikan baris ini mengarah ke folder storage di /tmp
    'compiled' => env('VERCEL') ? '/tmp/storage/framework/views' : storage_path('framework/views'),
];