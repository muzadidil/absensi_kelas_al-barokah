<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Batas waktu (timeout) latihan per tahap, fleksibel:
 * - 0  = tanpa batas (murid selesaikan semua kata).
 * - >0 = mode berwaktu (mis. 60 detik = 1 menit); waktu habis, latihan otomatis
 *        selesai dan dinilai dari kata yang sempat dikerjakan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('typing_levels', function (Blueprint $table) {
            $table->unsignedSmallInteger('time_limit_seconds')->default(0)->after('max_error_percent');
        });
    }

    public function down(): void
    {
        Schema::table('typing_levels', function (Blueprint $table) {
            $table->dropColumn('time_limit_seconds');
        });
    }
};
