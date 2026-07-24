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
     * Daftar tahap latihan + skor terbaik + status lulus/terkunci untuk murid.
     * Tahap terkunci sampai tahap sebelumnya (nomor lebih kecil) dinyatakan lulus.
     */
    public function index()
    {
        $learner = Learner::find(session('learner_id'));

        $levels = TypingLevel::orderBy('level_number')->get();

        $attempts = TypingAttempt::where('learner_id', $learner->id)
            ->whereIn('typing_level_id', $levels->pluck('id'))
            ->get();

        // Rekor terbaik per tahap (berdasarkan WPM tertinggi).
        $bestAttempts = $attempts->sortByDesc('wpm')
            ->groupBy('typing_level_id')
            ->map(fn ($group) => $group->first());

        $passedLevelIds = $attempts->where('passed', true)
            ->pluck('typing_level_id')
            ->unique();

        // Kunci progresif: tahap pertama terbuka; berikutnya terbuka bila tahap
        // sebelumnya sudah lulus.
        $unlocked = [];
        $prevPassed = true;
        foreach ($levels as $level) {
            $unlocked[$level->id] = $prevPassed;
            $prevPassed = $passedLevelIds->contains($level->id);
        }

        return view('learner.typing.index', compact(
            'learner', 'levels', 'bestAttempts', 'passedLevelIds', 'unlocked'
        ));
    }

    /**
     * Halaman latihan: tolak bila tahap masih terkunci, lalu tampilkan teks latihan.
     */
    public function show(TypingLevel $typingLevel)
    {
        $learner = Learner::find(session('learner_id'));

        $prev = TypingLevel::where('level_number', '<', $typingLevel->level_number)
            ->orderByDesc('level_number')
            ->first();

        if ($prev) {
            $passedPrev = TypingAttempt::where('learner_id', $learner->id)
                ->where('typing_level_id', $prev->id)
                ->where('passed', true)
                ->exists();

            if (! $passedPrev) {
                return redirect()->route('learner.typing.index')
                    ->with('error', "Selesaikan & lulus tahap \"{$prev->name}\" dulu untuk membuka tahap ini.");
            }
        }

        $practiceText = $this->generatePracticeText($typingLevel);

        return view('learner.typing.practice', compact('learner', 'typingLevel', 'practiceText'));
    }

    /**
     * Simpan hasil latihan. Jumlah kata benar/salah & WPM dihitung di client,
     * tapi status LULUS dihitung ulang di server dari ambang tahap (anti-curang).
     */
    public function submit(Request $request, TypingLevel $typingLevel)
    {
        $learner = Learner::find(session('learner_id'));

        $data = $request->validate([
            'wpm' => 'required|integer|min:0|max:500',
            'correct_words' => 'required|integer|min:0|max:1000',
            'total_words' => 'required|integer|min:1|max:1000',
            'duration_seconds' => 'required|integer|min:1|max:3600',
        ]);

        $total = $data['total_words'];
        $correct = min($data['correct_words'], $total);
        $wrong = $total - $correct;

        $accuracy = (int) round($correct / $total * 100); // % kata benar
        $errorPercent = 100 - $accuracy;                   // % kata salah

        $passed = $typingLevel->isPassing($data['wpm'], $accuracy, $errorPercent);

        TypingAttempt::create([
            'learner_id' => $learner->id,
            'typing_level_id' => $typingLevel->id,
            'wpm' => $data['wpm'],
            'accuracy' => $accuracy,
            'correct_words' => $correct,
            'wrong_words' => $wrong,
            'total_words' => $total,
            'passed' => $passed,
            'duration_seconds' => $data['duration_seconds'],
        ]);

        $summary = "Hasil: {$correct} kata benar, {$wrong} salah, {$data['wpm']} kata/menit (akurasi {$accuracy}%).";
        $verdict = $passed
            ? ' 🎉 LULUS! Tahap berikutnya terbuka.'
            : ($typingLevel->hasPassCriteria() ? ' Belum lulus — coba lagi ya.' : '');

        return redirect()->route('learner.typing.index')
            ->with($passed ? 'success' : 'warning', $summary . $verdict);
    }

    /**
     * Bikin teks latihan: kalau tahap punya word bank (kata sungguhan yang
     * diisi Guru), acak beberapa kata dari situ. Kalau kosong, fallback ke
     * kelompok huruf acak dari tombol yang diizinkan seperti sebelumnya.
     */
    private function generatePracticeText(TypingLevel $typingLevel): string
    {
        // Mode berwaktu butuh teks lebih panjang supaya murid tak kehabisan kata
        // sebelum waktu habis (± 90 kata/menit kapasitas ketik).
        $targetWordCount = $typingLevel->hasTimeLimit()
            ? min(400, max(60, (int) ceil($typingLevel->time_limit_seconds / 60 * 90)))
            : 40;

        $words = $this->wordsFromBank($typingLevel->word_bank);

        if (empty($words)) {
            $totalChars = $typingLevel->hasTimeLimit()
                ? min(2400, max(300, $typingLevel->time_limit_seconds * 6))
                : 120;

            return $this->generateRandomLetterText($typingLevel->allowed_keys, $totalChars);
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
