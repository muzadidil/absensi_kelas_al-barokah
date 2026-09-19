<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Models\MeteorGameAttempt;
use App\Models\MeteorGameLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Game 10 Jari — JILID berjenjang, tiap JILID ditutup lawan boss.
 * Lulus satu JILID = boss-nya kalah sebelum nyawa habis (RENCANA_GAME_10_JARI.md §3.4).
 */
class MeteorGameController extends Controller
{
    /**
     * Daftar JILID + status terbuka/terkunci/lulus.
     */
    public function index()
    {
        $learner = Learner::find(session('learner_id'));

        $levels = MeteorGameLevel::with(['theme', 'boss', 'bullet'])->orderBy('level_number')->get();
        $passed = $this->passedLevelNumbers($learner, $levels);

        $unlocked = [];
        $prevPassed = true;
        foreach ($levels as $level) {
            $unlocked[$level->level_number] = $prevPassed;
            $prevPassed = $passed->contains($level->level_number);
        }

        $attemptCounts = MeteorGameAttempt::where('learner_id', $learner->id)
            ->selectRaw('meteor_game_level_id, count(*) as c')
            ->groupBy('meteor_game_level_id')
            ->pluck('c', 'meteor_game_level_id');

        $bestWpm = MeteorGameAttempt::where('learner_id', $learner->id)->max('wpm') ?? 0;

        return view('learner.meteor.index', compact(
            'learner', 'levels', 'passed', 'unlocked', 'attemptCounts', 'bestWpm'
        ));
    }

    /**
     * Halaman main satu JILID. Tolak kalau JILID sebelumnya belum lulus.
     */
    public function play(MeteorGameLevel $meteorGameLevel)
    {
        $learner = Learner::find(session('learner_id'));

        $levels = MeteorGameLevel::with('boss')->orderBy('level_number')->get();
        $passed = $this->passedLevelNumbers($learner, $levels);

        $prev = $levels->where('level_number', '<', $meteorGameLevel->level_number)
            ->sortByDesc('level_number')
            ->first();

        if ($prev && ! $passed->contains($prev->level_number)) {
            $bossName = $prev->boss?->name ?? 'boss';
            return redirect()->route('learner.meteor.index')
                ->with('error', "Kalahkan dulu {$bossName} di {$prev->display_label} untuk membuka {$meteorGameLevel->display_label}.");
        }

        $meteorGameLevel->load(['theme', 'boss', 'bullet.effect', 'effect']);

        $nextLevel = $levels->where('level_number', '>', $meteorGameLevel->level_number)
            ->sortBy('level_number')
            ->first();

        return view('learner.meteor.play', compact('learner', 'meteorGameLevel', 'nextLevel'));
    }

    /**
     * Catat hasil satu percobaan JILID (dipanggil via fetch dari game).
     * Gagal di JILID DI ATAS checkpoint → progres di atas checkpoint hangus (§3.6).
     */
    public function attempt(Request $request, MeteorGameLevel $meteorGameLevel): JsonResponse
    {
        $learner = Learner::find(session('learner_id'));

        $data = $request->validate([
            'passed' => 'required|boolean',
            'reached_boss' => 'required|boolean',
            'meteors_destroyed' => 'required|integer|min:0|max:9999',
            'wpm' => 'required|integer|min:0|max:500',
            'accuracy' => 'required|integer|min:0|max:100',
        ]);

        MeteorGameAttempt::create([
            'learner_id' => $learner->id,
            'meteor_game_level_id' => $meteorGameLevel->id,
            'passed' => $data['passed'],
            'reached_boss' => $data['reached_boss'],
            'meteors_destroyed' => $data['meteors_destroyed'],
            'wpm' => $data['wpm'],
            'accuracy' => $data['accuracy'],
        ]);

        $checkpoint = (int) $learner->meteor_checkpoint_level;
        $resetToStart = false;

        if ($data['passed']) {
            if ($meteorGameLevel->is_checkpoint && $meteorGameLevel->level_number > $checkpoint) {
                $learner->update(['meteor_checkpoint_level' => $meteorGameLevel->level_number]);
                $checkpoint = $meteorGameLevel->level_number;
            }
        } elseif ($meteorGameLevel->level_number > $checkpoint) {
            $learner->update(['meteor_reset_at' => now()]);
            $resetToStart = true;
        }

        $restartFrom = MeteorGameLevel::where('level_number', '>', $checkpoint)
            ->orderBy('level_number')
            ->first();

        return response()->json([
            'ok' => true,
            'reset_to_start' => $resetToStart,
            'restart_label' => $restartFrom?->display_label ?? 'JILID 1',
        ]);
    }

    /**
     * Nomor JILID yang dihitung sudah lulus: JILID sampai batas checkpoint selalu
     * dianggap lulus (itu gunanya checkpoint — tidak ikut hangus), sisanya hanya
     * dari percobaan SETELAH reset terakhir.
     */
    private function passedLevelNumbers(Learner $learner, Collection $levels): Collection
    {
        $checkpoint = (int) $learner->meteor_checkpoint_level;

        $fromAttempts = MeteorGameAttempt::where('learner_id', $learner->id)
            ->where('passed', true)
            ->when($learner->meteor_reset_at, fn ($q) => $q->where('created_at', '>', $learner->meteor_reset_at))
            ->pluck('meteor_game_level_id');

        return $levels->filter(
            fn ($level) => $level->level_number <= $checkpoint || $fromAttempts->contains($level->id)
        )->pluck('level_number')->unique()->values();
    }
}
