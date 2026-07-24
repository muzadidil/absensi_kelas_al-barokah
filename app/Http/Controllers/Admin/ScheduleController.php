<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\JamPelajaran;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::orderBy('name')->get();
        $gradeLevels = GradeLevel::orderBy('name')->get();
        $gurus = User::whereHas('roles', fn ($q) => $q->where('name', 'guru'))->orderBy('name')->get();

        $kelas = $request->query('kelas');

        $jadwalQuery = JamPelajaran::with(['subject', 'guru'])
            ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu')")
            ->orderBy('jam_ke');

        if ($kelas && $kelas !== 'semua') {
            $jadwalQuery->where('grade_level', $kelas);
        }

        $jadwal = $jadwalQuery->get()->groupBy('hari');

        return view('admin.schedule.index', compact('subjects', 'gradeLevels', 'gurus', 'jadwal', 'kelas'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
        ]);

        Subject::create($request->only('name'));

        return redirect()->back()->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
        ]);

        $subject->update($request->only('name'));

        return redirect()->back()->with('success', 'Mata pelajaran berhasil diperbarui!');
    }

    public function destroySubject(Subject $subject)
    {
        $subject->delete();

        return redirect()->back()->with('success', 'Mata pelajaran berhasil dihapus!');
    }

    public function storeJamPelajaran(Request $request)
    {
        $data = $request->validate([
            'grade_level' => 'required|exists:grade_levels,name',
            'hari' => [
                'required',
                Rule::in(JamPelajaran::HARI_LIST),
                Rule::unique('jam_pelajarans')->where(fn ($q) => $q
                    ->where('grade_level', $request->grade_level)
                    ->where('jam_ke', $request->jam_ke)),
            ],
            'jam_ke' => 'required|integer|min:1|max:20',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'subject_id' => 'required|exists:subjects,id',
            'guru_id' => 'required|exists:users,id',
        ], [
            'hari.unique' => 'Jam ke- ini sudah ada untuk kelas & hari yang sama.',
        ]);

        JamPelajaran::create($data);

        return redirect()->back()->with('success', 'Jam pelajaran berhasil ditambahkan!');
    }

    public function updateJamPelajaran(Request $request, JamPelajaran $jamPelajaran)
    {
        $data = $request->validate([
            'grade_level' => 'required|exists:grade_levels,name',
            'hari' => [
                'required',
                Rule::in(JamPelajaran::HARI_LIST),
                Rule::unique('jam_pelajarans')->ignore($jamPelajaran->id)->where(fn ($q) => $q
                    ->where('grade_level', $request->grade_level)
                    ->where('jam_ke', $request->jam_ke)),
            ],
            'jam_ke' => 'required|integer|min:1|max:20',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'subject_id' => 'required|exists:subjects,id',
            'guru_id' => 'required|exists:users,id',
        ], [
            'hari.unique' => 'Jam ke- ini sudah ada untuk kelas & hari yang sama.',
        ]);

        $jamPelajaran->update($data);

        return redirect()->back()->with('success', 'Jam pelajaran berhasil diperbarui!');
    }

    public function destroyJamPelajaran(JamPelajaran $jamPelajaran)
    {
        $jamPelajaran->delete();

        return redirect()->back()->with('success', 'Jam pelajaran berhasil dihapus!');
    }
}
