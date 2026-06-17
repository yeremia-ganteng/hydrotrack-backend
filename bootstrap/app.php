<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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

/*
|--------------------------------------------------------------------------
| Inisialisasi Runtime Folder Vercel
|--------------------------------------------------------------------------
*/
if (env('VERCEL')) {
    // Set instance path storage utama sejak awal aplikasi di-load
    $app->instance('path.storage', '/tmp/storage');

    $storageFolders = [
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

return $app;