<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Setiap Guru punya kuis sendiri per mapel yang diampunya — nomor tahap
 * jadi unik per Guru (bukan global lagi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_levels', function (Blueprint $table) {
            $table->dropUnique(['level_number']);
            $table->foreignId('guru_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->after('guru_id')->constrained('subjects')->onDelete('cascade');
            $table->unique(['guru_id', 'level_number']);
        });
    }

    public function down(): void
    {
        Schema::table('quiz_levels', function (Blueprint $table) {
            $table->dropUnique(['guru_id', 'level_number']);
            $table->dropConstrainedForeignId('subject_id');
            $table->dropConstrainedForeignId('guru_id');
            $table->unique('level_number');
        });
    }
};
