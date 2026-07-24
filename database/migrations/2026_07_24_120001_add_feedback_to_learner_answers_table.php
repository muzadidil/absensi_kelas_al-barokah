<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learner_answers', function (Blueprint $table) {
            // Komentar/feedback guru untuk jawaban essay murid.
            $table->text('feedback')->nullable()->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('learner_answers', function (Blueprint $table) {
            $table->dropColumn('feedback');
        });
    }
};
