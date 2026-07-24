<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Models\TypingAttempt;
use App\Models\TypingLevel;
use Illuminate\Http\Request;

class TypingController extends Controller
{
    /**
     * Daftar tahap latihan + skor terbaik murid yang sedang login di tiap tahap.
     */
    public function index()
    {
        $learner = Learner::find(session('learner_id'));

        $levels = TypingLevel::orderBy('level_number')->get();

        $bestAttempts = TypingAttempt::where('learner_id', $learner->id)
            ->whereIn('typing_level_id', $levels->pluck('id'))
            ->orderByDesc('wpm')
            ->get()
            ->groupBy('typing_level_id')
            ->map(fn ($attempts) => $attempts->first());

        return view('learner.typing.index', compact('learner', 'levels', 'bestAttempts'));
    }

    /**
     * Halaman latihan: tampilkan teks acak dari tombol yang diizinkan tahap ini.
     */
    public function show(TypingLevel $typingLevel)
    {
        $learner = Learner::find(session('learner_id'));

        $practiceText = $this->generatePracticeText($typingLevel->allowed_keys);

        return view('learner.typing.practice', compact('learner', 'typingLevel', 'practiceText'));
    }

    /**
     * Simpan hasil latihan (dihitung di sisi client: WPM, akurasi, durasi).
     */
    public function submit(Request $request, TypingLevel $typingLevel)
    {
        $learner = Learner::find(session('learner_id'));

        $data = $request->validate([
            'wpm' => 'required|integer|min:0|max:500',
            'accuracy' => 'required|integer|min:0|max:100',
            'duration_seconds' => 'required|integer|min:1|max:3600',
        ]);

        TypingAttempt::create([
            'learner_id' => $learner->id,
            'typing_level_id' => $typingLevel->id,
            'wpm' => $data['wpm'],
            'accuracy' => $data['accuracy'],
            'duration_seconds' => $data['duration_seconds'],
        ]);

        return redirect()->route('learner.typing.index')
            ->with('success', "Latihan selesai! Kecepatan {$data['wpm']} WPM, akurasi {$data['accuracy']}%.");
    }

    /**
     * Bikin teks latihan acak: kelompok 3-5 huruf dari tombol yang diizinkan,
     * dipisah spasi seperti "kata", supaya terasa seperti mengetik kalimat.
     */
    private function generatePracticeText(string $allowedKeys, int $totalChars = 120): string
    {
        $keys = str_split($allowedKeys);
        $words = [];
        $length = 0;

        while ($length < $totalChars) {
            $wordLength = random_int(2, 5);
            $word = '';
            for ($i = 0; $i < $wordLength; $i++) {
                $word .= $keys[array_rand($keys)];
            }
            $words[] = $word;
            $length += $wordLength + 1;
        }

        return implode(' ', $words);
    }
}
