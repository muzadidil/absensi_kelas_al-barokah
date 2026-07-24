<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TypingLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TypingLevelController extends Controller
{
    public function index()
    {
        $levels = TypingLevel::withCount('attempts')->orderBy('level_number')->get();

        return view('guru.typing-levels.index', compact('levels'));
    }

    public function store(Request $request)
    {
        $data = $this->validateLevel($request);

        TypingLevel::create($data);

        return redirect()->back()->with('success', 'Tahap latihan berhasil ditambahkan!');
    }

    public function update(Request $request, TypingLevel $typingLevel)
    {
        $data = $this->validateLevel($request, $typingLevel->id);

        $typingLevel->update($data);

        return redirect()->back()->with('success', 'Tahap latihan berhasil diperbarui!');
    }

    public function destroy(TypingLevel $typingLevel)
    {
        $typingLevel->delete();

        return redirect()->back()->with('success', 'Tahap latihan berhasil dihapus!');
    }

    private function validateLevel(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'level_number' => [
                'required', 'integer', 'min:1', 'max:50',
                Rule::unique('typing_levels', 'level_number')->ignore($ignoreId),
            ],
            'name' => 'required|string|max:255',
            'allowed_keys' => 'required|string|max:100',
            'word_bank' => 'nullable|string',
            'description' => 'nullable|string',
            'min_wpm' => 'nullable|integer|min:0|max:500',
            'min_accuracy' => 'nullable|integer|min:0|max:100',
            'max_error_percent' => 'nullable|integer|min:0|max:100',
            'time_limit_seconds' => 'nullable|integer|min:5|max:3600',
        ], [
            'min_wpm.max' => 'Skor minimal (WPM) terlalu besar.',
            'min_accuracy.max' => 'Kebenaran minimal maksimal 100%.',
            'max_error_percent.max' => 'Toleransi kesalahan maksimal 100%.',
            'time_limit_seconds.min' => 'Batas waktu minimal 5 detik.',
            'time_limit_seconds.max' => 'Batas waktu maksimal 3600 detik (60 menit).',
        ]);

        // Ambang lulus: default longgar bila dikosongkan (0 / 100 = tidak menyaring).
        $data['min_wpm'] = $data['min_wpm'] ?? 0;
        $data['min_accuracy'] = $data['min_accuracy'] ?? 0;
        $data['max_error_percent'] = $data['max_error_percent'] ?? 100;

        // Checkbox: tercentang = boleh, tidak tercentang = tidak boleh.
        $data['allow_backspace'] = $request->boolean('allow_backspace');
        $data['allow_space'] = $request->boolean('allow_space');

        // Batas waktu: aktif hanya bila toggle "enable_timeout" dinyalakan.
        $data['time_limit_seconds'] = $request->boolean('enable_timeout')
            ? (int) ($data['time_limit_seconds'] ?? 60)
            : 0;

        return $data;
    }
}
