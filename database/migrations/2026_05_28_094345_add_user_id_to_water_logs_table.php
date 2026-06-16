<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 💡 Cek apakah kolom 'user_id' BELUM ADA di tabel 'water_logs'
        if (!Schema::hasColumn('water_logs', 'user_id')) {
            Schema::table('water_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(); // Menambahkan kolom user_id
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // 💡 Cek apakah kolom 'user_id' ADA sebelum mencoba menghapusnya
        if (Schema::hasColumn('water_logs', 'user_id')) {
            Schema::table('water_logs', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};