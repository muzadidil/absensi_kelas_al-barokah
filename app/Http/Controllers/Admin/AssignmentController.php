<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentLearner;
use App\Models\GradeLevel;
use App\Models\LearnerAnswer;
use Illuminate\Http\Request;

/**
 * Admin hanya bisa memantau (read) tugas yang dibuat Guru, dan menilai
 * (evaluasi) jawaban essay murid. Pembuatan/pengeditan tugas & soal,
 * serta penugasan ke murid, sepenuhnya jadi tanggung jawab Guru
 * (lihat App\Http\Controllers\Guru\AssignmentController).
 */
class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $kelas = $request->query('kelas');

        $assignmentsQuery = Assignment::with(['questions', 'assignmentLearners'])
            ->orderByDesc('created_at');

        if ($kelas && $kelas !== 'semua') {
            $assignmentsQuery->where('grade_level', $kelas);
        }

        $assignments = $assignmentsQuery->get();
        $gradeLevels = GradeLevel::orderBy('name')->get();

        return view('admin.assignments.index', compact('assignments', 'gradeLevels', 'kelas'));
    }

    public function show(Assignment $assignment)
    {
        $assignment->load([
            'questions' => fn ($query) => $query->orderBy('sort_order'),
            'assignmentLearners.learner',
        ]);

        // Tandai murid mana yang masih punya soal essay yang belum dinilai,
        // supaya tabel "Murid yang Ditugaskan" bisa kasih peringatan.
        $essayQuestionIds = $assignment->questions->where('type', 'essay')->pluck('id');
        $ungradedByLearner = [];
        if ($essayQuestionIds->isNotEmpty()) {
            $ungradedByLearner = LearnerAnswer::whereIn('assignment_question_id', $essayQuestionIds)
                ->whereNull('score')
                ->pluck('learner_id')
                ->unique()
                ->flip()
                ->toArray();
        }

        return view('admin.assignments.show', compact('assignment', 'ungradedByLearner'));
    }

    /**
     * Tampilkan semua jawaban satu murid untuk satu tugas — hanya kalau
     * murid ini sudah menyelesaikan (submit) tugasnya.
     */
    public function showLearnerAnswers(Assignment $assignment, \App\Models\Learner $learner)
    {
        $assignmentLearner = AssignmentLearner::where('assignment_id', $assignment->id)
            ->where('learner_id', $learner->id)
            ->firstOrFail();

        abort_if($assignmentLearner->status !== 'selesai', 404);

        $assignment->load(['questions' => fn ($query) => $query->orderBy('sort_order')]);

        $answers = LearnerAnswer::where('learner_id', $learner->id)
            ->whereIn('assignment_question_id', $assignment->questions->pluck('id'))
            ->get()
            ->keyBy('assignment_question_id');

        return view('admin.assignments.learner-answers', compact('assignment', 'learner', 'assignmentLearner', 'answers'));
    }

    /**
     * Simpan nilai soal essay yang diisi admin, lalu hitung ulang total_score
     * (gabungan pilgan yang sudah auto-koreksi + essay yang baru dinilai).
     */
    public function gradeLearnerAnswers(Request $request, Assignment $assignment, \App\Models\Learner $learner)
    {
        $assignmentLearner = AssignmentLearner::where('assignment_id', $assignment->id)
            ->where('learner_id', $learner->id)
            ->firstOrFail();

        $assignment->load('questions');
        $essayQuestions = $assignment->questions->where('type', 'essay');

        $rules = [];
        foreach ($essayQuestions as $question) {
            $rules["scores.{$question->id}"] = "required|integer|min:0|max:{$question->points}";
            $rules["feedback.{$question->id}"] = 'nullable|string';
        }

        $data = $request->validate($rules);

        foreach ($essayQuestions as $question) {
            LearnerAnswer::where('learner_id', $learner->id)
                ->where('assignment_question_id', $question->id)
                ->update([
                    'score' => $data['scores'][$question->id],
                    'feedback' => $data['feedback'][$question->id] ?? null,
                ]);
        }

        $totalScore = LearnerAnswer::where('learner_id', $learner->id)
            ->whereIn('assignment_question_id', $assignment->questions->pluck('id'))
            ->whereNotNull('score')
            ->sum('score');

        $assignmentLearner->update(['total_score' => $totalScore]);

        return redirect()->route('admin.assignments.learner-answers', [$assignment->id, $learner->id])
            ->with('success', 'Nilai berhasil disimpan!');
    }
}
