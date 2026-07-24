<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('typing_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('level_number')->unique();
            $table->string('name');
            $table->string('allowed_keys'); // mis. "asdfghjkl;"
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('typing_levels');
    }
};
