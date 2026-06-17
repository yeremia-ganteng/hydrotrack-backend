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
        // Cukup set path storage di sini jika di Vercel
        if (env('VERCEL')) {
            $this->app->instance('path.storage', '/tmp/storage');
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