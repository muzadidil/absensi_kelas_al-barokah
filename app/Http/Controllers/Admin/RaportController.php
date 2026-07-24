<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\Learner;
use App\Support\CalculatesQuizProgress;
use Illuminate\Http\Request;

class RaportController extends Controller
{
    use CalculatesQuizProgress;

    public function index(Request $request)
    {
        $kelas = $request->query('kelas');

        $learnersQuery = Learner::orderBy('nama_lengkap');
        if ($kelas && $kelas !== 'semua') {
            $learnersQuery->where('grade_level', $kelas);
        }
        $learners = $learnersQuery->get();

        $gradeLevels = GradeLevel::orderBy('name')->get();

        $rekap = $learners->map(fn (Learner $learner) => [
            'learner' => $learner,
            'kuis' => $this->hitungProgresKuis($learner),
        ]);

        return view('admin.raport.index', compact('rekap', 'gradeLevels', 'kelas'));
    }

    public function show(Learner $learner)
    {
        $kuis = $this->hitungProgresKuis($learner);

        return view('admin.raport.show', compact('learner', 'kuis'));
    }
}
