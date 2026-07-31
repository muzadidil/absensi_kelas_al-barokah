<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Models\QuizAttempt;
use App\Models\QuizLevel;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Daftar mapel yang punya kuis — setiap mapel adalah gauntlet sendiri
     * milik guru pengampunya.
     */
    public function index()
    {
        $subjects = Subject::whereHas('quizLevels')
            ->withCount('quizLevels as levels_count')
            ->orderBy('name')
            ->get();

        return view('learner.quiz.subjects', compact('subjects'));
    }

    /**
     * Daftar tahap kuis milik satu mapel + status terbuka/terkunci/lulus.
     * Kunci progresif: tahap N terbuka bila tahap sebelumnya (mapel yang sama) lulus.
     * "Lulus" hanya dihitung dari percobaan SETELAH reset pamungkas terakhir.
     */
    public function bySubject(Subject $subject)
    {
        $learner = Learner::find(session('learner_id'));

        $levels = QuizLevel::where('subject_id', $subject->id)
            ->withCount('questions')
            ->orderBy('level_number')
            ->get();

        $passedLevelIds = $this->passedLevelIds($learner, $levels->pluck('id'));

        $attemptCounts = QuizAttempt::where('learner_id', $learner->id)
            ->whereIn('quiz_level_id', $levels->pluck('id'))
            ->selectRaw('quiz_level_id, count(*) as c')
            ->groupBy('quiz_level_id')
            ->pluck('c', 'quiz_level_id');

        $unlocked = [];
        $prevPassed = true;
        foreach ($levels as $level) {
            $unlocked[$level->id] = $prevPassed;
            $prevPassed = $passedLevelIds->contains($level->id);
        }

        return view('learner.quiz.index', compact('learner', 'subject', 'levels', 'passedLevelIds', 'unlocked', 'attemptCounts'));
    }

    /**
     * Halaman main satu tahap. Tolak kalau terkunci atau belum ada soal.
     */
    public function play(QuizLevel $quizLevel)
    {
        $learner = Learner::find(session('learner_id'));

        $prev = QuizLevel::where('subject_id', $quizLevel->subject_id)
            ->where('level_number', '<', $quizLevel->level_number)
            ->orderByDesc('level_number')
            ->first();

        $siblingIds = QuizLevel::where('subject_id', $quizLevel->subject_id)->pluck('id');

        if ($prev && ! $this->passedLevelIds($learner, $siblingIds)->contains($prev->id)) {
            return redirect()->route('learner.quiz.subject', $quizLevel->subject_id)
                ->with('error', "Selesaikan & lulus tahap \"{$prev->name}\" dulu untuk membuka tahap ini.");
        }

        $quizLevel->load(['questions.options']);

        if ($quizLevel->questions->isEmpty()) {
            return redirect()->route('learner.quiz.subject', $quizLevel->subject_id)
                ->with('error', 'Tahap ini belum ada soalnya. Hubungi guru.');
        }

        $firstLevel = QuizLevel::where('subject_id', $quizLevel->subject_id)->orderBy('level_number')->first();
        $nextLevel = QuizLevel::where('subject_id', $quizLevel->subject_id)
            ->where('level_number', '>', $quizLevel->level_number)
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
     * ID tahap (dibatasi ke satu mapel) yang sudah dilulusi murid — hanya
     * percobaan setelah reset pamungkas terakhir (kalau ada) yang dihitung.
     */
    private function passedLevelIds(Learner $learner, $levelIds)
    {
        return QuizAttempt::where('learner_id', $learner->id)
            ->where('passed', true)
            ->whereIn('quiz_level_id', $levelIds)
            ->when($learner->quiz_reset_at, fn ($q) => $q->where('created_at', '>', $learner->quiz_reset_at))
            ->pluck('quiz_level_id')
            ->unique();
    }
}
