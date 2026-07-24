<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aturan latihan per tahap (diatur Guru):
 * - allow_backspace / allow_space : mode ketik (backspace & spasi boleh/tidak).
 * - min_wpm            : kecepatan minimal (kata/menit) untuk lulus.
 * - min_accuracy       : kebenaran minimal (% kata benar) untuk lulus.
 * - max_error_percent  : toleransi kesalahan (% kata salah maksimal) untuk lulus.
 *
 * Nilai default sengaja "tidak menyaring" (min 0, toleransi 100) supaya tahap
 * lama tetap bisa dilewati sampai Guru mengisi ambangnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('typing_levels', function (Blueprint $table) {
            $table->boolean('allow_backspace')->default(true)->after('word_bank');
            $table->boolean('allow_space')->default(true)->after('allow_backspace');
            $table->unsignedSmallInteger('min_wpm')->default(0)->after('allow_space');
            $table->unsignedTinyInteger('min_accuracy')->default(0)->after('min_wpm');
            $table->unsignedTinyInteger('max_error_percent')->default(100)->after('min_accuracy');
        });
    }

    public function down(): void
    {
        Schema::table('typing_levels', function (Blueprint $table) {
            $table->dropColumn(['allow_backspace', 'allow_space', 'min_wpm', 'min_accuracy', 'max_error_percent']);
        });
    }
};
