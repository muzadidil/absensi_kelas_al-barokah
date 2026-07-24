<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pensiunkan sistem Tugas lama (Esai/Praktek/Pilgan) — digantikan oleh
 * Kuis Pilihan Ganda berjenjang (quiz_*). Drop tabel dari anak ke induk
 * agar tidak melanggar foreign key.
 *
 * ⚠️ Menghapus data. Backup DB produksi sebelum migrate bila masih ada
 * data Tugas yang ingin diselamatkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('learner_answers');
        Schema::dropIfExists('assignment_learners');
        Schema::dropIfExists('assignment_questions');
        Schema::dropIfExists('assignments');
    }

    public function down(): void
    {
        // Membangun ulang tabel kosong (tanpa data). Struktur mengikuti migrasi
        // asli create_* + kolom tambahan answer_key/feedback.
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('grade_level');
            $table->dateTime('deadline')->nullable();
            $table->timestamps();
        });

        Schema::create('assignment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->string('type');
            $table->text('question_text');
            $table->json('options')->nullable();
            $table->string('correct_answer')->nullable();
            $table->text('answer_key')->nullable();
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('assignment_learners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('learner_id')->constrained('learners')->cascadeOnDelete();
            $table->string('status')->default('belum');
            $table->timestamp('submitted_at')->nullable();
            $table->integer('total_score')->nullable();
            $table->timestamps();
        });

        Schema::create('learner_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained('learners')->cascadeOnDelete();
            $table->foreignId('assignment_question_id')->constrained('assignment_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }
};
