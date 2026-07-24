<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Models\QuizAttempt;
use App\Models\QuizLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Daftar tahap kuis + status terbuka/terkunci/lulus + total percobaan.
     * Kunci progresif: tahap N terbuka bila tahap sebelumnya lulus.
     * "Lulus" hanya dihitung dari percobaan SETELAH reset pamungkas terakhir.
     */
    public function index()
    {
        $learner = Learner::find(session('learner_id'));

        $levels = QuizLevel::withCount('questions')->orderBy('level_number')->get();

        $passedLevelIds = $this->passedLevelIds($learner);

        $attemptCounts = QuizAttempt::where('learner_id', $learner->id)
            ->selectRaw('quiz_level_id, count(*) as c')
            ->groupBy('quiz_level_id')
            ->pluck('c', 'quiz_level_id');

        $unlocked = [];
        $prevPassed = true;
        foreach ($levels as $level) {
            $unlocked[$level->id] = $prevPassed;
            $prevPassed = $passedLevelIds->contains($level->id);
        }

        return view('learner.quiz.index', compact('learner', 'levels', 'passedLevelIds', 'unlocked', 'attemptCounts'));
    }

    /**
     * Halaman main satu tahap. Tolak kalau terkunci atau belum ada soal.
     */
    public function play(QuizLevel $quizLevel)
    {
        $learner = Learner::find(session('learner_id'));

        $prev = QuizLevel::where('level_number', '<', $quizLevel->level_number)
            ->orderByDesc('level_number')
            ->first();

        if ($prev && ! $this->passedLevelIds($learner)->contains($prev->id)) {
            return redirect()->route('learner.quiz.index')
                ->with('error', "Selesaikan & lulus tahap \"{$prev->name}\" dulu untuk membuka tahap ini.");
        }

        $quizLevel->load(['questions.options']);

        if ($quizLevel->questions->isEmpty()) {
            return redirect()->route('learner.quiz.index')
                ->with('error', 'Tahap ini belum ada soalnya. Hubungi guru.');
        }

        $firstLevel = QuizLevel::orderBy('level_number')->first();
        $nextLevel = QuizLevel::where('level_number', '>', $quizLevel->level_number)
            ->orderBy('level_number')
            ->first();

        return view('learner.quiz.play', compact('learner', 'quizLevel', 'firstLevel', 'nextLevel'));
    }

    /**
     * Catat hasil satu "run" (dipanggil via fetch dari engine gauntlet).
     * Gagal di tahap Mode Pamungkas → set quiz_reset_at (semua kunci menutup).
     */
    public function attempt(Request $request, QuizLevel $quizLevel): JsonResponse
    {
        $learner = Learner::find(session('learner_id'));

        $data = $request->validate([
            'passed' => 'required|boolean',
            'questions_cleared' => 'required|integer|min:0|max:1000',
            'total_questions' => 'required|integer|min:0|max:1000',
        ]);

        QuizAttempt::create([
            'learner_id' => $learner->id,
            'quiz_level_id' => $quizLevel->id,
            'passed' => $data['passed'],
            'questions_cleared' => $data['questions_cleared'],
            'total_questions' => $data['total_questions'],
        ]);

        $resetToFirst = false;
        if (! $data['passed'] && $quizLevel->reset_to_first_on_fail) {
            $learner->update(['quiz_reset_at' => now()]);
            $resetToFirst = true;
        }

        return response()->json(['ok' => true, 'reset_to_first' => $resetToFirst]);
    }

    /**
     * ID tahap yang sudah dilulusi murid — hanya percobaan setelah reset
     * pamungkas terakhir (kalau ada) yang dihitung.
     */
    private function passedLevelIds(Learner $learner)
    {
        return QuizAttempt::where('learner_id', $learner->id)
            ->where('passed', true)
            ->when($learner->quiz_reset_at, fn ($q) => $q->where('created_at', '>', $learner->quiz_reset_at))
            ->pluck('quiz_level_id')
            ->unique();
    }
}
