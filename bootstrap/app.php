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
        // ⚡ RAW PHP ESCAPE HATCH FOR VERCEL (ANTI DOUBLE-CRASH)
        $exceptions->render(function (\Throwable $e) {
            if (isset($_ENV['VERCEL']) || isset($_ENV['NOW_REGION'])) {
                // WAJIB PAKAI PHP MURNI: Jangan pakai helper response() atau json() bawaan Laravel
                header('Content-Type: application/json');
                http_response_code(500);
                
                echo json_encode([
                    '🔥_STATUS_EROR' => '🚨 EROR ASLI BERHASIL DICEGAT 🚨',
                    'tipe_exception' => get_class($e),
                    'pesan_error'   => $e->getMessage(),
                    'file_terkait'  => $e->getFile(),
                    'baris_line'    => $e->getLine(),
                    'trace_singkat' => explode("\n", substr($e->getTraceAsString(), 0, 1500))
                ], JSON_PRETTY_PRINT);
                
                exit(1); // Hentikan paksa seluruh proses script saat ini juga!
            }
        });
    })->create();

// 2. Alihkan seluruh sistem Storage Laravel ke folder /tmp yang baru dibuat
if (isset($_ENV['VERCEL']) || isset($_ENV['NOW_REGION'])) {
    $app->useStoragePath('/tmp/storage');
}

return $app;