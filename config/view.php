<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage History Paths
    |--------------------------------------------------------------------------
    |
    | Sebagian besar sistem perpustakaan template memuat template dari disk.
    | Di sini kamu dapat menentukan array jalur yang harus diperiksa
    | untuk mencari file view (Blade) aplikasi kamu.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path (Bypass Khusus Vercel Serverless)
    |--------------------------------------------------------------------------
    |
    | Opsi ini menentukan di mana semua template Blade yang telah dikompilasi
    | akan disimpan. Biasanya di dalam folder storage, tetapi karena Vercel
    | bersifat read-only, kita alihkan langsung ke root direktori '/tmp'.
    |
    */

    'compiled' => env('VERCEL') ? '/tmp' : storage_path('framework/views'),

];