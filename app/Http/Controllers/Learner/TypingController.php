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
     * Halaman latihan: tampilkan teks latihan untuk tahap ini.
     */
    public function show(TypingLevel $typingLevel)
    {
        $learner = Learner::find(session('learner_id'));

        $practiceText = $this->generatePracticeText($typingLevel);

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
     * Bikin teks latihan: kalau tahap punya word bank (kata sungguhan yang
     * diisi Guru), acak beberapa kata dari situ. Kalau kosong, fallback ke
     * kelompok huruf acak dari tombol yang diizinkan seperti sebelumnya.
     */
    private function generatePracticeText(TypingLevel $typingLevel, int $targetWordCount = 40): string
    {
        $words = $this->wordsFromBank($typingLevel->word_bank);

        if (empty($words)) {
            return $this->generateRandomLetterText($typingLevel->allowed_keys);
        }

        $picked = [];
        for ($i = 0; $i < $targetWordCount; $i++) {
            $picked[] = $words[array_rand($words)];
        }

        return implode(' ', $picked);
    }

    /**
     * Pecah isi word bank (dipisah koma, spasi, dan/atau baris baru) jadi
     * array kata bersih huruf kecil. Huruf kecil dipakai karena tahap latihan
     * hanya melatih tombol huruf tanpa Shift.
     */
    private function wordsFromBank(?string $wordBank): array
    {
        if (! $wordBank) {
            return [];
        }

        $words = preg_split('/[\s,]+/u', trim($wordBank));

        return array_values(array_filter(array_map(
            fn ($word) => mb_strtolower(trim($word)),
            $words
        )));
    }

    private function generateRandomLetterText(string $allowedKeys, int $totalChars = 120): string
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
