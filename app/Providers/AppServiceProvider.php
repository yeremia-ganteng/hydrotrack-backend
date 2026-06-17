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
        // 🛠️ Logika Otomatis Khusus Vercel Serverless
        // Membuat folder yang writable di direktori /tmp sebelum Laravel dijalankan
        if (env('VERCEL')) {
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