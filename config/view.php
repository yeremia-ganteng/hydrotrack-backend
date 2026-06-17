<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage History Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path (Bypass Khusus Vercel Serverless)
    |--------------------------------------------------------------------------
    */

    // Jika di Vercel, tembak langsung jalur /tmp yang writable. Jika lokal, gunakan storage_path normal.
    'compiled' => env('VERCEL') 
        ? '/tmp/storage/framework/views' 
        : storage_path('framework/views'),

];