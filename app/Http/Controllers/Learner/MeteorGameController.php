<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Learner;

class MeteorGameController extends Controller
{
    /**
     * Pratinjau Fase 1: arena meteor huruf tunggal (home row) + nyawa 5.
     * Belum ada JILID/boss/checkpoint/rekor — itu menyusul di Fase 2 & 3
     * (lihat RENCANA_GAME_10_JARI.md).
     */
    public function index()
    {
        $learner = Learner::find(session('learner_id'));

        return view('learner.meteor.play', compact('learner'));
    }
}
