<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// 1. PASTIKAN FOLDER COMPILED VIEW DI /TMP SUDAH DIBUAT SEBELUM LARAVEL BERJALAN
if (isset($_SERVER['VERCEL']) || env('VERCEL')) {
    $folders = [
        '/tmp/views', 
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/sessions'
    ];
    foreach ($folders as $folder) {
        if (!is_dir($folder)) {
            @mkdir($folder, 0755, true);
        }
    }
}

return Application::configure(basePath: dirname(__DIR__))
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
        // 2. TANGKAP ERROR API AGAR MENAMPILKAN ERROR ASLI DI CONTROLLER
        $exceptions->render(function (\Throwable $e) {
            if (request()->is('api/*') || request()->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ], 500);
            }
        });
    })->create();