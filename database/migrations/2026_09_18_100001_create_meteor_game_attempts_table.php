<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Riwayat percobaan Game 10 Jari (kembaran quiz_attempts).
 * - passed : true hanya kalau boss JILID itu berhasil dikalahkan.
 * - wpm : tolok ukur saja, BUKAN syarat lulus (RENCANA_GAME_10_JARI.md §3.7).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meteor_game_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained('learners')->cascadeOnDelete();
            $table->foreignId('meteor_game_level_id')->constrained('meteor_game_levels')->cascadeOnDelete();
            $table->boolean('passed')->default(false);
            $table->boolean('reached_boss')->default(false);
            $table->unsignedSmallInteger('meteors_destroyed')->default(0);
            $table->unsignedSmallInteger('wpm')->default(0);
            $table->unsignedTinyInteger('accuracy')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meteor_game_attempts');
    }
};
