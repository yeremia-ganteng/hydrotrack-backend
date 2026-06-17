<?php

return [

    'paths' => [
        resource_path('views'),
    ],

    // Amankan baris ini dengan getenv() juga
    'compiled' => (getenv('VERCEL') || isset($_SERVER['VERCEL']))
        ? '/tmp/storage/framework/views' 
        : storage_path('framework/views'),

];