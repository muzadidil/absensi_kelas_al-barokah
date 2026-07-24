<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('learners', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable()->after('id');
        });

        // Gabungkan fname + mname + lname yang sudah ada menjadi satu nama_lengkap,
        // lewati bagian yang kosong/null dan rapikan spasi ganda.
        DB::table('learners')->orderBy('id')->chunkById(100, function ($learners) {
            foreach ($learners as $learner) {
                $namaLengkap = collect([$learner->fname, $learner->mname, $learner->lname])
                    ->map(fn ($part) => trim((string) $part))
                    ->filter(fn ($part) => $part !== '')
                    ->implode(' ');

                DB::table('learners')
                    ->where('id', $learner->id)
                    ->update(['nama_lengkap' => $namaLengkap]);
            }
        });

        Schema::table('learners', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable(false)->change();
            $table->dropColumn(['fname', 'mname', 'lname']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learners', function (Blueprint $table) {
            // Data tidak bisa dipecah balik ke fname/mname/lname — kolom
            // dikembalikan kosong (nullable), itu wajar untuk rollback.
            $table->string('fname')->nullable()->after('id');
            $table->string('mname')->nullable()->after('fname');
            $table->string('lname')->nullable()->after('mname');
            $table->dropColumn('nama_lengkap');
        });
    }
};
