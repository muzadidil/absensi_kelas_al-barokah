<?php

namespace App\Http\Controllers\Guru;

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

        return view('guru.assignments.index', compact('assignments', 'gradeLevels', 'kelas'));
    }

    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('name')->get();

        return view('guru.assignments.create', compact('gradeLevels'));
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

        return redirect()->route('guru.assignments.index')->with('success', 'Tugas berhasil dibuat!');
    }

    /**
     * Bentuk edit dilakukan lewat modal langsung di halaman index
     * (lihat guru.assignments.index), bukan halaman /edit terpisah.
     * Method ini dipertahankan supaya Route::resource tetap lengkap.
     */
    public function edit(Assignment $assignment)
    {
        return redirect()->route('guru.assignments.index');
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

        return redirect()->route('guru.assignments.index')->with('success', 'Tugas berhasil diperbarui!');
    }

    public function show(Assignment $assignment)
    {
        $assignment->load([
            'questions' => fn ($query) => $query->orderBy('sort_order'),
            'assignmentLearners.learner',
        ]);

        $gradeLevels = GradeLevel::orderBy('name')->get();
        $learners = Learner::orderBy('nama_lengkap')->get();

        return view('guru.assignments.show', compact('assignment', 'gradeLevels', 'learners'));
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('guru.assignments.index')->with('success', 'Tugas berhasil dihapus!');
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

        return redirect()->route('guru.assignments.show', $assignment->id)
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

        return redirect()->route('guru.assignments.show', $assignment->id)
            ->with('success', 'Penugasan murid berhasil dihapus!');
    }
}
