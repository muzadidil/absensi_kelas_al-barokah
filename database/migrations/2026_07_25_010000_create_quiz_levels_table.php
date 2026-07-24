<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tahap Kuis Pilihan Ganda berjenjang (kembaran typing_levels).
 * - level_number : urutan tahap (unik).
 * - reset_to_first_on_fail : "Mode Pamungkas" — kalau gagal di tahap ini,
 *   murid dilempar balik ke Tahap 1 (semua kunci menutup lagi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('level_number')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('reset_to_first_on_fail')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_levels');
    }
};
