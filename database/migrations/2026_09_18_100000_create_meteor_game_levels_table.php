<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * JILID Game 10 Jari (lihat RENCANA_GAME_10_JARI.md).
 * - wave_target / spawn_interval_ms : kesulitan naik dari JUMLAH meteor,
 *   bukan dari kecepatan jatuh — kecepatan sengaja konstan di JILID 1-5.
 * - is_checkpoint : titik simpan. Gagal setelah menembusnya tidak melempar
 *   murid balik ke JILID 1, cukup ke checkpoint terakhir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meteor_game_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('level_number')->unique();
            $table->string('display_label');
            $table->string('allowed_keys')->default('asdfghjkl;');
            $table->unsignedTinyInteger('lives')->default(5);
            $table->unsignedSmallInteger('wave_target')->default(12);
            $table->unsignedSmallInteger('spawn_interval_ms')->default(1600);
            $table->string('boss_name');
            $table->string('boss_weapon_name');
            $table->unsignedTinyInteger('boss_bullets_per_shot')->default(1);
            $table->unsignedSmallInteger('boss_hp')->default(6);
            $table->boolean('is_checkpoint')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meteor_game_levels');
    }
};
