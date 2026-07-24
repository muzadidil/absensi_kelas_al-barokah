<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\JamPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    private const HARI_MAP = [
        1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu', 7 => null,
    ];

    public function index()
    {
        $user = auth()->user();
        $hariIni = self::HARI_MAP[Carbon::today()->isoWeekday()] ?? null;

        $jadwalHariIni = JamPelajaran::with('subject')
            ->where('guru_id', $user->id)
            ->where('hari', $hariIni)
            ->orderBy('jam_ke')
            ->get();

        $totalJadwalMinggu = JamPelajaran::where('guru_id', $user->id)->count();

        $jadwalHariIniIds = $jadwalHariIni->pluck('id');
        $sudahDiisiHariIni = Attendance::whereIn('jam_pelajaran_id', $jadwalHariIniIds)
            ->whereDate('tanggal', today())
            ->distinct('jam_pelajaran_id')
            ->count('jam_pelajaran_id');

        return view('guru.dashboard', compact('jadwalHariIni', 'totalJadwalMinggu', 'sudahDiisiHariIni', 'hariIni'));
    }

    // Admin: list all users with the 'guru' role
    public function manage()
    {
        $gurus = User::whereHas('roles', fn ($q) => $q->where('name', 'guru'))
            ->orderBy('name')
            ->get();

        return view('admin.guru.index', compact('gurus'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Guru berhasil dihapus.');
    }
}
