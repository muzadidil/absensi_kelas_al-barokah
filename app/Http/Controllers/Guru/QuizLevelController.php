<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\QuizLevel;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizLevelController extends Controller
{
    public function index(Request $request)
    {
        $subjects = $request->user()->subjects()->orderBy('name')->get();

        if ($subjects->isEmpty()) {
            return view('guru.quiz-levels.index', [
                'subjects' => $subjects,
                'activeSubject' => null,
                'levels' => collect(),
            ]);
        }

        $activeSubject = $subjects->firstWhere('id', (int) $request->query('subject')) ?? $subjects->first();

        $levels = QuizLevel::where('guru_id', $request->user()->id)
            ->where('subject_id', $activeSubject->id)
            ->withCount(['questions', 'attempts'])
            ->orderBy('level_number')
            ->get();

        return view('guru.quiz-levels.index', compact('subjects', 'activeSubject', 'levels'));
    }

    /**
     * Kelola soal satu tahap.
     */
    public function show(Request $request, QuizLevel $quizLevel)
    {
        $this->authorizeOwner($request, $quizLevel);

        $quizLevel->load(['questions.options']);

        return view('guru.quiz-levels.show', compact('quizLevel'));
    }

    public function store(Request $request)
    {
        $subject = $this->resolveOwnedSubject($request);

        $data = $this->validateLevel($request, $subject->id);
        $data['guru_id'] = $request->user()->id;
        $data['subject_id'] = $subject->id;

        QuizLevel::create($data);

        return redirect()->back()->with('success', 'Tahap kuis berhasil ditambahkan! Sekarang tambahkan soal-soalnya.');
    }

    public function update(Request $request, QuizLevel $quizLevel)
    {
        $this->authorizeOwner($request, $quizLevel);

        $data = $this->validateLevel($request, $quizLevel->subject_id, $quizLevel->id);

        $quizLevel->update($data);

        return redirect()->back()->with('success', 'Tahap kuis berhasil diperbarui!');
    }

    public function destroy(Request $request, QuizLevel $quizLevel)
    {
        $this->authorizeOwner($request, $quizLevel);

        $quizLevel->delete();

        return redirect()->back()->with('success', 'Tahap kuis berhasil dihapus!');
    }

    /**
     * Salin sebuah tahap beserta seluruh soal & opsinya sebagai tahap baru di
     * urutan akhir. Nomor otomatis unik dan nama diberi penanda "(salinan)".
     */
    public function duplicate(Request $request, QuizLevel $quizLevel)
    {
        $this->authorizeOwner($request, $quizLevel);

        $copy = $quizLevel->replicate();
        $copy->level_number = (int) QuizLevel::where('guru_id', $quizLevel->guru_id)->max('level_number') + 1;
        $copy->name = mb_substr($quizLevel->name . ' (salinan)', 0, 255);
        $copy->save();

        foreach ($quizLevel->questions()->with('options')->get() as $question) {
            $newQuestion = QuizQuestion::create([
                'quiz_level_id' => $copy->id,
                'question_text' => $question->question_text,
                'explanation' => $question->explanation,
                'sort_order' => $question->sort_order,
            ]);

            foreach ($question->options as $option) {
                QuizOption::create([
                    'quiz_question_id' => $newQuestion->id,
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                    'sort_order' => $option->sort_order,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Tahap berhasil disalin beserta semua soalnya! Silakan ubah nomor & nama tahap salinan.');
    }

    private function authorizeOwner(Request $request, QuizLevel $quizLevel): void
    {
        abort_unless($quizLevel->guru_id === $request->user()->id, 403);
    }

    /**
     * Mapel yang dipilih Guru saat menambah tahap — harus salah satu mapel
     * yang memang diampunya.
     */
    private function resolveOwnedSubject(Request $request): Subject
    {
        $subjectId = (int) $request->input('subject_id');

        $subject = $request->user()->subjects()->find($subjectId);

        abort_unless($subject, 403, 'Anda belum ditugaskan mengampu mapel ini.');

        return $subject;
    }

    private function validateLevel(Request $request, int $subjectId, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'level_number' => [
                'required', 'integer', 'min:1', 'max:999',
                Rule::unique('quiz_levels', 'level_number')
                    ->where('guru_id', $request->user()->id)
                    ->ignore($ignoreId),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['reset_to_first_on_fail'] = $request->boolean('reset_to_first_on_fail');

        return $data;
    }
}
