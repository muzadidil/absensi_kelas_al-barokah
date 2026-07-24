<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Learner;
use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Support\Facades\DB;


class LearnerController extends Controller
{
    public function index()
    {
         if (auth()->user()->hasRole('learner')) {
        return view('learner.dashboard');
    }

    // For admin viewing learner records
        // $learners = Learner::all();
         $learners = Learner::orderBy('nama_lengkap')->get();
         $gradeLevels = GradeLevel::orderBy('name')->get();
         $sections = Section::orderBy('name')->get();
        return view('admin.learners.index', compact('learners', 'gradeLevels', 'sections'));
    }

    /**
     * Dasbor murid — diakses lewat sesi learner_id (login via PIN),
     * bukan lewat Auth::user() seperti admin/guru.
     */
    public function learnerDashboard()
    {
        $learner = Learner::find(session('learner_id'));

        return view('learner.dashboard', compact('learner'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'nullable|email|unique:learners,email',
            'pin' => 'nullable|numeric|digits:4',
            'grade_level' => 'required|exists:grade_levels,name',
            'section' => 'required|exists:sections,name',
        ]);

        $data['email'] = $data['email'] ?: null;

        Learner::create($data);

        return redirect()->back()->with('success', 'Murid berhasil ditambahkan!');
    }

    public function update(Request $request, Learner $learner)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'nullable|email|unique:learners,email,' . $learner->id,
            'pin' => 'nullable|numeric|digits:4',
            'grade_level' => 'required|exists:grade_levels,name',
            'section' => 'required|exists:sections,name',
        ]);

        $data['email'] = $data['email'] ?: null;

        // PIN opsional saat edit: kosongkan input berarti PIN lama tidak diubah
        if (empty($data['pin'])) {
            unset($data['pin']);
        }

        $learner->update($data);

        return redirect()->back()->with('success', 'Data murid berhasil diperbarui!');
    }

    public function destroy(Learner $learner)
    {
        $learner->delete();

        return redirect()->back()->with('success', 'Murid berhasil dihapus!');
    }
}
