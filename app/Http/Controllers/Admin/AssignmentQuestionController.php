<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use Illuminate\Http\Request;

class AssignmentQuestionController extends Controller
{
    public function store(Request $request, Assignment $assignment)
    {
        $data = $this->validateQuestion($request);

        AssignmentQuestion::create([
            'assignment_id' => $assignment->id,
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'options' => $data['type'] === 'pilgan' ? array_values($data['options']) : null,
            'correct_answer' => $data['type'] === 'pilgan' ? $data['correct_answer'] : null,
            'answer_key' => $data['type'] === 'essay' ? ($data['answer_key'] ?? null) : null,
            'points' => $data['points'],
            'sort_order' => $assignment->questions()->count() + 1,
        ]);

        return redirect()->route('admin.assignments.show', $assignment->id)
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function update(Request $request, Assignment $assignment, AssignmentQuestion $question)
    {
        $data = $this->validateQuestion($request);

        $question->update([
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'options' => $data['type'] === 'pilgan' ? array_values($data['options']) : null,
            'correct_answer' => $data['type'] === 'pilgan' ? $data['correct_answer'] : null,
            'answer_key' => $data['type'] === 'essay' ? ($data['answer_key'] ?? null) : null,
            'points' => $data['points'],
        ]);

        return redirect()->route('admin.assignments.show', $assignment->id)
            ->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy(Assignment $assignment, AssignmentQuestion $question)
    {
        $question->delete();

        return redirect()->route('admin.assignments.show', $assignment->id)
            ->with('success', 'Soal berhasil dihapus!');
    }

    private function validateQuestion(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:pilgan,essay',
            'question_text' => 'required|string',
            'options' => 'required_if:type,pilgan|array|min:2|max:5',
            'options.*' => 'string',
            'correct_answer' => 'required_if:type,pilgan|string',
            'answer_key' => 'nullable|string',
            'points' => 'required|integer|min:1',
        ]);
    }
}
