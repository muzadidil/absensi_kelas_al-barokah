<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\QuizLevel;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizLevelController extends Controller
{
    public function index()
    {
        $levels = QuizLevel::withCount(['questions', 'attempts'])
            ->orderBy('level_number')
            ->get();

        return view('guru.quiz-levels.index', compact('levels'));
    }

    /**
     * Kelola soal satu tahap.
     */
    public function show(QuizLevel $quizLevel)
    {
        $quizLevel->load(['questions.options']);

        return view('guru.quiz-levels.show', compact('quizLevel'));
    }

    public function store(Request $request)
    {
        $data = $this->validateLevel($request);

        QuizLevel::create($data);

        return redirect()->back()->with('success', 'Tahap kuis berhasil ditambahkan! Sekarang tambahkan soal-soalnya.');
    }

    public function update(Request $request, QuizLevel $quizLevel)
    {
        $data = $this->validateLevel($request, $quizLevel->id);

        $quizLevel->update($data);

        return redirect()->back()->with('success', 'Tahap kuis berhasil diperbarui!');
    }

    public function destroy(QuizLevel $quizLevel)
    {
        $quizLevel->delete();

        return redirect()->back()->with('success', 'Tahap kuis berhasil dihapus!');
    }

    /**
     * Salin sebuah tahap beserta seluruh soal & opsinya sebagai tahap baru di
     * urutan akhir. Nomor otomatis unik dan nama diberi penanda "(salinan)".
     */
    public function duplicate(QuizLevel $quizLevel)
    {
        $copy = $quizLevel->replicate();
        $copy->level_number = (int) QuizLevel::max('level_number') + 1;
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

    private function validateLevel(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'level_number' => [
                'required', 'integer', 'min:1', 'max:999',
                Rule::unique('quiz_levels', 'level_number')->ignore($ignoreId),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['reset_to_first_on_fail'] = $request->boolean('reset_to_first_on_fail');

        return $data;
    }
}
