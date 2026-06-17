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
        // 🛠️ Menggunakan getenv() agar kebal dari batasan cache Laravel
        if (getenv('VERCEL') || isset($_SERVER['VERCEL'])) {
            $storageFolders = [
                '/tmp/storage/framework/views',
                '/tmp/storage/framework/cache',
                '/tmp/storage/framework/sessions',
                '/tmp/bootstrap/cache'
            ];

            foreach ($storageFolders as $folder) {
                if (!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}