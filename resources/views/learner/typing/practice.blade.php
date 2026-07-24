@extends('layouts.learner')

@section('title', $typingLevel->name)

@section('content')

<div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
        <span class="badge bg-primary mb-1">Tahap {{ $typingLevel->level_number }}</span>
        <h5 class="fw-bold mb-0">{{ $typingLevel->name }}</h5>
        <p class="text-muted small mb-0">Tombol yang dilatih: <code>{{ strtoupper($typingLevel->allowed_keys) }}</code></p>
    </div>
    <a href="{{ route('learner.typing.index') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div id="targetText" class="fs-4 mb-4 p-3 rounded" style="font-family: monospace; letter-spacing: 2px; line-height: 2; background:#f8f9fc; word-break: break-all;"></div>

        <input type="text" id="typedInput" class="form-control form-control-lg" autocomplete="off" autocorrect="off"
            autocapitalize="off" spellcheck="false" maxlength="{{ strlen($practiceText) }}"
            placeholder="Klik di sini lalu mulai mengetik...">

        <div class="d-flex justify-content-between mt-3 small text-muted">
            <span><i class="bi bi-stopwatch me-1"></i> Waktu: <span id="timer">0</span> detik</span>
            <span><i class="bi bi-list-ol me-1"></i> Progres: <span id="progress">0</span> / {{ strlen($practiceText) }}</span>
        </div>
    </div>
</div>

<form id="resultForm" method="POST" action="{{ route('learner.typing.submit', $typingLevel->id) }}" class="d-none">
    @csrf
    <input type="hidden" name="wpm" id="wpmInput">
    <input type="hidden" name="accuracy" id="accuracyInput">
    <input type="hidden" name="duration_seconds" id="durationInput">
</form>

<script>
    const targetText = @json($practiceText);
    const targetEl = document.getElementById('targetText');
    const input = document.getElementById('typedInput');
    const progressEl = document.getElementById('progress');
    const timerEl = document.getElementById('timer');
    let startTime = null;
    let finished = false;
    let timerInterval = null;

    function renderTarget(typed) {
        let html = '';
        for (let i = 0; i < targetText.length; i++) {
            const char = targetText[i] === ' ' ? '&nbsp;' : targetText[i];
            let cls = 'text-muted';
            if (i < typed.length) {
                cls = typed[i] === targetText[i] ? 'text-success fw-bold' : 'text-danger fw-bold text-decoration-underline';
            } else if (i === typed.length) {
                cls = 'bg-warning bg-opacity-50 rounded';
            }
            html += `<span class="${cls}">${char}</span>`;
        }
        targetEl.innerHTML = html;
    }

    function finishPractice(typed) {
        finished = true;
        input.disabled = true;
        clearInterval(timerInterval);

        const durationSeconds = Math.max(1, Math.round((Date.now() - startTime) / 1000));
        let correct = 0;
        for (let i = 0; i < targetText.length; i++) {
            if (typed[i] === targetText[i]) correct++;
        }
        const accuracy = Math.round((correct / targetText.length) * 100);
        const wpm = Math.max(1, Math.round((targetText.length / 5) / (durationSeconds / 60)));

        document.getElementById('wpmInput').value = wpm;
        document.getElementById('accuracyInput').value = accuracy;
        document.getElementById('durationInput').value = durationSeconds;
        document.getElementById('resultForm').submit();
    }

    renderTarget('');

    input.addEventListener('input', function () {
        if (finished) return;

        if (!startTime) {
            startTime = Date.now();
            timerInterval = setInterval(() => {
                timerEl.textContent = Math.floor((Date.now() - startTime) / 1000);
            }, 500);
        }

        const typed = input.value;
        renderTarget(typed);
        progressEl.textContent = typed.length;

        if (typed.length >= targetText.length) {
            finishPractice(typed);
        }
    });

    input.focus();
</script>

@endsection
