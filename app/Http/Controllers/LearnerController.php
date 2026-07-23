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
         $learners = Learner::orderByRaw("CONCAT(lname, fname, mname) ASC")->get();
         $gradeLevels = GradeLevel::orderBy('name')->get();
         $sections = Section::orderBy('name')->get();
        return view('admin.learners.index', compact('learners', 'gradeLevels', 'sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email|unique:learners,email',
            'grade_level' => 'required|exists:grade_levels,name',
            'section' => 'required|exists:sections,name',
        ]);

        Learner::create($request->all());

        return redirect()->back()->with('success', 'Murid berhasil ditambahkan!');
    }

    public function update(Request $request, Learner $learner)
    {
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email|unique:learners,email,' . $learner->id,
            'grade_level' => 'required|exists:grade_levels,name',
            'section' => 'required|exists:sections,name',
        ]);

        $learner->update($request->all());

        return redirect()->back()->with('success', 'Data murid berhasil diperbarui!');
    }

    public function destroy(Learner $learner)
    {
        $learner->delete();

        return redirect()->back()->with('success', 'Murid berhasil dihapus!');
    }
}
