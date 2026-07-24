<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\GradeLevel;
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

    /**
     * Halaman detail tugas belum dibuat — sementara arahkan balik ke daftar.
     */
    public function show(Assignment $assignment)
    {
        return redirect()->route('admin.assignments.index')
            ->with('info', 'Halaman detail tugas belum tersedia.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('admin.assignments.index')->with('success', 'Tugas berhasil dihapus!');
    }
}
