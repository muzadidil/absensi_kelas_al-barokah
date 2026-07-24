<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Support\CalculatesQuizProgress;

class RaportController extends Controller
{
    use CalculatesQuizProgress;

    /**
     * Raport murid — fokus progres & kegigihan Kuis Pilihan Ganda.
     */
    public function index()
    {
        $learner = Learner::find(session('learner_id'));

        $kuis = $this->hitungProgresKuis($learner);

        return view('learner.raport', compact('learner', 'kuis'));
    }
}
