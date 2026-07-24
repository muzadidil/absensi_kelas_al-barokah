@php
    /** @var \App\Models\TypingLevel|null $level */
    $allowBackspace = $level ? $level->allow_backspace : true;
    $allowSpace     = $level ? $level->allow_space : true;
    $minWpm         = $level ? $level->min_wpm : null;
    $minAccuracy    = $level ? $level->min_accuracy : null;
    $maxError       = $level ? $level->max_error_percent : null;
    $enableTimeout  = $level ? $level->hasTimeLimit() : false;
    $timeLimit      = $level && $level->time_limit_seconds > 0 ? $level->time_limit_seconds : 60;
    $sfx            = $level->id ?? 'new';
@endphp

<div class="mb-3">
    <label class="form-label">Nomor Tahap</label>
    <input type="number" name="level_number" class="form-control" value="{{ $level->level_number ?? '' }}" min="1" placeholder="mis. 1" required>
</div>
<div class="mb-3">
    <label class="form-label">Nama Tahap</label>
    <input type="text" name="name" class="form-control" value="{{ $level->name ?? '' }}" placeholder="Contoh: Tahap 4: Angka" required>
</div>
<div class="mb-3">
    <label class="form-label">Tombol yang Dilatih <span class="text-muted small">(huruf/simbol tanpa spasi, mis. asdfghjkl;)</span></label>
    <input type="text" name="allowed_keys" class="form-control" value="{{ $level->allowed_keys ?? '' }}" placeholder="asdfghjkl;" required>
</div>
<div class="mb-3">
    <label class="form-label">Bank Kata <span class="text-muted small">(opsional — kata sungguhan untuk dilatih, pisahkan koma/spasi/baris baru. Kosong = teks acak dari tombol di atas)</span></label>
    <textarea name="word_bank" class="form-control" rows="4" placeholder="contoh: ada, akad, akal, gagah, salah, ...">{{ $level->word_bank ?? '' }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi <span class="text-muted small">(opsional)</span></label>
    <textarea name="description" class="form-control" rows="2">{{ $level->description ?? '' }}</textarea>
</div>

<hr>

<!-- Mode ketik -->
<p class="fw-semibold mb-2"><i class="bi bi-keyboard me-1"></i> Mode Ketik</p>
<div class="form-check form-switch mb-2">
    <input class="form-check-input" type="checkbox" role="switch" name="allow_backspace" value="1"
           id="allow_backspace_{{ $level->id ?? 'new' }}" {{ $allowBackspace ? 'checked' : '' }}>
    <label class="form-check-label" for="allow_backspace_{{ $level->id ?? 'new' }}">
        Boleh pakai <strong>Backspace</strong>
        <span class="text-muted small d-block">Matikan agar hasil ketik bersih apa adanya (salah tetap salah). Nyalakan bila tahap ini melatih backspace.</span>
    </label>
</div>
<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" name="allow_space" value="1"
           id="allow_space_{{ $level->id ?? 'new' }}" {{ $allowSpace ? 'checked' : '' }}>
    <label class="form-check-label" for="allow_space_{{ $level->id ?? 'new' }}">
        Boleh pakai <strong>Spasi</strong>
        <span class="text-muted small d-block">Matikan agar murid tak perlu menekan spasi — selesai satu kata langsung pindah ke kata berikutnya.</span>
    </label>
</div>

<hr>

<!-- Batas waktu (timeout) -->
<p class="fw-semibold mb-2"><i class="bi bi-stopwatch me-1"></i> Batas Waktu (Timeout)</p>
<div class="form-check form-switch mb-2">
    <input class="form-check-input" type="checkbox" role="switch" name="enable_timeout" value="1"
           id="enable_timeout_{{ $sfx }}" {{ $enableTimeout ? 'checked' : '' }}
           onchange="document.getElementById('time_wrap_{{ $sfx }}').style.display = this.checked ? 'block' : 'none'">
    <label class="form-check-label" for="enable_timeout_{{ $sfx }}">
        Aktifkan batas waktu
        <span class="text-muted small d-block">Seperti typing test — murid mengetik sampai waktu habis, lalu otomatis dinilai dari kata yang sempat dikerjakan.</span>
    </label>
</div>
<div id="time_wrap_{{ $sfx }}" class="mb-2" style="display: {{ $enableTimeout ? 'block' : 'none' }};">
    <label class="form-label small mb-1">Durasi (detik)</label>
    <input type="number" name="time_limit_seconds" class="form-control" value="{{ $timeLimit }}" min="5" max="3600" placeholder="60">
    <div class="form-text text-muted">Fleksibel — contoh: <strong>60</strong> = 1 menit, <strong>120</strong> = 2 menit, <strong>30</strong> = 30 detik.</div>
</div>

<hr>

<!-- Syarat lulus -->
<p class="fw-semibold mb-1"><i class="bi bi-trophy me-1"></i> Syarat Lulus <span class="text-muted small fw-normal">(untuk membuka tahap berikutnya)</span></p>
<p class="text-muted small mb-2">Kosongkan / 0 berarti tidak disyaratkan. Murid lulus bila memenuhi <strong>ketiganya</strong>.</p>
<div class="criteria-box">
    <div class="row g-2">
        <div class="col-4">
            <label class="form-label small mb-1">Kecepatan<br><span class="text-muted">min. WPM</span></label>
            <input type="number" name="min_wpm" class="form-control" value="{{ $minWpm }}" min="0" max="500" placeholder="0">
        </div>
        <div class="col-4">
            <label class="form-label small mb-1">Kebenaran<br><span class="text-muted">min. % benar</span></label>
            <input type="number" name="min_accuracy" class="form-control" value="{{ $minAccuracy }}" min="0" max="100" placeholder="0">
        </div>
        <div class="col-4">
            <label class="form-label small mb-1">Kesalahan<br><span class="text-muted">maks. % salah</span></label>
            <input type="number" name="max_error_percent" class="form-control" value="{{ $maxError }}" min="0" max="100" placeholder="100">
        </div>
    </div>
</div>
