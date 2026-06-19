<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| 1. Siapkan Struktur Folder Writable di /tmp Vercel
|--------------------------------------------------------------------------
*/
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/framework/views',
    $storagePath . '/framework/sessions', 
    $storagePath . '/framework/cache/data',
    $storagePath . '/logs',
    $storagePath . '/app/public',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

/*
|--------------------------------------------------------------------------
| 2. Atur Environment Runtime Serverless
|--------------------------------------------------------------------------
*/
putenv('CACHE_STORE=array');
putenv('SESSION_DRIVER=cookie');
putenv('LOG_CHANNEL=stderr');

// 🔥 TRIK SAKTI: Paksa Vercel agar tidak memotong prefix /api pada URL Laravel
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

/*
|--------------------------------------------------------------------------
| 3. Bootstrapping Laravel 11 & Suntik Path Storage secara Paksa
|--------------------------------------------------------------------------
*/

// Muat Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Muat Object Aplikasi Laravel dari bootstrap
$app = require_once __DIR__ . '/../bootstrap/app.php';

// PANDU LARAVEL: Gunakan /tmp/storage sebagai satu-satunya jalur penulisan data!
$app->useStoragePath($storagePath);

/*
|--------------------------------------------------------------------------
| 4. Eksekusi Request API lewat HTTP Kernel (Aman untuk Subfolder Vercel)
|--------------------------------------------------------------------------
*/
// Ambil Http Kernel dari container Laravel
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Tangkap request yang masuk dari internet
$request = \Illuminate\Http\Request::capture();

// Jalankan request melalui kernel dan dapatkan responnya
$response = $kernel->handle($request);

// Kirim balik respon tersebut ke client (Browser / Flutter)
$response->send();

// Selesaikan siklus hidup request laravel
$kernel->terminate($request, $response);