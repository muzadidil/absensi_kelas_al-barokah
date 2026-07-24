<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Logika hitung rata-rata & predikat rapor, dipakai bersama oleh
 * Admin\RaportController, Learner\AssignmentController, dan LearnerController
 * (dashboard murid) supaya angkanya selalu konsisten di satu tempat.
 */
trait CalculatesRaport
{
    protected function hitungRataRataPersen(Collection $assignmentLearnersSelesai): float
    {
        if ($assignmentLearnersSelesai->isEmpty()) {
            return 0;
        }

        $persentasePerTugas = $assignmentLearnersSelesai->map(function ($al) {
            $maxScore = $al->assignment->questions->sum('points');

            return $maxScore > 0 ? ($al->total_score / $maxScore) * 100 : 0;
        });

        return round($persentasePerTugas->avg(), 1);
    }

    protected function hitungPredikat(float $rataRata): string
    {
        return match (true) {
            $rataRata >= 90 => 'Sangat Baik',
            $rataRata >= 75 => 'Baik',
            $rataRata >= 60 => 'Cukup',
            default => 'Perlu Perbaikan',
        };
    }
}
