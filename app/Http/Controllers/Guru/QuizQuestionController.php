<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\QuizLevel;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QuizQuestionController extends Controller
{
    public function store(Request $request, QuizLevel $quizLevel)
    {
        $data = $this->validateQuestion($request);

        $question = QuizQuestion::create([
            'quiz_level_id' => $quizLevel->id,
            'question_text' => $data['question_text'],
            'explanation' => $data['explanation'],
            'sort_order' => (int) $quizLevel->questions()->max('sort_order') + 1,
        ]);

        $this->syncOptions($question, $data['optionList'], $data['correctPos']);

        return redirect()->route('guru.quiz-levels.show', $quizLevel->id)
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function update(Request $request, QuizLevel $quizLevel, QuizQuestion $question)
    {
        $data = $this->validateQuestion($request);

        $question->update([
            'question_text' => $data['question_text'],
            'explanation' => $data['explanation'],
        ]);

        $this->syncOptions($question, $data['optionList'], $data['correctPos']);

        return redirect()->route('guru.quiz-levels.show', $quizLevel->id)
            ->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy(QuizLevel $quizLevel, QuizQuestion $question)
    {
        $question->delete();

        return redirect()->route('guru.quiz-levels.show', $quizLevel->id)
            ->with('success', 'Soal berhasil dihapus!');
    }

    /**
     * Validasi + rapikan opsi: buang opsi kosong, pastikan minimal 2 opsi dan
     * tepat 1 jawaban benar yang menunjuk ke opsi yang terisi.
     */
    private function validateQuestion(Request $request): array
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'options' => 'required|array',
            'options.*' => 'nullable|string|max:1000',
            'correct' => 'required|integer|min:0',
        ], [
            'correct.required' => 'Pilih jawaban yang benar.',
        ]);

        $optionList = [];
        $correctPos = null;

        foreach ($validated['options'] as $i => $text) {
            $text = trim((string) $text);
            if ($text === '') {
                continue;
            }
            if ((int) $validated['correct'] === (int) $i) {
                $correctPos = count($optionList);
            }
            $optionList[] = $text;
        }

        if (count($optionList) < 2) {
            throw ValidationException::withMessages(['options' => 'Isi minimal 2 opsi jawaban.']);
        }
        if ($correctPos === null) {
            throw ValidationException::withMessages(['correct' => 'Jawaban benar harus menunjuk salah satu opsi yang terisi.']);
        }

        return [
            'question_text' => $validated['question_text'],
            'explanation' => $validated['explanation'] ?? null,
            'optionList' => $optionList,
            'correctPos' => $correctPos,
        ];
    }

    private function syncOptions(QuizQuestion $question, array $optionList, int $correctPos): void
    {
        $question->options()->delete();

        foreach ($optionList as $pos => $text) {
            QuizOption::create([
                'quiz_question_id' => $question->id,
                'option_text' => $text,
                'is_correct' => $pos === $correctPos,
                'sort_order' => $pos + 1,
            ]);
        }
    }
}
