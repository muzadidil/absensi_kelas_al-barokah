<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahan untuk JILID 6-10.
 * - theme : 'malam' (JILID 1-5) atau 'pagi' (JILID 6-10) — hanya mengubah suasana
 *   langit/markas, tidak mengubah aturan main.
 * - bullet_returns : peluru yang diketik MEMANTUL balik ke boss dan meledak di sana,
 *   bukan hancur di tempat. Sengaja per-JILID supaya JILID 1-5 tetap seperti semula.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meteor_game_levels', function (Blueprint $table) {
            $table->string('theme')->default('malam')->after('display_label');
            $table->boolean('bullet_returns')->default(false)->after('boss_hp');
        });
    }

    public function down(): void
    {
        Schema::table('meteor_game_levels', function (Blueprint $table) {
            $table->dropColumn(['theme', 'bullet_returns']);
        });
    }
};
