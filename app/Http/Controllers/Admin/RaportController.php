<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\Learner;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RaportController extends Controller
{
    public function index(Request $request)
    {
        $kelas = $request->query('kelas');

        $learnersQuery = Learner::orderBy('nama_lengkap');
        if ($kelas && $kelas !== 'semua') {
            $learnersQuery->where('grade_level', $kelas);
        }
        $learners = $learnersQuery->get();

        $gradeLevels = GradeLevel::orderBy('name')->get();

        $rekap = $learners->map(function (Learner $learner) {
            $assignmentLearners = $learner->assignmentLearners()->with('assignment.questions')->get();
            $selesai = $assignmentLearners->where('status', 'selesai');
            $rataRata = $this->hitungRataRataPersen($selesai);

            return [
                'learner' => $learner,
                'jumlah_selesai' => $selesai->count(),
                'jumlah_total' => $assignmentLearners->count(),
                'rata_rata' => $rataRata,
                'predikat' => $this->hitungPredikat($rataRata),
            ];
        });

        return view('admin.raport.index', compact('rekap', 'gradeLevels', 'kelas'));
    }

    public function show(Learner $learner)
    {
        $assignmentLearners = $learner->assignmentLearners()
            ->with('assignment.questions')
            ->get()
            ->sortByDesc(fn ($al) => $al->assignment->created_at)
            ->values();

        $selesai = $assignmentLearners->where('status', 'selesai');
        $rataRata = $this->hitungRataRataPersen($selesai);

        $totalTugas = $assignmentLearners->count();
        $totalSelesai = $selesai->count();
        $predikat = $this->hitungPredikat($rataRata);

        return view('admin.raport.show', compact(
            'learner', 'assignmentLearners', 'totalTugas', 'totalSelesai', 'rataRata', 'predikat'
        ));
    }

    private function hitungRataRataPersen(Collection $assignmentLearnersSelesai): float
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

    private function hitungPredikat(float $rataRata): string
    {
        return match (true) {
            $rataRata >= 90 => 'Sangat Baik',
            $rataRata >= 75 => 'Baik',
            $rataRata >= 60 => 'Cukup',
            default => 'Perlu Perbaikan',
        };
    }
}
