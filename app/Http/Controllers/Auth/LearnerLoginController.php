<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LearnerLoginController extends Controller
{
    /**
     * Daftar murid (id + nama lengkap) berdasarkan tingkat kelas,
     * dipakai untuk mengisi dropdown "Pilih Nama" via AJAX di halaman login
     * DAN dropdown "Assign Individual" di halaman detail tugas (admin).
     */
    public function getByGrade(string $gradeLevel): JsonResponse
    {
        $learners = Learner::where('grade_level', $gradeLevel)
            ->orderBy('nama_lengkap')
            ->orderBy('id')
            ->get();

        // Kalau ada dua murid dengan nama_lengkap yang sama persis, dropdown
        // (baik di halaman login murid maupun "Assign Individual" admin) akan
        // menampilkan opsi yang terlihat identik — orang yang memilih tidak
        // bisa membedakan mana yang mana, sehingga bisa salah pilih ID murid.
        // Tambahkan penanda ID hanya untuk nama yang bentrok, supaya kasus
        // normal (nama unik) tetap tampil bersih tanpa noise.
        $namaCount = $learners->countBy('nama_lengkap');

        $result = $learners->map(function (Learner $learner) use ($namaCount) {
            $name = $learner->nama_lengkap;

            if ($namaCount[$name] > 1) {
                $name .= ' (ID ' . $learner->id . ')';
            }

            return [
                'id' => $learner->id,
                'name' => $name,
            ];
        });

        return response()->json($result);
    }

    /**
     * Proses login murid: cocokkan learner_id + pin, simpan learner_id di session.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'learner_id' => 'required|exists:learners,id',
            'pin' => 'required|digits:4',
        ]);

        $learner = Learner::find($request->learner_id);

        if ($learner->pin === null || $learner->pin !== $request->pin) {
            return redirect()->back()->withErrors([
                'pin' => 'PIN yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('learner_id', $learner->id);

        return redirect()->route('learner.dashboard');
    }

    /**
     * Logout murid: hapus learner_id dari session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('learner_id');
        $request->session()->regenerate();

        return redirect()->route('login');
    }
}
