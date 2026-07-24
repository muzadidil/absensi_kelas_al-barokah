<?php

namespace App\Support;

use App\Models\Learner;
use App\Models\QuizAttempt;
use App\Models\QuizLevel;

/**
 * Ringkasan progres Kuis Pilihan Ganda untuk raport — fokus KEGIGIHAN:
 * berapa tahap tembus, berapa total percobaan (makin banyak = makin gigih),
 * plus predikat dari progres. Dipakai bersama Admin\RaportController &
 * Learner\AssignmentController agar angkanya konsisten.
 */
trait CalculatesQuizProgress
{
    protected function hitungProgresKuis(Learner $learner): array
    {
        $totalTahap = QuizLevel::count();

        // Lulus hanya dihitung dari percobaan setelah reset pamungkas terakhir.
        $tahapTembus = QuizAttempt::where('learner_id', $learner->id)
            ->where('passed', true)
            ->when($learner->quiz_reset_at, fn ($q) => $q->where('created_at', '>', $learner->quiz_reset_at))
            ->pluck('quiz_level_id')
            ->unique()
            ->count();

        $totalPercobaan = QuizAttempt::where('learner_id', $learner->id)->count();

        $progresPersen = $totalTahap > 0 ? (int) round($tahapTembus / $totalTahap * 100) : 0;

        return [
            'total_tahap' => $totalTahap,
            'tahap_tembus' => $tahapTembus,
            'total_percobaan' => $totalPercobaan,
            'progres_persen' => $progresPersen,
            'predikat' => $this->hitungPredikatKuis($progresPersen, $totalTahap),
        ];
    }

    protected function hitungPredikatKuis(int $progresPersen, int $totalTahap): string
    {
        if ($totalTahap === 0) {
            return 'Belum Ada Kuis';
        }

        return match (true) {
            $progresPersen >= 100 => 'Tuntas',
            $progresPersen >= 60 => 'Baik',
            $progresPersen >= 30 => 'Berkembang',
            $progresPersen > 0 => 'Mulai Jalan',
            default => 'Belum Mulai',
        };
    }
}
