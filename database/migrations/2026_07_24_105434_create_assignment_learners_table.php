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
        Schema::create('assignment_learners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('learner_id')->constrained('learners')->onDelete('cascade');
            $table->string('status')->default('belum'); // 'belum' atau 'selesai'
            $table->dateTime('submitted_at')->nullable();
            $table->integer('total_score')->nullable();
            $table->timestamps();

            $table->unique(['assignment_id', 'learner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_learners');
    }
};
