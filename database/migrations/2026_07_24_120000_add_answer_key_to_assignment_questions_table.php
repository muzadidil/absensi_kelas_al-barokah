<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_questions', function (Blueprint $table) {
            // Kunci jawaban acuan untuk soal essay — jadi panduan guru saat menilai manual.
            $table->text('answer_key')->nullable()->after('correct_answer');
        });
    }

    public function down(): void
    {
        Schema::table('assignment_questions', function (Blueprint $table) {
            $table->dropColumn('answer_key');
        });
    }
};
