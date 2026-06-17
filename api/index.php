<?php

$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/framework/views',
    $storagePath . '/framework/sessions', 
    $storagePath . '/framework/cache/data',
    $storagePath . '/logs',
    $storagePath . '/app/public',
    '/tmp/views',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0775, true);
}

putenv('LARAVEL_STORAGE_PATH=' . $storagePath);
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
putenv('CACHE_STORE=array');
putenv('SESSION_DRIVER=cookie');
putenv('LOG_CHANNEL=stderr');

// Tangkap error PHP apapun
ini_set('display_errors', 1);
error_reporting(E_ALL);

set_exception_handler(function($e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => substr($e->getTraceAsString(), 0, 500)
    ]);
});

require __DIR__ . '/../public/index.php';