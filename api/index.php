<?php

// Clear cached routes untuk Vercel
if (file_exists(__DIR__ . '/../bootstrap/cache/routes-v7.php')) {
    unlink(__DIR__ . '/../bootstrap/cache/routes-v7.php');
}

// Buat tmp dirs
$dirs = ['/tmp/views', '/tmp/storage/framework/sessions',
         '/tmp/storage/framework/cache/data', '/tmp/storage/logs'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0775, true);
}

$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/views';

require __DIR__ . '/../public/index.php';