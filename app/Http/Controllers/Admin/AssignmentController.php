<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentLearner;
use App\Models\GradeLevel;
use App\Models\Learner;
use App\Models\LearnerAnswer;
use Illuminate\Http\Request;

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

    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('name')->get();

        return view('admin.assignments.create', compact('gradeLevels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_type' => 'required|in:kelas,individu',
            'grade_level' => 'required_if:target_type,kelas|nullable|exists:grade_levels,name',
            'deadline' => 'nullable|date|after:now',
        ]);

        Assignment::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'grade_level' => $data['target_type'] === 'kelas' ? $data['grade_level'] : null,
            'deadline' => $data['deadline'] ?? null,
        ]);

        // Halaman detail tugas belum dibuat — untuk sekarang kembali ke daftar tugas.
        return redirect()->route('admin.assignments.index')->with('success', 'Tugas berhasil dibuat!');
    }

    /**
     * Bentuk edit di panel admin memakai modal langsung di halaman index
     * (lihat admin.assignments.index), bukan halaman /edit terpisah.
     * Method ini dipertahankan supaya Route::resource tetap lengkap.
     */
    public function edit(Assignment $assignment)
    {
        return redirect()->route('admin.assignments.index');
    }

    public function update(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_type' => 'required|in:kelas,individu',
            'grade_level' => 'required_if:target_type,kelas|nullable|exists:grade_levels,name',
            'deadline' => 'nullable|date|after:now',
        ]);

        $assignment->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'grade_level' => $data['target_type'] === 'kelas' ? $data['grade_level'] : null,
            'deadline' => $data['deadline'] ?? null,
        ]);

        return redirect()->route('admin.assignments.index')->with('success', 'Tugas berhasil diperbarui!');
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

        $gradeLevels = GradeLevel::orderBy('name')->get();
        $learners = Learner::orderBy('nama_lengkap')->get();

        return view('admin.assignments.show', compact('assignment', 'gradeLevels', 'learners', 'ungradedByLearner'));
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('admin.assignments.index')->with('success', 'Tugas berhasil dihapus!');
    }

    /**
     * Tugaskan murid ke tugas ini — per kelas (semua murid di kelas tsb)
     * atau per individu (pilih murid tertentu). firstOrCreate mencegah duplikat.
     */
    public function assignLearners(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'assign_type' => 'required|in:kelas,individu',
            'grade_level' => 'required_if:assign_type,kelas|nullable|exists:grade_levels,name',
            'learner_ids' => 'required_if:assign_type,individu|nullable|array',
            'learner_ids.*' => 'exists:learners,id',
        ]);

        if ($data['assign_type'] === 'kelas') {
            $learnerIds = Learner::where('grade_level', $data['grade_level'])->pluck('id');
        } else {
            $learnerIds = collect($data['learner_ids']);
        }

        foreach ($learnerIds as $learnerId) {
            AssignmentLearner::firstOrCreate([
                'assignment_id' => $assignment->id,
                'learner_id' => $learnerId,
            ]);
        }

        return redirect()->route('admin.assignments.show', $assignment->id)
            ->with('success', 'Murid berhasil ditugaskan!');
    }

    /**
     * Batalkan penugasan seorang murid dari tugas ini, sekaligus hapus
     * jawaban yang sudah diisi murid tersebut untuk tugas ini.
     */
    public function unassignLearner(Assignment $assignment, Learner $learner)
    {
        LearnerAnswer::where('learner_id', $learner->id)
            ->whereIn('assignment_question_id', $assignment->questions()->pluck('id'))
            ->delete();

        AssignmentLearner::where('assignment_id', $assignment->id)
            ->where('learner_id', $learner->id)
            ->delete();

        return redirect()->route('admin.assignments.show', $assignment->id)
            ->with('success', 'Penugasan murid berhasil dihapus!');
    }

    /**
     * Tampilkan semua jawaban satu murid untuk satu tugas — hanya kalau
     * murid ini sudah menyelesaikan (submit) tugasnya.
     */
    public function showLearnerAnswers(Assignment $assignment, Learner $learner)
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
    public function gradeLearnerAnswers(Request $request, Assignment $assignment, Learner $learner)
    {
        $assignmentLearner = AssignmentLearner::where('assignment_id', $assignment->id)
            ->where('learner_id', $learner->id)
            ->firstOrFail();

        $assignment->load('questions');
        $essayQuestions = $assignment->questions->where('type', 'essay');

        $rules = [];
        foreach ($essayQuestions as $question) {
            $rules["scores.{$question->id}"] = "required|integer|min:0|max:{$question->points}";
        }

        $data = $request->validate($rules);

        foreach ($essayQuestions as $question) {
            LearnerAnswer::where('learner_id', $learner->id)
                ->where('assignment_question_id', $question->id)
                ->update(['score' => $data['scores'][$question->id]]);
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
