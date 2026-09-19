<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Memisahkan isi gelombang meteor dari isi peluru boss, dan membuka mode kata.
 *
 * - boss_keys  : huruf khusus peluru boss. Kosong = ikut allowed_keys (huruf meteor).
 * - wave_words : bank kata untuk gelombang meteor. Kalau diisi, fase itu memakai
 *                KATA, bukan huruf — jadi tidak perlu kolom "mode" terpisah.
 * - boss_words : bank kata untuk peluru boss, aturannya sama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meteor_game_levels', function (Blueprint $table) {
            $table->string('boss_keys')->nullable()->after('allowed_keys');
            $table->text('wave_words')->nullable()->after('boss_keys');
            $table->text('boss_words')->nullable()->after('wave_words');
        });
    }

    public function down(): void
    {
        Schema::table('meteor_game_levels', function (Blueprint $table) {
            $table->dropColumn(['boss_keys', 'wave_words', 'boss_words']);
        });
    }
};
