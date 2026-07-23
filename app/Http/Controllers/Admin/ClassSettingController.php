<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Http\Request;

class ClassSettingController extends Controller
{
    public function index()
    {
        $gradeLevels = GradeLevel::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();

        return view('admin.class-settings.index', compact('gradeLevels', 'sections'));
    }

    public function storeGradeLevel(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:grade_levels,name',
        ]);

        GradeLevel::create($request->only('name'));

        return redirect()->back()->with('success', 'Tingkat kelas berhasil ditambahkan!');
    }

    public function updateGradeLevel(Request $request, GradeLevel $gradeLevel)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:grade_levels,name,' . $gradeLevel->id,
        ]);

        $gradeLevel->update($request->only('name'));

        return redirect()->back()->with('success', 'Tingkat kelas berhasil diperbarui!');
    }

    public function destroyGradeLevel(GradeLevel $gradeLevel)
    {
        $gradeLevel->delete();

        return redirect()->back()->with('success', 'Tingkat kelas berhasil dihapus!');
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sections,name',
        ]);

        Section::create($request->only('name'));

        return redirect()->back()->with('success', 'Tahun ajaran berhasil ditambahkan!');
    }

    public function updateSection(Request $request, Section $section)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sections,name,' . $section->id,
        ]);

        $section->update($request->only('name'));

        return redirect()->back()->with('success', 'Tahun ajaran berhasil diperbarui!');
    }

    public function destroySection(Section $section)
    {
        $section->delete();

        return redirect()->back()->with('success', 'Tahun ajaran berhasil dihapus!');
    }
}
