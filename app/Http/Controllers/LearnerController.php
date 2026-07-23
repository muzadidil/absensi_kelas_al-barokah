<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Learner;
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
        return view('admin.learners.index', compact('learners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email|unique:learners,email',
            'grade_level' => 'required',
            'section' => 'required',
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
            'grade_level' => 'required',
            'section' => 'required',
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
