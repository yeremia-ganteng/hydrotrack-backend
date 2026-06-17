<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
public function register(): void
{
    if (env('VERCEL')) {
        // 1. Alihkan path storage utama seperti yang sudah kamu lakukan
        $this->app->instance('path.storage', '/tmp/storage');

        // 2. PAKSA konfigurasi 'view.compiled' yang terkunci di Fase 1 untuk pindah ke /tmp
        config(['view.compiled' => '/tmp/storage/framework/views']);
    }
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Jalankan pembuatan folder saat aplikasi sudah siap (booting)
        if (env('VERCEL')) {
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
    }
}