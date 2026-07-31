<?php

use App\Models\QuizLevel;
use App\Models\Subject;
use App\Models\TypingLevel;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tahap kuis & mengetik yang sudah ada (dibuat sebelum fitur per-guru)
 * di-assign ke Guru TIK, karena selama ini memang cuma dia yang pakai.
 * Guru lain mulai dari kosong sesuai keputusan kepala sekolah.
 */
return new class extends Migration
{
    public function up(): void
    {
        $guru = User::where('email', 'muzadidilfuad@gmail.com')->first();

        if (! $guru) {
            return;
        }

        $subject = Subject::firstOrCreate(['name' => 'TIK']);

        DB::table('subject_teacher')->updateOrInsert(
            ['subject_id' => $subject->id, 'guru_id' => $guru->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        QuizLevel::query()->whereNull('guru_id')->update([
            'guru_id' => $guru->id,
            'subject_id' => $subject->id,
        ]);

        TypingLevel::query()->whereNull('guru_id')->update([
            'guru_id' => $guru->id,
            'subject_id' => $subject->id,
        ]);
    }

    public function down(): void
    {
        // Data migration — tidak perlu dibalik.
    }
};
