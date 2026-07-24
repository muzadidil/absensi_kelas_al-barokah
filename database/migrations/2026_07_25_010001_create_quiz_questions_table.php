<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Soal per tahap kuis. `explanation` ditampilkan ke murid saat menjawab salah
 * (bareng jawaban yang benar) sebelum diulang dari soal 1.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_level_id')->constrained('quiz_levels')->cascadeOnDelete();
            $table->text('question_text');
            $table->text('explanation')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
