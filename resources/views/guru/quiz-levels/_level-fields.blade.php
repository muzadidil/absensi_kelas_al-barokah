@php
    /** @var \App\Models\QuizLevel|null $level */
    $resetOnFail = $level ? $level->reset_to_first_on_fail : false;
    $sfx = $level->id ?? 'new';
@endphp

<div class="mb-3">
    <label class="form-label">Nomor Tahap</label>
    <input type="number" name="level_number" class="form-control" value="{{ $level->level_number ?? '' }}" min="1" placeholder="mis. 1" required>
</div>
<div class="mb-3">
    <label class="form-label">Nama Tahap</label>
    <input type="text" name="name" class="form-control" value="{{ $level->name ?? '' }}" placeholder="Contoh: Tahap 1 — Dasar" required>
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi <span class="text-muted small">(opsional)</span></label>
    <textarea name="description" class="form-control" rows="2" placeholder="Materi/tema tahap ini">{{ $level->description ?? '' }}</textarea>
</div>

<hr>

<p class="fw-semibold mb-2"><i class="bi bi-fire me-1"></i> Mode Pamungkas</p>
<div class="form-check form-switch mb-1">
    <input class="form-check-input" type="checkbox" role="switch" name="reset_to_first_on_fail" value="1"
           id="reset_fail_{{ $sfx }}" {{ $resetOnFail ? 'checked' : '' }}>
    <label class="form-check-label" for="reset_fail_{{ $sfx }}">
        Gagal di tahap ini → <strong>balik ke Tahap 1</strong>
        <span class="text-muted small d-block">Kalau nyala, salah menjawab di tahap ini melempar murid kembali ke Tahap 1 dan semua kunci menutup lagi. Cocok buat gauntlet pemungkas di akhir. Kalau mati, gagal hanya mengulang tahap ini.</span>
    </label>
</div>
