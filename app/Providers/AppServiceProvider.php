<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Cek apakah aplikasi berjalan di lingkungan Vercel/Production
        if (env('VERCEL') || config('app.env') === 'production') {
            
            /** @var \Illuminate\Foundation\Application $app */
            $app = $this->app;

            // Atur ulang path storage ke folder /tmp yang writable di Vercel
            $app->useStoragePath('/tmp/storage');
            
            // Secara otomatis buat struktur folder yang dibutuhkan jika belum ada
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

            // Paksa compiler view Blade untuk menggunakan folder /tmp
            config(['view.compiled' => '/tmp/storage/framework/views']);
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
