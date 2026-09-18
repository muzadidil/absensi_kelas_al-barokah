<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Learner;

class MeteorGameController extends Controller
{
    /**
     * Mode bebas: arena meteor huruf tunggal (home row), 5 nyawa, tanpa batas waktu.
     * JILID/boss/checkpoint/rekor belum ada — menyusul di Fase 2 & 3
     * (lihat RENCANA_GAME_10_JARI.md).
     */
    public function index()
    {
        $learner = Learner::find(session('learner_id'));

        return view('learner.meteor.play', compact('learner'));
    }
}
