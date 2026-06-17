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
        // Tempat middleware kamu jika ada
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ⚡ RAW PHP ESCAPE HATCH FOR VERCEL (ANTI DOUBLE-CRASH)
        $exceptions->render(function (\Throwable $e) {
            if (isset($_ENV['VERCEL']) || isset($_ENV['NOW_REGION'])) {
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
                
                exit(1);
            }
        });
    })
    ->create(); // <--- Membuat instance application di sini

// ⚡ BYPASS READ-ONLY FILESYSTEM VERCEL
if (isset($_ENV['VERCEL']) || isset($_ENV['NOW_REGION'])) {
    // Paksa Laravel menggunakan /tmp untuk folder storage dan bootstrap cache
    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');

    // Buat foldernya secara gaib di serverless jika belum tersedia
    if (!is_dir('/tmp/bootstrap/cache')) {
        mkdir('/tmp/bootstrap/cache', 0755, true);
    }
    if (!is_dir('/tmp/storage/framework/cache')) {
        mkdir('/tmp/storage/framework/cache', 0755, true);
    }
}

return $app; // <--- Kembalikan instance aplikasi yang sudah dimodifikasi jalur lokasinya