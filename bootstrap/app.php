<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Tempat mengatur middleware global / web / api
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // 🛠️ SUNTIKAN DIAGNOSTIK: Paksa cetak error asli ke Vercel Log Stream
        $exceptions->report(function (\Throwable $e) {
            file_put_contents(
                'php://stderr', 
                "\n=========================================\n" . 
                "⚠️ ERROR ASLI YANG TERSEMBUNYI:\n" . 
                "Pesan : " . $e->getMessage() . "\n" .
                "File  : " . $e->getFile() . " (Baris: " . $e->getLine() . ")\n" .
                "=========================================\n"
            );
        });

    })->create();