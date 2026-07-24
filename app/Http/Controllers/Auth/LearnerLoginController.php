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
     * dipakai untuk mengisi dropdown "Pilih Nama" via AJAX di halaman login.
     */
    public function getByGrade(string $gradeLevel): JsonResponse
    {
        $learners = Learner::where('grade_level', $gradeLevel)
            ->orderBy('fname')
            ->get()
            ->map(fn (Learner $learner) => [
                'id' => $learner->id,
                'name' => collect([$learner->fname, $learner->mname, $learner->lname])
                    ->filter()
                    ->implode(' '),
            ]);

        return response()->json($learners);
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
