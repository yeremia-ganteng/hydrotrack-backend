<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('water_logs', function (Blueprint $table) {
            // 1. Tambahkan kolom user_id yang merujuk ke tabel users
            // constrained() otomatis membuat relasi ke tabel 'users'
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            
            // 2. Jika Anda tetap ingin mempertahankan kolom 'date'
            $table->date('date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('water_logs', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};