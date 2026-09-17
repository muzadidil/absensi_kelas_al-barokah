@extends('layouts.learner')

@section('title', 'Game 10 Jari')

@push('styles')
<style>
    #gameArena {
        position: relative;
        border-radius: 0.85rem;
        overflow: hidden;
        background: linear-gradient(to bottom, #0b1026 0%, #1b2350 65%, #232a5c 100%);
        user-select: none;
    }
    #sky { position: relative; height: 380px; }
    #vkeyboard {
        display: flex;
        height: 64px;
        background: rgba(0,0,0,.35);
        border-top: 2px solid rgba(255,255,255,.15);
    }
    .vkey {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cfd6ff;
        font-weight: 700;
        font-family: 'Consolas','Courier New',monospace;
        border-right: 1px solid rgba(255,255,255,.08);
        transition: background .15s, color .15s;
    }
    .vkey:last-child { border-right: none; }
    .vkey-hit  { background: #2ecc71; color: #04220f; }
    .vkey-miss { background: #e74c3c; color: #2a0505; }

    .meteor {
        position: absolute;
        top: -60px;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        transform: translateX(-50%);
        background: radial-gradient(circle at 35% 30%, #ffdd99, #ff8a3d 55%, #b23a0e 100%);
        box-shadow: 0 0 18px 4px rgba(255,138,61,.55);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2a0d00;
        font-weight: 800;
        font-family: 'Consolas','Courier New',monospace;
        font-size: 1.1rem;
        z-index: 2;
    }
    .meteor::after {
        content: '';
        position: absolute;
        top: -34px; left: 50%;
        width: 6px; height: 34px;
        transform: translateX(-50%);
        background: linear-gradient(to top, rgba(255,138,61,.65), transparent);
        border-radius: 3px;
    }
    .meteor-boom { animation: boom .15s ease-out forwards; }
    @keyframes boom { to { transform: translateX(-50%) scale(1.8); opacity: 0; } }

    #introOverlay, #gameOverOverlay {
        position: absolute; inset: 0; z-index: 5;
        background: rgba(10,14,35,.92); color: #fff;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        text-align: center; padding: 1.5rem;
    }
    .blink { animation: blink 1.1s infinite; font-weight: 700; color: #ffd76a; }
    @keyframes blink { 50% { opacity: .25; } }

    .stats-row { display: flex; gap: 2rem; margin: 1rem 0; }
    .stats-row .num { font-size: 1.7rem; font-weight: 800; }
    .stats-row .lbl { font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; color: #9aa4d6; }

    #livesBox { font-size: 1.25rem; letter-spacing: 2px; }
    .lives-lost { opacity: .35; filter: grayscale(1); }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
        <span class="badge bg-primary mb-1">Pratinjau Fase 1</span>
        <h5 class="fw-bold mb-0"><i class="bi bi-rocket-takeoff me-1"></i> Game 10 Jari</h5>
        <p class="text-muted small mb-0">Home row: <code>A S D F G H J K L ;</code> — ketik hurufnya sebelum meteor mendarat!</p>
    </div>
    <a href="{{ route('learner.dashboard') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div><span class="text-muted small me-2">Nyawa</span><span id="livesBox"></span></div>
            <div class="small text-muted">
                Hancur: <span id="destroyedCount">0</span>
                &nbsp;|&nbsp; Waktu: <span id="timeElapsed">0</span> dtk
            </div>
        </div>

        <div id="gameArena">
            <div id="introOverlay">
                <h4 class="fw-bold mb-2">Selamat datang, {{ $learner->nama_lengkap }}!</h4>
                <p class="mb-3">Apakah kamu siap menyelamatkan Al-Barokah dari Meteor?</p>
                <p class="blink mb-0">Tekan SPASI jika siap</p>
            </div>

            <div id="gameOverOverlay" class="d-none">
                <h4 class="fw-bold mb-1">💥 Markas Al-Barokah Kebobolan!</h4>
                <p class="text-muted small mb-0">Ini pratinjau Fase 1 — boss &amp; progres JILID menyusul.</p>
                <div class="stats-row">
                    <div><div class="num" id="statDestroyed">0</div><div class="lbl">Meteor Hancur</div></div>
                    <div><div class="num" id="statWpm">0</div><div class="lbl">WPM</div></div>
                    <div><div class="num" id="statAcc">0%</div><div class="lbl">Akurasi</div></div>
                </div>
                <button type="button" id="restartBtn" class="btn btn-primary px-4">
                    <i class="bi bi-arrow-repeat me-1"></i> Main Lagi
                </button>
            </div>

            <div id="sky"></div>
            <div id="vkeyboard"></div>
        </div>
    </div>
</div>

<script>
    const HOME_KEYS = ['a','s','d','f','g','h','j','k','l',';'];
    const FALL_MS = 5500;
    const SPAWN_MS = 1500;
    const START_LIVES = 5;

    const sky = document.getElementById('sky');
    const vkeyboard = document.getElementById('vkeyboard');
    const introOverlay = document.getElementById('introOverlay');
    const gameOverOverlay = document.getElementById('gameOverOverlay');
    const livesBox = document.getElementById('livesBox');
    const destroyedCountEl = document.getElementById('destroyedCount');
    const timeElapsedEl = document.getElementById('timeElapsed');

    let lives = START_LIVES;
    let destroyed = 0;
    let missed = 0;
    let started = false;
    let over = false;
    let startTime = null;
    let spawnTimer = null;
    let tickTimer = null;
    let activeMeteors = [];

    HOME_KEYS.forEach((k) => {
        const keyEl = document.createElement('div');
        keyEl.className = 'vkey';
        keyEl.dataset.key = k;
        keyEl.textContent = k.toUpperCase();
        vkeyboard.appendChild(keyEl);
    });

    function renderLives() {
        livesBox.innerHTML = '❤️'.repeat(lives) + '<span class="lives-lost">' + '🖤'.repeat(START_LIVES - lives) + '</span>';
    }
    renderLives();

    function laneLeftPercent(index) {
        return ((index + 0.5) / HOME_KEYS.length) * 100;
    }

    function flashKey(key, cls) {
        const keyEl = vkeyboard.querySelector('.vkey[data-key="' + key + '"]');
        if (!keyEl) return;
        keyEl.classList.add(cls);
        setTimeout(() => keyEl.classList.remove(cls), 180);
    }

    function spawnMeteor() {
        if (over || !started) return;
        const idx = Math.floor(Math.random() * HOME_KEYS.length);
        const key = HOME_KEYS[idx];

        const el = document.createElement('div');
        el.className = 'meteor';
        el.textContent = key.toUpperCase();
        el.style.left = laneLeftPercent(idx) + '%';
        el.style.transition = `top ${FALL_MS}ms linear`;
        sky.appendChild(el);

        requestAnimationFrame(() => { el.style.top = 'calc(100% - 6px)'; });

        const meteorObj = { el, key, hit: false, timeoutId: null };
        meteorObj.timeoutId = setTimeout(() => onMeteorLanded(meteorObj), FALL_MS);
        activeMeteors.push(meteorObj);
    }

    function removeMeteor(meteorObj) {
        activeMeteors = activeMeteors.filter(m => m !== meteorObj);
        meteorObj.el.remove();
    }

    function onMeteorLanded(meteorObj) {
        if (meteorObj.hit || over) return;
        removeMeteor(meteorObj);
        missed++;
        loseLife();
    }

    function loseLife() {
        lives = Math.max(0, lives - 1);
        renderLives();
        if (lives <= 0) endGame();
    }

    function handleCorrectHit(meteorObj) {
        meteorObj.hit = true;
        clearTimeout(meteorObj.timeoutId);
        meteorObj.el.classList.add('meteor-boom');
        flashKey(meteorObj.key, 'vkey-hit');
        setTimeout(() => removeMeteor(meteorObj), 150);
        destroyed++;
        destroyedCountEl.textContent = destroyed;
    }

    function onKeyDown(e) {
        if (!started) {
            if (e.code === 'Space') { e.preventDefault(); startGame(); }
            return;
        }
        if (over) return;

        const key = e.key.toLowerCase();
        if (!HOME_KEYS.includes(key)) return;
        e.preventDefault();

        const candidates = activeMeteors.filter(m => m.key === key && !m.hit);
        if (candidates.length === 0) {
            flashKey(key, 'vkey-miss');
            return;
        }
        candidates.sort((a, b) => b.el.getBoundingClientRect().top - a.el.getBoundingClientRect().top);
        handleCorrectHit(candidates[0]);
    }

    function startGame() {
        started = true;
        over = false;
        lives = START_LIVES;
        destroyed = 0;
        missed = 0;
        renderLives();
        destroyedCountEl.textContent = 0;
        timeElapsedEl.textContent = 0;
        introOverlay.classList.add('d-none');
        gameOverOverlay.classList.add('d-none');
        startTime = Date.now();

        spawnMeteor();
        spawnTimer = setInterval(spawnMeteor, SPAWN_MS);
        tickTimer = setInterval(() => {
            timeElapsedEl.textContent = Math.floor((Date.now() - startTime) / 1000);
        }, 250);
    }

    function endGame() {
        over = true;
        clearInterval(spawnTimer);
        clearInterval(tickTimer);
        activeMeteors.forEach(m => { clearTimeout(m.timeoutId); m.el.remove(); });
        activeMeteors = [];

        const elapsedMinutes = Math.max(1 / 60, (Date.now() - startTime) / 60000);
        const wpm = Math.round((destroyed / 5) / elapsedMinutes);
        const totalAttempt = destroyed + missed;
        const accuracy = totalAttempt > 0 ? Math.round((destroyed / totalAttempt) * 100) : 0;

        document.getElementById('statDestroyed').textContent = destroyed;
        document.getElementById('statWpm').textContent = wpm;
        document.getElementById('statAcc').textContent = accuracy + '%';

        gameOverOverlay.classList.remove('d-none');
    }

    document.getElementById('restartBtn').addEventListener('click', () => {
        gameOverOverlay.classList.add('d-none');
        introOverlay.classList.remove('d-none');
        started = false;
    });

    window.addEventListener('keydown', onKeyDown);
</script>

@endsection
