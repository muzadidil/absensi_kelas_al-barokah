@extends('layouts.learner')

@section('title', $quizLevel->name)

@push('styles')
<style>
    .opt-btn {
        display: block; width: 100%; text-align: left;
        padding: 0.85rem 1.1rem; border-radius: 0.75rem;
        font-size: 1.02rem; margin-bottom: 0.6rem;
        transition: transform .06s ease;
    }
    .opt-btn:active { transform: scale(0.99); }
    .question-text { font-size: 1.3rem; font-weight: 600; line-height: 1.5; }
    .rule-tag { font-size: 0.75rem; font-weight: 600; padding: 0.25rem 0.6rem; border-radius: 999px; }
    .streak-dot { width: 12px; height: 12px; border-radius: 50%; background: #e2e6ee; display: inline-block; }
    .streak-dot.done { background: #157347; }
</style>
@endpush

@section('content')

@php
    $questionsData = $quizLevel->questions->map(fn ($q) => [
        'text' => $q->question_text,
        'explanation' => $q->explanation,
        'options' => $q->options->map(fn ($o) => ['text' => $o->option_text, 'correct' => (bool) $o->is_correct])->values(),
    ])->values();
@endphp

<div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
        <span class="badge bg-primary mb-1">Tahap {{ $quizLevel->level_number }}</span>
        <h5 class="fw-bold mb-0">{{ $quizLevel->name }}</h5>
    </div>
    <a href="{{ route('learner.quiz.index') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
        <i class="bi bi-arrow-left me-1"></i> Keluar
    </a>
</div>

<div class="mb-3">
    @if($quizLevel->reset_to_first_on_fail)
        <span class="rule-tag" style="background:rgba(220,53,69,.12);color:#b02a37;">
            <i class="bi bi-fire me-1"></i>MODE PAMUNGKAS — salah = balik ke Tahap 1, semua kunci tertutup!
        </span>
    @else
        <span class="rule-tag" style="background:rgba(13,110,253,.10);color:#0a58ca;">
            <i class="bi bi-arrow-repeat me-1"></i>Salah = ulang dari soal 1 tahap ini
        </span>
    @endif
</div>

<!-- Arena soal -->
<div class="card" id="playCard">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small"><i class="bi bi-list-ol me-1"></i> <span id="progress">Soal 1 / {{ $questionsData->count() }}</span></span>
            <span id="streak" class="d-flex gap-1"></span>
        </div>
        <div id="questionText" class="question-text mb-4"></div>
        <div id="optionsWrap"></div>
    </div>
</div>

<!-- Umpan balik salah -->
<div class="card d-none mt-3 border-danger" id="feedbackCard">
    <div class="card-body">
        <h5 class="fw-bold text-danger mb-2"><i class="bi bi-x-circle-fill me-1"></i> Salah!</h5>
        <p class="mb-2">Jawaban yang benar: <strong id="fbCorrect" class="text-success"></strong></p>
        <div id="fbExplanation" class="alert alert-info mb-3 d-none"></div>
        <button type="button" id="continueBtn" class="btn btn-primary"></button>
    </div>
</div>

<!-- Layar lulus -->
<div class="card d-none mt-3" id="successCard">
    <div class="card-body text-center">
        <i class="bi bi-patch-check-fill text-success" style="font-size:3rem;"></i>
        <h3 class="fw-bold text-success mt-2 mb-1">Lulus Tahap {{ $quizLevel->level_number }}! 🎉</h3>
        <p class="text-muted mb-4">Kamu menembus semua soal tanpa salah. Keren!</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            @if($nextLevel)
                <a href="{{ route('learner.quiz.play', $nextLevel->id) }}" class="btn btn-primary">
                    <i class="bi bi-arrow-right-circle me-1"></i> Tahap Berikutnya
                </a>
            @endif
            <a href="{{ route('learner.quiz.index') }}" class="btn btn-outline-primary">Kembali ke Daftar</a>
        </div>
    </div>
</div>

<script>
    const QUESTIONS = @json($questionsData);
    const LEVEL = { resetToFirst: @json((bool) $quizLevel->reset_to_first_on_fail) };
    const ROUTES = {
        attempt: @json(route('learner.quiz.attempt', $quizLevel->id)),
        index: @json(route('learner.quiz.index')),
        playFirst: @json($firstLevel ? route('learner.quiz.play', $firstLevel->id) : route('learner.quiz.index')),
    };
    const CSRF = @json(csrf_token());

    const playCard = document.getElementById('playCard');
    const feedbackCard = document.getElementById('feedbackCard');
    const successCard = document.getElementById('successCard');
    const optionsWrap = document.getElementById('optionsWrap');
    const questionTextEl = document.getElementById('questionText');
    const progressEl = document.getElementById('progress');
    const streakEl = document.getElementById('streak');
    const continueBtn = document.getElementById('continueBtn');

    let order = [];
    let pos = 0;
    let locked = false;

    function shuffle(a) {
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function renderStreak() {
        streakEl.innerHTML = '';
        for (let i = 0; i < order.length; i++) {
            const dot = document.createElement('span');
            dot.className = 'streak-dot' + (i < pos ? ' done' : '');
            streakEl.appendChild(dot);
        }
    }

    function startRun() {
        order = shuffle(QUESTIONS.map((_, i) => i));
        pos = 0;
        locked = false;
        feedbackCard.classList.add('d-none');
        playCard.classList.remove('d-none');
        renderQuestion();
    }

    function renderQuestion() {
        locked = false;
        const q = QUESTIONS[order[pos]];
        progressEl.textContent = `Soal ${pos + 1} / ${order.length}`;
        questionTextEl.textContent = q.text;
        renderStreak();

        const opts = shuffle(q.options.map(o => ({ ...o })));
        optionsWrap.innerHTML = '';
        opts.forEach(o => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-primary opt-btn';
            btn.textContent = o.text;
            btn.dataset.correct = o.correct ? '1' : '0';
            btn.addEventListener('click', () => answer(o.correct, btn, q));
            optionsWrap.appendChild(btn);
        });
    }

    async function answer(isCorrect, btn, q) {
        if (locked) return;
        locked = true;
        optionsWrap.querySelectorAll('.opt-btn').forEach(b => b.disabled = true);

        if (isCorrect) {
            btn.classList.replace('btn-outline-primary', 'btn-success');
            pos++;
            renderStreak();
            if (pos >= order.length) {
                await recordAttempt(true, order.length, order.length);
                showSuccess();
            } else {
                setTimeout(renderQuestion, 450);
            }
        } else {
            btn.classList.replace('btn-outline-primary', 'btn-danger');
            const correctBtn = optionsWrap.querySelector('.opt-btn[data-correct="1"]');
            if (correctBtn) correctBtn.classList.replace('btn-outline-primary', 'btn-success');
            await recordAttempt(false, pos, order.length);
            showFailure(q);
        }
    }

    function showSuccess() {
        playCard.classList.add('d-none');
        feedbackCard.classList.add('d-none');
        successCard.classList.remove('d-none');
    }

    function showFailure(q) {
        const correct = q.options.find(o => o.correct);
        document.getElementById('fbCorrect').textContent = correct ? correct.text : '';
        const exp = document.getElementById('fbExplanation');
        if (q.explanation) {
            exp.innerHTML = '<i class="bi bi-lightbulb me-1"></i>' + q.explanation;
            exp.classList.remove('d-none');
        } else {
            exp.classList.add('d-none');
        }
        continueBtn.textContent = LEVEL.resetToFirst ? '⟲ Balik ke Tahap 1' : '⟲ Ulang dari Soal 1';
        feedbackCard.classList.remove('d-none');
        feedbackCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    continueBtn.addEventListener('click', () => {
        if (LEVEL.resetToFirst) {
            window.location.href = ROUTES.playFirst;
        } else {
            startRun();
            playCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });

    async function recordAttempt(passed, cleared, total) {
        try {
            await fetch(ROUTES.attempt, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ passed, questions_cleared: cleared, total_questions: total }),
            });
        } catch (e) {
            // Abaikan error jaringan — permainan tetap lanjut.
        }
    }

    startRun();
</script>

@endsection
