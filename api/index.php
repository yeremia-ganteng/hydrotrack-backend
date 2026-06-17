<?php

// Redirect semua Laravel cache ke /tmp (writable di Vercel)
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');

// Buat tmp dirs
$dirs = ['/tmp/views', '/tmp/storage/framework/sessions',
         '/tmp/storage/framework/cache/data', '/tmp/storage/logs'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0775, true);
}

$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/views';

require __DIR__ . '/../public/index.php';