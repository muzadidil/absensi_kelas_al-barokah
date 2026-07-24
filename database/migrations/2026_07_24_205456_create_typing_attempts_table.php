<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('typing_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_id')->constrained('learners')->onDelete('cascade');
            $table->foreignId('typing_level_id')->constrained('typing_levels')->onDelete('cascade');
            $table->unsignedInteger('wpm');
            $table->unsignedTinyInteger('accuracy'); // persen 0-100
            $table->unsignedInteger('duration_seconds');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('typing_attempts');
    }
};
