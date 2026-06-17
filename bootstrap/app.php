<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 1. Otomatis buat struktur folder di /tmp jika mendeteksi lingkungan Vercel
if (isset($_ENV['VERCEL']) || isset($_ENV['NOW_REGION'])) {
    $storageFolders = [
        '/tmp/storage/logs',
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/bootstrap/cache'
    ];
    foreach ($storageFolders as $folder) {
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
    }
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// 2. Alihkan seluruh sistem Storage Laravel ke folder /tmp yang baru dibuat
if (isset($_ENV['VERCEL']) || isset($_ENV['NOW_REGION'])) {
    $app->useStoragePath('/tmp/storage');
}

return $app;