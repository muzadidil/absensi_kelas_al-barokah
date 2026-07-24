@php
    /** @var \App\Models\QuizQuestion|null $question */
    $opts = $question ? $question->options->values() : collect();
    $correctIndex = $question ? $opts->search(fn ($o) => $o->is_correct) : null;
    if ($correctIndex === false) { $correctIndex = null; }
    $maxSlots = 5;
@endphp

<div class="mb-3">
    <label class="form-label">Pertanyaan</label>
    <textarea name="question_text" class="form-control" rows="2" placeholder="Tulis pertanyaan..." required>{{ $question->question_text ?? '' }}</textarea>
</div>

<label class="form-label">Opsi Jawaban <span class="text-muted small">(isi 2–5, pilih satu yang benar)</span></label>
<div class="mb-3">
    @for($i = 0; $i < $maxSlots; $i++)
        <div class="input-group mb-2">
            <div class="input-group-text">
                <input class="form-check-input mt-0" type="radio" name="correct" value="{{ $i }}"
                       title="Tandai sebagai jawaban benar"
                       {{ $correctIndex === $i ? 'checked' : '' }}>
            </div>
            <input type="text" name="options[]" class="form-control"
                   value="{{ $opts[$i]->option_text ?? '' }}"
                   placeholder="Opsi {{ $i + 1 }}{{ $i < 2 ? ' (wajib)' : ' (opsional)' }}">
        </div>
    @endfor
    <div class="form-text text-muted"><i class="bi bi-info-circle me-1"></i> Radio di kiri = jawaban benar. Opsi kosong diabaikan. Posisi opsi diacak otomatis saat murid mengerjakan.</div>
</div>

<div class="mb-1">
    <label class="form-label">Penjelasan <span class="text-muted small">(opsional, tampil ke murid saat menjawab salah)</span></label>
    <textarea name="explanation" class="form-control" rows="2" placeholder="Kenapa jawabannya begitu...">{{ $question->explanation ?? '' }}</textarea>
</div>
