<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Riwayat percobaan kuis (kembaran typing_attempts). Satu "run" = satu baris.
 * - passed : true kalau run bersih (semua soal tahap dijawab benar tanpa salah).
 * - questions_cleared / total_questions : sejauh mana dia sampai sebelum gagal
 *   (buat baca kegigihan di raport).
 *
 * Total percobaan = COUNT baris; progres = tahap tertinggi yang punya passed=true.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained('learners')->cascadeOnDelete();
            $table->foreignId('quiz_level_id')->constrained('quiz_levels')->cascadeOnDelete();
            $table->boolean('passed')->default(false);
            $table->unsignedInteger('questions_cleared')->default(0);
            $table->unsignedInteger('total_questions')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
