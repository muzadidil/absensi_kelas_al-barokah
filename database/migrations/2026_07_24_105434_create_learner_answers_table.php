<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('learner_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained('learners')->onDelete('cascade');
            $table->foreignId('assignment_question_id')->constrained('assignment_questions')->onDelete('cascade');
            $table->text('answer_text');
            $table->integer('score')->nullable();
            $table->timestamps();

            $table->unique(['learner_id', 'assignment_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learner_answers');
    }
};
