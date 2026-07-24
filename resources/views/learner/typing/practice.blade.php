@extends('layouts.learner')

@section('title', $typingLevel->name)

@push('styles')
<style>
    #targetText {
        font-family: 'Consolas', 'Courier New', monospace;
        font-size: 1.35rem;
        line-height: 2.1;
        letter-spacing: 1px;
        max-height: 220px;
        overflow-y: auto;
        background: #f8f9fc;
        border-radius: 0.75rem;
        padding: 1rem 1.15rem;
        word-break: break-word;
    }
    #targetText .word { display: inline-block; margin: 0 2px; border-radius: 4px; padding: 0 1px; }
    #targetText .word.wcurrent { background: rgba(79,107,237,.10); box-shadow: inset 0 -2px 0 var(--lems-accent); }
    #targetText .word.wcorrect { }
    #targetText .word.wwrong   { text-decoration: line-through; text-decoration-color: rgba(220,53,69,.5); }
    #targetText .ch { color: #9aa1ad; }
    #targetText .ch.ok     { color: #157347; }
    #targetText .ch.bad    { color: #dc3545; text-decoration: underline; }
    #targetText .ch.extra  { color: #dc3545; opacity: .7; }
    #targetText .ch.cursor { background: #ffe08a; border-radius: 3px; color: #1e2333; }

    .stat-pill { text-align: center; }
    .stat-pill .num { font-size: 1.5rem; font-weight: 700; line-height: 1; }
    .stat-pill .lbl { font-size: 0.72rem; color: var(--lems-ink-muted); text-transform: uppercase; letter-spacing: .04em; }
    .rule-tag { font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.6rem; border-radius: 999px; background: rgba(220,53,69,.12); color: #b02a37; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
        <span class="badge bg-primary mb-1">Tahap {{ $typingLevel->level_number }}</span>
        <h5 class="fw-bold mb-0">{{ $typingLevel->name }}</h5>
        <p class="text-muted small mb-0">Tombol: <code>{{ strtoupper($typingLevel->allowed_keys) }}</code></p>
    </div>
    <a href="{{ route('learner.typing.index') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Aturan & syarat -->
<div class="d-flex flex-wrap gap-2 mb-3">
    @unless($typingLevel->allow_backspace)
        <span class="rule-tag"><i class="bi bi-backspace me-1"></i>Backspace dimatikan</span>
    @endunless
    @unless($typingLevel->allow_space)
        <span class="rule-tag"><i class="bi bi-space me-1"></i>Tanpa spasi (otomatis pindah kata)</span>
    @endunless
    @if($typingLevel->hasTimeLimit())
        <span class="rule-tag" style="background:rgba(13,110,253,.12);color:#0a58ca;">
            <i class="bi bi-stopwatch me-1"></i>Batas waktu {{ $typingLevel->time_limit_seconds }} dtk
        </span>
    @endif
    @if($typingLevel->hasPassCriteria())
        <span class="rule-tag" style="background:rgba(79,70,229,.10);color:#4f46e5;">
            <i class="bi bi-trophy me-1"></i>Lulus:
            @if($typingLevel->min_wpm > 0) ≥{{ $typingLevel->min_wpm }} WPM @endif
            @if($typingLevel->min_accuracy > 0) · benar ≥{{ $typingLevel->min_accuracy }}% @endif
            @if($typingLevel->max_error_percent < 100) · salah ≤{{ $typingLevel->max_error_percent }}% @endif
        </span>
    @endif
</div>

<!-- Area latihan -->
<div class="card" id="practiceCard">
    <div class="card-body">
        <div id="targetText"></div>

        <input type="text" id="typedInput" class="form-control form-control-lg mt-3" autocomplete="off" autocorrect="off"
            autocapitalize="off" spellcheck="false" placeholder="Klik di sini lalu mulai mengetik...">

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="small text-muted">
                <span class="me-3"><i class="bi bi-stopwatch me-1"></i> <span id="timerLabel">Waktu</span>: <span id="timer">0</span> dtk</span>
                <span><i class="bi bi-list-ol me-1"></i> Kata: <span id="wordProgress">0</span> / <span id="wordTotal">0</span></span>
            </div>
            <button type="button" id="finishBtn" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-flag-fill me-1"></i> Selesai
            </button>
        </div>
    </div>
</div>

<!-- Hasil -->
<div class="card d-none" id="resultCard">
    <div class="card-body text-center">
        <div id="verdictIcon" class="mb-2"></div>
        <h4 class="fw-bold mb-1" id="verdictText">Hasil Latihan</h4>
        <p class="text-muted small mb-4" id="verdictSub"></p>

        <div class="row g-3 justify-content-center mb-4">
            <div class="col-3 stat-pill"><div class="num text-success" id="rCorrect">0</div><div class="lbl">Kata Benar</div></div>
            <div class="col-3 stat-pill"><div class="num text-danger" id="rWrong">0</div><div class="lbl">Kata Salah</div></div>
            <div class="col-3 stat-pill"><div class="num text-primary" id="rWpm">0</div><div class="lbl">Kata / Menit</div></div>
            <div class="col-3 stat-pill"><div class="num" id="rAcc">0%</div><div class="lbl">Akurasi</div></div>
        </div>

        <form id="resultForm" method="POST" action="{{ route('learner.typing.submit', $typingLevel->id) }}">
            @csrf
            <input type="hidden" name="wpm" id="wpmInput">
            <input type="hidden" name="correct_words" id="correctInput">
            <input type="hidden" name="total_words" id="totalInput">
            <input type="hidden" name="duration_seconds" id="durationInput">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i> Simpan Hasil & Kembali
            </button>
        </form>
    </div>
</div>

<script>
    const targetText   = @json($practiceText);
    const allowBackspace = @json((bool) $typingLevel->allow_backspace);
    const allowSpace     = @json((bool) $typingLevel->allow_space);
    const timeLimit      = @json((int) $typingLevel->time_limit_seconds); // 0 = tanpa batas
    const passCfg = {
        minWpm: @json((int) $typingLevel->min_wpm),
        minAcc: @json((int) $typingLevel->min_accuracy),
        maxErr: @json((int) $typingLevel->max_error_percent),
        hasCriteria: @json((bool) $typingLevel->hasPassCriteria()),
    };

    const words = targetText.split(' ').filter(w => w.length);
    const targetEl = document.getElementById('targetText');
    const input    = document.getElementById('typedInput');
    const timerEl  = document.getElementById('timer');
    const wordProgressEl = document.getElementById('wordProgress');

    let idx = 0;
    const typedWords = [];
    let finished = false;
    let startTime = null;
    let timerInterval = null;

    document.getElementById('wordTotal').textContent = words.length;

    // Mode berwaktu: tampilkan hitung mundur dari batas waktu.
    if (timeLimit > 0) {
        document.getElementById('timerLabel').textContent = 'Sisa';
        timerEl.textContent = timeLimit;
    }

    function esc(c) {
        if (c === '&') return '&amp;';
        if (c === '<') return '&lt;';
        if (c === '>') return '&gt;';
        if (c === ' ') return '&nbsp;';
        return c;
    }

    function renderWord(target, typed, state) {
        let html = '';
        for (let i = 0; i < target.length; i++) {
            let cls = 'pending';
            if (i < typed.length) cls = (typed[i] === target[i]) ? 'ok' : 'bad';
            else if (state === 'current' && i === typed.length) cls = 'cursor';
            html += `<span class="ch ${cls}">${esc(target[i])}</span>`;
        }
        for (let i = target.length; i < typed.length; i++) {
            html += `<span class="ch extra">${esc(typed[i])}</span>`;
        }
        let wc = state === 'current' ? 'wcurrent'
               : state === 'done' ? (typed === target ? 'wcorrect' : 'wwrong') : '';
        return `<span class="word ${wc}">${html}</span>`;
    }

    function render() {
        let html = '';
        for (let w = 0; w < words.length; w++) {
            if (w < idx)        html += renderWord(words[w], typedWords[w] ?? '', 'done');
            else if (w === idx) html += renderWord(words[w], input.value, 'current');
            else                html += renderWord(words[w], '', 'future');
        }
        targetEl.innerHTML = html;
        const cur = targetEl.querySelector('.word.wcurrent');
        if (cur) cur.scrollIntoView({ block: 'nearest' });
    }

    function startTimerIfNeeded() {
        if (startTime) return;
        startTime = Date.now();
        timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            if (timeLimit > 0) {
                const remaining = Math.max(0, timeLimit - elapsed);
                timerEl.textContent = remaining;
                if (remaining <= 0) {
                    finishNow(); // waktu habis → otomatis selesai
                }
            } else {
                timerEl.textContent = elapsed;
            }
        }, 250);
    }

    function commitWord(typed) {
        typedWords[idx] = typed;
        idx++;
        input.value = '';
        wordProgressEl.textContent = idx;
        if (idx >= words.length) {
            finishPractice();
        } else {
            render();
        }
    }

    function finishNow() {
        if (finished) return;
        if (input.value.length > 0) {
            typedWords[idx] = input.value;
            idx++;
            wordProgressEl.textContent = idx;
        }
        finishPractice();
    }

    function finishPractice() {
        finished = true;
        input.disabled = true;
        clearInterval(timerInterval);
        render();

        const durationSeconds = Math.max(1, Math.round((Date.now() - (startTime ?? Date.now())) / 1000));
        const attempted = typedWords.filter(w => w !== undefined).length;

        // Mode berwaktu: nilai hanya kata yang sempat dikerjakan (seperti typing test).
        // Mode biasa: harus menyelesaikan semua kata — yang tak diketik dihitung salah.
        let total, correct = 0;
        if (timeLimit > 0) {
            total = Math.max(1, attempted);
            for (let i = 0; i < attempted; i++) {
                if (typedWords[i] === words[i]) correct++;
            }
        } else {
            total = words.length;
            for (let i = 0; i < total; i++) {
                if ((typedWords[i] ?? '') === words[i]) correct++;
            }
        }
        const wrong = total - correct;
        // Batasi 0–500 agar selalu lolos validasi server (max:500) walau ketikan sangat cepat.
        const wpm = Math.min(500, Math.max(0, Math.round(Math.max(1, attempted) / (durationSeconds / 60))));
        const accuracy = Math.round((correct / total) * 100);
        const errorPercent = 100 - accuracy;

        // Fill form
        document.getElementById('wpmInput').value = wpm;
        document.getElementById('correctInput').value = correct;
        document.getElementById('totalInput').value = total;
        document.getElementById('durationInput').value = durationSeconds;

        // Fill result panel
        document.getElementById('rCorrect').textContent = correct;
        document.getElementById('rWrong').textContent = wrong;
        document.getElementById('rWpm').textContent = wpm;
        document.getElementById('rAcc').textContent = accuracy + '%';

        // Preview verdict (server tetap yang menentukan sebenarnya)
        const iconEl = document.getElementById('verdictIcon');
        const textEl = document.getElementById('verdictText');
        const subEl  = document.getElementById('verdictSub');

        if (passCfg.hasCriteria) {
            const pass = wpm >= passCfg.minWpm && accuracy >= passCfg.minAcc && errorPercent <= passCfg.maxErr;
            if (pass) {
                iconEl.innerHTML = '<i class="bi bi-patch-check-fill text-success" style="font-size:2.5rem;"></i>';
                textEl.textContent = 'Selamat, Lulus! 🎉';
                textEl.className = 'fw-bold mb-1 text-success';
                subEl.textContent = 'Tahap berikutnya akan terbuka.';
            } else {
                iconEl.innerHTML = '<i class="bi bi-emoji-frown text-warning" style="font-size:2.5rem;"></i>';
                textEl.textContent = 'Belum Lulus';
                textEl.className = 'fw-bold mb-1 text-warning';
                const need = [];
                if (wpm < passCfg.minWpm) need.push(`kecepatan ≥ ${passCfg.minWpm} WPM`);
                if (accuracy < passCfg.minAcc) need.push(`benar ≥ ${passCfg.minAcc}%`);
                if (errorPercent > passCfg.maxErr) need.push(`salah ≤ ${passCfg.maxErr}%`);
                subEl.textContent = 'Perlu: ' + need.join(', ') + '. Coba lagi ya!';
            }
        } else {
            iconEl.innerHTML = '<i class="bi bi-check-circle-fill text-primary" style="font-size:2.5rem;"></i>';
            textEl.textContent = 'Latihan Selesai';
            textEl.className = 'fw-bold mb-1 text-primary';
            subEl.textContent = 'Hasilmu tersimpan.';
        }

        document.getElementById('practiceCard').classList.add('d-none');
        document.getElementById('resultCard').classList.remove('d-none');
    }

    // ---- Input events ----
    input.addEventListener('keydown', function (e) {
        if (finished) { e.preventDefault(); return; }
        startTimerIfNeeded();

        if ((e.key === 'Backspace') && !allowBackspace) {
            e.preventDefault();
            return;
        }
        if (e.key === ' ' || e.code === 'Space') {
            e.preventDefault(); // spasi tidak pernah masuk ke input
            if (allowSpace && input.value.length > 0) commitWord(input.value);
            return;
        }
        if (e.key === 'Enter') {
            e.preventDefault();
            if (input.value.length > 0) commitWord(input.value);
            return;
        }
    });

    input.addEventListener('input', function () {
        if (finished) return;
        startTimerIfNeeded();

        // Bersihkan spasi yang mungkin lolos (mis. keyboard mobile)
        if (input.value.includes(' ')) {
            const parts = input.value.split(' ');
            if (allowSpace && parts[0].length > 0) {
                commitWord(parts[0]);
                return;
            }
            input.value = input.value.replace(/ /g, '');
        }

        render();

        if (!allowSpace) {
            const target = words[idx];
            if (input.value.length >= target.length) {
                commitWord(input.value.slice(0, target.length));
            }
        }
    });

    document.getElementById('finishBtn').addEventListener('click', finishNow);

    render();
    input.focus();
</script>

@endsection
