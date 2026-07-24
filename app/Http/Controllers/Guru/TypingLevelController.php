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
        return $request->validate([
            'level_number' => [
                'required', 'integer', 'min:1', 'max:50',
                Rule::unique('typing_levels', 'level_number')->ignore($ignoreId),
            ],
            'name' => 'required|string|max:255',
            'allowed_keys' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);
    }
}
