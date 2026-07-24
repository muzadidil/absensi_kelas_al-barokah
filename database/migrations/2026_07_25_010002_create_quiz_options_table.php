<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Opsi jawaban tiap soal. Disimpan sebagai daftar (bukan A/B/C/D tetap) supaya
 * posisinya bisa DIACAK setiap kali murid mengulang — mereka harus paham isi,
 * bukan hafal huruf. Tepat satu opsi is_correct = true per soal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_options');
    }
};
