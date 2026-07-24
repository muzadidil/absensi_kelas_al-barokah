<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hasil per percobaan berbasis KATA:
 * - correct_words / wrong_words / total_words : koreksi jumlah kata.
 * - passed : apakah percobaan ini memenuhi ambang lulus tahap (dihitung server).
 *
 * Kolom `accuracy` lama dipertahankan (kini diisi akurasi berbasis kata).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('typing_attempts', function (Blueprint $table) {
            $table->unsignedSmallInteger('correct_words')->default(0)->after('accuracy');
            $table->unsignedSmallInteger('wrong_words')->default(0)->after('correct_words');
            $table->unsignedSmallInteger('total_words')->default(0)->after('wrong_words');
            $table->boolean('passed')->default(false)->after('total_words');
        });
    }

    public function down(): void
    {
        Schema::table('typing_attempts', function (Blueprint $table) {
            $table->dropColumn(['correct_words', 'wrong_words', 'total_words', 'passed']);
        });
    }
};
