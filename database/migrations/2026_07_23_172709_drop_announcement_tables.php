<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('announcement_logs');
        Schema::dropIfExists('announcement_targets');
        Schema::dropIfExists('announcements');
    }

    public function down(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('announcement_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('announcement_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
};
