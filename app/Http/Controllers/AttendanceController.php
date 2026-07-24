<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\GradeLevel;
use App\Models\JamPelajaran;
use App\Models\Learner;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private const HARI_MAP = [
        1 => 'senin',
        2 => 'selasa',
        3 => 'rabu',
        4 => 'kamis',
        5 => 'jumat',
        6 => 'sabtu',
        7 => null, // Minggu — tidak ada jadwal
    ];

    /**
     * Halaman input absensi: pilih Kelas + Tanggal, sistem otomatis
     * menampilkan Jam Pelajaran sesuai jadwal hari itu. Guru hanya
     * melihat jam pelajaran yang dia ampu sendiri; Admin lihat semua.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isGuru = $user->hasRole('guru') && ! $user->hasRole('admin');

        $tanggal = $request->query('tanggal', today()->toDateString());
        $hari = self::HARI_MAP[Carbon::parse($tanggal)->isoWeekday()] ?? null;
        $kelas = $request->query('kelas');

        $jamPelajaranQuery = JamPelajaran::with(['subject', 'guru'])
            ->where('hari', $hari)
            ->orderBy('jam_ke');

        if ($isGuru) {
            $jamPelajaranQuery->where('guru_id', $user->id);
        }

        if ($kelas && $kelas !== 'semua') {
            $jamPelajaranQuery->where('grade_level', $kelas);
        }

        $jamPelajaranList = $hari ? $jamPelajaranQuery->get() : collect();

        $gradeLevels = GradeLevel::orderBy('name')->get();

        $jamPelajaranId = $request->query('jam_pelajaran_id');
        $selectedJamPelajaran = null;
        $learners = collect();
        $existingAttendance = collect();

        if ($jamPelajaranId) {
            $selectedJamPelajaran = JamPelajaran::with(['subject', 'guru'])->find($jamPelajaranId);

            if ($selectedJamPelajaran && (! $isGuru || $selectedJamPelajaran->guru_id === $user->id)) {
                $learners = Learner::where('grade_level', $selectedJamPelajaran->grade_level)
                    ->orderBy('nama_lengkap')
                    ->get();

                $existingAttendance = Attendance::where('jam_pelajaran_id', $selectedJamPelajaran->id)
                    ->where('tanggal', $tanggal)
                    ->get()
                    ->keyBy('learner_id');
            } else {
                $selectedJamPelajaran = null;
            }
        }

        $layout = $isGuru ? 'layouts.app' : 'layouts.admin';

        return view('attendance.index', compact(
            'tanggal', 'hari', 'kelas', 'gradeLevels', 'jamPelajaranList',
            'jamPelajaranId', 'selectedJamPelajaran', 'learners', 'existingAttendance', 'layout'
        ));
    }

    /**
     * Simpan absensi sekaligus untuk semua murid di satu Jam Pelajaran + tanggal.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isGuru = $user->hasRole('guru') && ! $user->hasRole('admin');

        $data = $request->validate([
            'jam_pelajaran_id' => 'required|exists:jam_pelajarans,id',
            'tanggal' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'required|in:' . implode(',', Attendance::STATUS_LIST),
            'keterangan' => 'nullable|array',
        ]);

        $jamPelajaran = JamPelajaran::findOrFail($data['jam_pelajaran_id']);

        abort_if($isGuru && $jamPelajaran->guru_id !== $user->id, 403);

        foreach ($data['status'] as $learnerId => $status) {
            Attendance::updateOrCreate(
                [
                    'learner_id' => $learnerId,
                    'jam_pelajaran_id' => $jamPelajaran->id,
                    'tanggal' => $data['tanggal'],
                ],
                [
                    'status' => $status,
                    'keterangan' => in_array($status, ['sakit', 'izin']) ? ($data['keterangan'][$learnerId] ?? null) : null,
                ]
            );
        }

        return redirect()->route('attendance.index', [
            'kelas' => $jamPelajaran->grade_level,
            'tanggal' => $data['tanggal'],
            'jam_pelajaran_id' => $jamPelajaran->id,
        ])->with('success', 'Absensi berhasil disimpan!');
    }

    /**
     * Rekap absensi bentuk spreadsheet: baris = murid, kolom = setiap
     * kombinasi tanggal + jam pelajaran yang punya data dalam rentang tanggal.
     */
    public function rekap(Request $request)
    {
        $kelas = $request->query('kelas');
        $mulai = $request->query('mulai', today()->startOfWeek()->toDateString());
        $selesai = $request->query('selesai', today()->toDateString());

        $gradeLevels = GradeLevel::orderBy('name')->get();

        $learners = collect();
        $columns = collect();
        $matrix = [];

        if ($kelas && $kelas !== 'semua') {
            $learners = Learner::where('grade_level', $kelas)->orderBy('nama_lengkap')->get();

            $attendances = Attendance::with('jamPelajaran.subject')
                ->whereIn('learner_id', $learners->pluck('id'))
                ->whereBetween('tanggal', [$mulai, $selesai])
                ->get();

            $columns = $attendances
                ->map(fn ($a) => [
                    'key' => $a->tanggal->toDateString() . '-' . $a->jam_pelajaran_id,
                    'tanggal' => $a->tanggal,
                    'jp' => $a->jamPelajaran,
                ])
                ->unique('key')
                ->sortBy(fn ($c) => $c['tanggal']->toDateString() . str_pad($c['jp']->jam_ke, 2, '0', STR_PAD_LEFT))
                ->values();

            foreach ($attendances as $a) {
                $matrix[$a->learner_id][$a->tanggal->toDateString() . '-' . $a->jam_pelajaran_id] = $a;
            }
        }

        return view('attendance.rekap', compact('gradeLevels', 'kelas', 'mulai', 'selesai', 'learners', 'columns', 'matrix'));
    }
}
