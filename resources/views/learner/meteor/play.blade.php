@extends('layouts.learner')

@section('title', 'Game 10 Jari — ' . $meteorGameLevel->display_label)

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
<style>
    .meteor-page .arena-wrap {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        background: #05071a;
        box-shadow: 0 10px 30px rgba(8, 12, 40, .35);
        touch-action: manipulation;
    }
    .meteor-page canvas {
        display: block;
        width: 100%;
        height: auto;
    }

    .meteor-page .overlay {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 1.25rem;
        color: #eef1ff;
        background: radial-gradient(circle at 50% 40%, rgba(22, 30, 70, .82), rgba(5, 7, 26, .95));
        backdrop-filter: blur(2px);
    }
    .meteor-page .overlay h4 { font-weight: 800; letter-spacing: -.01em; }
    .meteor-page .overlay p { color: #b9c2ea; }
    .meteor-page .hint-key {
        display: inline-block;
        border: 1px solid rgba(255, 215, 106, .55);
        border-radius: .45rem;
        padding: .1rem .5rem;
        font-family: ui-monospace, Consolas, monospace;
        font-weight: 700;
        color: #ffd76a;
    }
    .meteor-page .soft-key {
        position: absolute;
        bottom: 6px;
        left: 50%;
        width: 1px;
        height: 1px;
        padding: 0;
        border: 0;
        opacity: 0;
        pointer-events: none;
    }
    .meteor-page .blink { animation: meteorBlink 1.2s ease-in-out infinite; }
    @keyframes meteorBlink { 50% { opacity: .3; } }

    .meteor-page .hud {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .4rem .75rem;
        margin-bottom: .6rem;
    }

    /* Saat layar penuh, HUD dipindah ke dalam arena supaya tetap kelihatan. */
    .meteor-page .arena-wrap:fullscreen { border-radius: 0; box-shadow: none; }
    .meteor-page .hud.hud-float {
        position: absolute;
        top: 0; left: 0; right: 0;
        z-index: 4;
        margin: 0;
        padding: .6rem .9rem;
        background: linear-gradient(to bottom, rgba(6, 9, 26, .72), rgba(6, 9, 26, 0));
    }
    .meteor-page .hud.hud-float .chip {
        background: rgba(255, 255, 255, .92);
        border-color: rgba(255, 255, 255, .4);
    }
    .meteor-page .chip {
        display: inline-flex;
        align-items: baseline;
        gap: .35rem;
        background: #f4f6fb;
        border: 1px solid #e3e8f3;
        border-radius: 999px;
        padding: .2rem .7rem;
        font-size: .78rem;
        color: #6b7490;
        white-space: nowrap;
    }
    .meteor-page .chip b {
        font-size: .95rem;
        color: #1f2640;
        font-variant-numeric: tabular-nums;
    }
    .meteor-page .lives { display: inline-flex; gap: .12rem; line-height: 1; }
    .meteor-page .lives i { color: #e8455f; font-size: .95rem; }
    .meteor-page .lives i.gone { color: #ccd3e4; }

    .meteor-page .result-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(88px, 1fr));
        gap: .5rem 1.5rem;
        margin: 1rem 0 1.25rem;
    }
    @media (min-width: 576px) {
        .meteor-page .result-grid { grid-template-columns: repeat(4, minmax(84px, 1fr)); }
    }
    .meteor-page .result-grid .num { font-size: 1.6rem; font-weight: 800; line-height: 1.1; }
    .meteor-page .result-grid .lbl {
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #9aa4d6;
    }
</style>
@endpush

@section('content')
<div class="meteor-page">

    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
        <div>
            <span class="badge bg-primary mb-1">{{ $meteorGameLevel->display_label }}</span>
            <h5 class="fw-bold mb-1"><i class="bi bi-rocket-takeoff me-1"></i> Boss: {{ $meteorGameLevel->boss?->name ?? '—' }}</h5>
            <p class="text-muted small mb-0">
                Hancurkan {{ $meteorGameLevel->wave_target }} meteor, lalu lawan
                {{ $meteorGameLevel->boss?->name }} dan senjata {{ $meteorGameLevel->bullet?->name }}-nya.
                Huruf: <code>{{ strtoupper(implode(' ', str_split($meteorGameLevel->allowed_keys))) }}</code>
            </p>
            @if($meteorGameLevel->bullet_returns)
                <p class="small text-primary mb-0 mt-1">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Peluru yang kamu ketik <strong>memantul balik</strong> dan meledak di tubuh bossnya.
                </p>
            @endif
        </div>
        <a href="{{ route('learner.meteor.index') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
            <i class="bi bi-arrow-left me-1"></i> Daftar JILID
        </a>
    </div>

    <div id="touchNotice" class="alert alert-warning py-2 small d-none">
        <i class="bi bi-keyboard me-1"></i>
        Game ini dirancang untuk <strong>keyboard fisik</strong> — latihan 10 jari tidak bisa dilakukan di layar sentuh.
        Untuk sekadar mencoba, tekan <strong>Mulai</strong> lalu <strong>sentuh arenanya</strong> supaya keyboard layar muncul.
    </div>

    <div class="card">
        <div class="card-body">

            <div class="hud" id="hudBar">
                <span class="chip">Nyawa <b class="lives" id="hudLives"></b></span>
                <span class="chip" id="chipProgress">Meteor <b id="hudProgress">0</b></span>
                <span class="chip">Kombo <b id="hudCombo">—</b></span>
                <span class="chip">Akurasi <b id="hudAcc">100%</b></span>
                <span class="chip">WPM <b id="hudWpm">0</b></span>
                <span class="chip">Waktu <b id="hudTime">0</b>dtk</span>
                <button type="button" id="fsBtn" class="btn btn-outline-primary btn-sm ms-auto">
                    <i class="bi bi-arrows-fullscreen"></i> Layar Penuh
                </button>
                <button type="button" id="pauseBtn" class="btn btn-outline-secondary btn-sm d-none">
                    <i class="bi bi-pause-fill"></i> Jeda
                </button>
            </div>

            <div class="arena-wrap" id="meteorArena">
                <canvas id="meteorCanvas"></canvas>

                <div class="overlay" id="introOverlay">
                    <h4 class="mb-2">Selamat datang, {{ $learner->nama_lengkap }}!</h4>
                    <p class="mb-3">Apakah kamu siap menyelamatkan Al-Barokah dari Meteor?</p>
                    <button type="button" id="startBtn" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-play-fill me-1"></i> Mulai
                    </button>
                    <p class="blink small mt-3 mb-0">atau tekan <span class="hint-key">SPASI</span></p>
                </div>

                <div class="overlay d-none" id="pauseOverlay">
                    <h4 class="mb-3"><i class="bi bi-pause-circle me-1"></i> Jeda</h4>
                    <button type="button" id="resumeBtn" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-play-fill me-1"></i> Lanjut
                    </button>
                    <p class="small mt-3 mb-0">atau tekan <span class="hint-key">SPASI</span></p>
                </div>

                {{-- Di HP tidak ada keyboard fisik: kotak tak terlihat ini yang dipakai
                     untuk memunculkan keyboard layar saat arena disentuh. --}}
                <input id="softKey" class="soft-key" type="text" inputmode="text"
                       autocomplete="off" autocorrect="off" autocapitalize="off"
                       spellcheck="false" tabindex="-1" aria-hidden="true">

                <div class="overlay d-none" id="wonOverlay">
                    <h4 class="mb-1">{{ $meteorGameLevel->boss?->name ?? 'Boss' }} tumbang!</h4>
                    <p class="small mb-0">{{ $meteorGameLevel->display_label }} tembus. Al-Barokah selamat.</p>

                    <div class="result-grid">
                        <div><div class="num" id="wonDestroyed">0</div><div class="lbl">Dihancurkan</div></div>
                        <div><div class="num" id="wonWpm">0</div><div class="lbl">WPM</div></div>
                        <div><div class="num" id="wonAcc">0%</div><div class="lbl">Akurasi</div></div>
                        <div><div class="num" id="wonLives">0</div><div class="lbl">Sisa Nyawa</div></div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @if($nextLevel)
                            <a href="{{ route('learner.meteor.play', $nextLevel->id) }}"
                               id="nextLevelBtn" class="btn btn-primary px-4">
                                Lanjut {{ $nextLevel->display_label }} <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @endif
                        <button type="button" id="replayBtn" class="btn btn-outline-light px-4">
                            <i class="bi bi-arrow-repeat me-1"></i> Ulangi
                        </button>
                        <a href="{{ route('learner.meteor.index') }}" class="btn btn-outline-light px-4">Daftar JILID</a>
                    </div>
                    @if($nextLevel)
                        <p class="small mt-3 mb-0">atau tekan <span class="hint-key">SPASI</span> untuk lanjut</p>
                    @endif
                </div>

                <div class="overlay d-none" id="lostOverlay">
                    <h4 class="mb-1">Markas Al-Barokah kebobolan!</h4>
                    <p class="small mb-0" id="lostSubtitle"></p>

                    <div class="result-grid">
                        <div><div class="num" id="lostDestroyed">0</div><div class="lbl">Dihancurkan</div></div>
                        <div><div class="num" id="lostWpm">0</div><div class="lbl">WPM</div></div>
                        <div><div class="num" id="lostAcc">0%</div><div class="lbl">Akurasi</div></div>
                        <div><div class="num" id="lostCombo">0</div><div class="lbl">Kombo Terbaik</div></div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <button type="button" id="retryBtn" class="btn btn-primary px-4">
                            <i class="bi bi-arrow-repeat me-1"></i> Coba Lagi
                        </button>
                        <a href="{{ route('learner.meteor.index') }}" class="btn btn-outline-light px-4">Daftar JILID</a>
                    </div>
                    <p class="small mt-3 mb-0">atau tekan <span class="hint-key">SPASI</span> untuk coba lagi</p>
                    <p class="small mt-2 mb-0" id="lostResetNote"></p>
                </div>
            </div>

            <p class="text-muted small mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Kecepatan jatuh tetap — yang bertambah adalah <em>jumlah</em>-nya.
                Salah tekan tidak mengurangi nyawa, tapi kombo hangus.
                Tekan <span class="hint-key">Esc</span> untuk jeda.
            </p>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // Semuanya datang dari database (menu Game 10 Jari di admin) — tidak ada lagi
    // angka kesulitan atau warna yang dipatok di dalam berkas ini.
    var LEVEL = @json($meteorGameLevel->gameConfig());

    var ATTEMPT_URL = @json(route('learner.meteor.attempt', $meteorGameLevel->id));
    var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var FALL_SECONDS = LEVEL.fallSeconds;
    var BULLET_SECONDS = LEVEL.bulletSeconds;
    var BOSS_SHOT_GAP = LEVEL.bossShotGap;
    var RETURN_SPEED = 820;      // laju peluru yang memantul balik ke boss
    var BOSS_INTRO = 2.2;
    var TAU = Math.PI * 2;

    var canvas = document.getElementById('meteorCanvas');
    var wrap   = document.getElementById('meteorArena');
    if (!canvas || !wrap) return;
    var ctx = canvas.getContext('2d');

    var introOverlay = document.getElementById('introOverlay');
    var pauseOverlay = document.getElementById('pauseOverlay');
    var wonOverlay   = document.getElementById('wonOverlay');
    var lostOverlay  = document.getElementById('lostOverlay');
    var pauseBtn     = document.getElementById('pauseBtn');

    var hud = {
        lives:    document.getElementById('hudLives'),
        progress: document.getElementById('hudProgress'),
        chip:     document.getElementById('chipProgress'),
        combo:    document.getElementById('hudCombo'),
        acc:      document.getElementById('hudAcc'),
        wpm:      document.getElementById('hudWpm'),
        time:     document.getElementById('hudTime')
    };

    var W = 0, H = 0, groundY = 0;
    var state = 'intro';   // intro | playing | paused | won | lost
    var phase = 'wave';    // wave | bossIntro | boss
    var lives, combo, bestCombo, destroyed, waveCleared, wrongKeys, missed;
    var elapsed, clock, spawnAcc, phaseTimer;
    var falling, particles, beams, stars, clouds;
    var TH = LEVEL.theme;
    var isTerang = !TH.isDark;          // latar terang: teks & kilatan perlu dibalik kontrasnya
    var boss, bossHp, bossTimer, bossCharge;
    var shake, hurt, wrongFlash, comboPop;
    var lastFrame = 0, hudTimer = 0, submitted = false;
    var lastChar = '', lastCharAt = -1e9, finishedAt = -1e9;
    var nextLevelBtn = document.getElementById('nextLevelBtn');   // null di JILID terakhir
    function now() { return window.performance ? performance.now() : Date.now(); }
    var isTouch = !!(window.matchMedia && window.matchMedia('(pointer: coarse)').matches);
    var softKey = document.getElementById('softKey');

    // ---------- ukuran & bintang ----------

    function buildSky() {
        stars = [];
        for (var i = 0; i < 110; i++) {
            stars.push({
                x: Math.random(),
                y: Math.random(),
                r: Math.random() < 0.82 ? 0.7 + Math.random() * 0.7 : 1.4 + Math.random() * 0.9,
                drift: 0.004 + Math.random() * 0.012,
                phase: Math.random() * TAU
            });
        }
        clouds = [];
        for (var c = 0; c < 7; c++) {
            clouds.push({
                x: Math.random(),
                y: 0.06 + Math.random() * 0.46,
                s: 0.55 + Math.random() * 0.95,
                drift: 0.006 + Math.random() * 0.014,
                a: 0.45 + Math.random() * 0.4
            });
        }
    }

    function isFullscreen() {
        return (document.fullscreenElement || document.webkitFullscreenElement) === wrap;
    }

    function resize() {
        // Saat layar penuh arena mengikuti ukuran layar; di luar itu tingginya dibatasi
        // supaya kartu di halaman tidak kepanjangan.
        var cssW = Math.max(280, Math.round(wrap.clientWidth));
        var cssH = isFullscreen()
            ? Math.max(260, Math.round(wrap.clientHeight))
            : Math.round(Math.min(560, Math.max(330, cssW * 0.58)));
        var dpr  = Math.min(2, window.devicePixelRatio || 1);
        var pxW  = Math.round(cssW * dpr);
        var pxH  = Math.round(cssH * dpr);

        // menugaskan canvas.width mengosongkan kanvas, jadi lakukan hanya saat ukurannya berubah
        if (W === cssW && H === cssH && canvas.width === pxW && canvas.height === pxH) return;

        var sx = W > 0 ? cssW / W : 1;
        var sy = H > 0 ? cssH / H : 1;

        W = cssW;
        H = cssH;
        groundY = H - Math.max(50, Math.min(120, Math.round(H * 0.12)));

        canvas.width  = pxW;
        canvas.height = pxH;
        canvas.style.height = cssH + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        if (falling) {
            for (var i = 0; i < falling.length; i++) {
                var f = falling[i];
                f.x *= sx;
                f.y *= sy;
                f.trail.length = 0;
                f.speed = travelSpeed(f);
            }
        }
        if (boss) { boss.x *= sx; boss.y = H * 0.19; }
    }

    function travelSpeed(f) {
        var secs = f.kind === 'bullet' ? BULLET_SECONDS : FALL_SECONDS;
        return ((groundY - f.spawnY) / secs) * f.speedVar;
    }

    // ---------- siklus permainan ----------

    function reset() {
        lives = LEVEL.lives;
        combo = 0; bestCombo = 0;
        destroyed = 0; waveCleared = 0; wrongKeys = 0; missed = 0;
        elapsed = 0; clock = 0; spawnAcc = 0; phaseTimer = 0;
        falling = []; particles = []; beams = [];
        boss = null; bossHp = LEVEL.bossHp; bossTimer = 0; bossCharge = 0;
        shake = 0; hurt = 0; wrongFlash = 0; comboPop = 0;
        phase = 'wave';
        submitted = false;
        syncHud();
    }

    function accuracy() {
        var total = destroyed + wrongKeys;
        return total === 0 ? 100 : Math.round((destroyed / total) * 100);
    }

    function wpm() {
        if (elapsed < 1) return 0;
        return Math.round((destroyed / 5) / (elapsed / 60));
    }

    function multiplier() {
        return 1 + Math.min(4, Math.floor(combo / 5));
    }

    function syncHud() {
        hud.lives.innerHTML =
            '<i class="bi bi-heart-fill"></i>'.repeat(lives) +
            '<i class="bi bi-heart gone"></i>'.repeat(Math.max(0, LEVEL.lives - lives));
        if (phase === 'wave') {
            hud.chip.firstChild.nodeValue = 'Meteor ';
            hud.progress.textContent = waveCleared + '/' + LEVEL.waveTarget;
        } else {
            hud.chip.firstChild.nodeValue = 'Boss ';
            hud.progress.textContent = bossHp + '/' + LEVEL.bossHp;
        }
        hud.acc.textContent = accuracy() + '%';
        hud.combo.textContent = combo >= 2 ? combo + '× (skor ×' + multiplier() + ')' : '—';
    }

    function show(el) { el.classList.remove('d-none'); }
    function hide(el) { el.classList.add('d-none'); }

    function startGame() {
        reset();
        state = 'playing';
        hide(introOverlay); hide(pauseOverlay); hide(wonOverlay); hide(lostOverlay);
        show(pauseBtn);
        spawnMeteor();
    }

    function setPaused(on) {
        if (state !== 'playing' && state !== 'paused') return;
        state = on ? 'paused' : 'playing';
        if (on) { show(pauseOverlay); } else { hide(pauseOverlay); }
        pauseBtn.innerHTML = on
            ? '<i class="bi bi-play-fill"></i> Lanjut'
            : '<i class="bi bi-pause-fill"></i> Jeda';
    }

    function finish(won) {
        state = won ? 'won' : 'lost';
        finishedAt = now();
        hide(pauseBtn);

        if (won) {
            document.getElementById('wonDestroyed').textContent = destroyed;
            document.getElementById('wonWpm').textContent = wpm();
            document.getElementById('wonAcc').textContent = accuracy() + '%';
            document.getElementById('wonLives').textContent = lives;
            show(wonOverlay);
        } else {
            document.getElementById('lostDestroyed').textContent = destroyed;
            document.getElementById('lostWpm').textContent = wpm();
            document.getElementById('lostAcc').textContent = accuracy() + '%';
            document.getElementById('lostCombo').textContent = bestCombo;
            document.getElementById('lostSubtitle').textContent = phase === 'wave'
                ? 'Belum sempat ketemu ' + LEVEL.bossName + ' — ' + waveCleared + ' dari ' + LEVEL.waveTarget + ' meteor.'
                : LEVEL.bossName + ' masih berdiri dengan ' + bossHp + ' nyawa tersisa.';
            document.getElementById('lostResetNote').textContent = '';
            show(lostOverlay);
        }

        submitAttempt(won);
    }

    function submitAttempt(won) {
        if (submitted) return;
        submitted = true;

        fetch(ATTEMPT_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({
                passed: won,
                reached_boss: phase !== 'wave',
                meteors_destroyed: destroyed,
                wpm: wpm(),
                accuracy: accuracy()
            })
        }).then(function (r) {
            return r.ok ? r.json() : null;
        }).then(function (res) {
            if (res && res.reset_to_start) {
                document.getElementById('lostResetNote').textContent =
                    'Progresmu diulang dari ' + res.restart_label + '.';
            }
        }).catch(function () {
            /* hasil gagal terkirim — permainan tetap bisa diulang, biarkan senyap */
        });
    }

    // ---------- objek jatuh ----------

    function pickChar() {
        var onScreen = falling.filter(function (f) { return !f.returning; })
                              .map(function (f) { return f.char; });
        var pool = LEVEL.keys.filter(function (c) { return onScreen.indexOf(c) === -1; });
        if (pool.length === 0) pool = LEVEL.keys;
        return pool[(Math.random() * pool.length) | 0];
    }

    function spreadX(r) {
        var margin = r + 12;
        var span = Math.max(1, W - margin * 2);
        var x = margin + Math.random() * span;
        if (falling.length === 0) return x;

        // best-candidate sampling: sebar horizontal supaya tidak menumpuk
        var bestGap = -1;
        for (var i = 0; i < 12; i++) {
            var cand = margin + Math.random() * span;
            var nearest = Infinity;
            for (var j = 0; j < falling.length; j++) {
                nearest = Math.min(nearest, Math.abs(falling[j].x - cand));
            }
            if (nearest > bestGap) { bestGap = nearest; x = cand; }
        }
        return x;
    }

    function push(kind, x, y, r) {
        var f = {
            kind: kind,
            char: pickChar(),
            x: x, y: y, r: r,
            spawnY: y,
            vx: (Math.random() - 0.5) * 26,
            speedVar: 0.92 + Math.random() * 0.16,
            speed: 0,
            angle: Math.random() * TAU,
            spin: (Math.random() - 0.5) * 1.8,
            trail: []
        };
        f.speed = travelSpeed(f);
        falling.push(f);
    }

    // di arena besar (layar penuh) huruf ikut dibesarkan supaya tetap enak dibaca
    function sizeScale() {
        return Math.max(1, Math.min(1.7, W / 900));
    }

    function spawnMeteor() {
        var r = (21 + Math.random() * 8) * sizeScale();
        push('meteor', spreadX(r), -r - 10, r);
    }

    function bossVolley() {
        var n = LEVEL.bullets;
        for (var i = 0; i < n; i++) {
            var r = (17 + Math.random() * 5) * sizeScale();
            var x = spreadX(r);
            push('bullet', x, boss.y + 26, r);
        }
        var fx = LEVEL.bulletEffect;
        burst(boss.x, boss.y + 26, Math.round(fx.particles * 0.8), Math.round(fx.spread * 0.8), false, fx.hue);
        shake = Math.max(shake, fx.shake + 2);
    }

    function burst(x, y, amount, spread, upward, hue) {
        for (var i = 0; i < amount; i++) {
            var a = upward ? -Math.PI / 2 + (Math.random() - 0.5) * 2.2 : Math.random() * TAU;
            var sp = 40 + Math.random() * spread;
            particles.push({
                x: x, y: y,
                vx: Math.cos(a) * sp,
                vy: Math.sin(a) * sp,
                life: 0.45 + Math.random() * 0.45,
                age: 0,
                size: 1.5 + Math.random() * 2.8,
                hue: (hue === undefined ? 18 : hue) + Math.random() * 30
            });
        }
    }

    function hitFalling(f) {
        var fx = fxFor(f.kind);
        destroyed++;
        combo++;
        if (combo > bestCombo) bestCombo = combo;
        if (combo >= 2) comboPop = 1;
        beams.push({ x: f.x, y: f.y, age: 0, life: 0.15, color: fx.beam });
        shake = Math.max(shake, fx.shake);

        // JILID ber-bullet_returns: peluru tidak hancur di tempat, tapi memantul
        // balik ke bossnya dan baru meledak di sana.
        if (f.kind === 'bullet' && LEVEL.returns && boss) {
            f.returning = true;
            f.trail.length = 0;
            burst(f.x, f.y, Math.round(fx.particles * 0.5), Math.round(fx.spread * 0.6), false, fx.hue);
            syncHud();
            return;
        }

        falling.splice(falling.indexOf(f), 1);
        burst(f.x, f.y, fx.particles, fx.spread, false, fx.hue);

        if (f.kind === 'bullet') {
            damageBoss(f.x, f.y);
        } else if (phase === 'wave') {
            waveCleared++;
            if (waveCleared >= LEVEL.waveTarget) {
                phase = 'bossIntro';
                phaseTimer = BOSS_INTRO;
                falling.length = 0;
            }
        }
        if (state === 'playing') syncHud();
    }

    function damageBoss(x, y) {
        var fx = LEVEL.bulletEffect;
        bossHp = Math.max(0, bossHp - 1);
        boss.flash = 1;
        shake = Math.max(shake, fx.shake * 2);
        burst(x, y, Math.round(fx.particles * 1.1), Math.round(fx.spread * 1.15), false, fx.hue);
        syncHud();
        if (bossHp <= 0) finish(true);
    }

    function landed(f) {
        missed++;
        combo = 0;
        lives = Math.max(0, lives - 1);
        shake = 16;
        hurt = 1;
        var fx = fxFor(f.kind);
        burst(f.x, groundY, Math.round(fx.particles * 1.4), Math.round(fx.spread * 1.25), true, fx.hue);
        syncHud();
        if (lives <= 0) finish(false);
    }

    function wrongKey() {
        wrongKeys++;
        combo = 0;
        wrongFlash = 1;
        syncHud();
    }

    // ---------- update ----------

    function update(dt) {
        clock += dt;
        elapsed += dt;

        if (phase === 'wave') {
            spawnAcc += dt;
            if (spawnAcc >= LEVEL.spawnMs / 1000) { spawnAcc = 0; spawnMeteor(); }
        } else if (phase === 'bossIntro') {
            phaseTimer -= dt;
            if (phaseTimer <= 0) {
                phase = 'boss';
                boss = { x: W / 2, y: H * 0.19, dir: Math.random() < 0.5 ? -1 : 1, flash: 0 };
                bossTimer = 1.4;
                syncHud();
            }
        } else if (phase === 'boss' && boss) {
            boss.x += boss.dir * 46 * dt;
            if (boss.x < 70) { boss.x = 70; boss.dir = 1; }
            if (boss.x > W - 70) { boss.x = W - 70; boss.dir = -1; }
            boss.flash = Math.max(0, boss.flash - dt * 3);

            bossTimer -= dt;
            bossCharge = bossTimer < 0.55 ? (0.55 - bossTimer) / 0.55 : 0;
            if (bossTimer <= 0) { bossVolley(); bossTimer = BOSS_SHOT_GAP; bossCharge = 0; }
        }

        for (var i = falling.length - 1; i >= 0; i--) {
            var f = falling[i];

            if (f.returning) {
                if (!boss) { falling.splice(i, 1); continue; }
                var dx = boss.x - f.x, dy = boss.y - f.y;
                var dist = Math.sqrt(dx * dx + dy * dy) || 1;
                f.x += (dx / dist) * RETURN_SPEED * dt;
                f.y += (dy / dist) * RETURN_SPEED * dt;
                f.angle += 9 * dt;
                f.trail.unshift({ x: f.x, y: f.y });
                if (f.trail.length > 12) f.trail.pop();
                if (dist < 46) {
                    falling.splice(i, 1);
                    damageBoss(f.x, f.y);
                    if (state !== 'playing') return;
                }
                continue;
            }

            f.y += f.speed * dt;
            f.x += f.vx * dt;
            f.angle += f.spin * dt;

            if (f.x < f.r)     { f.x = f.r;     f.vx =  Math.abs(f.vx); }
            if (f.x > W - f.r) { f.x = W - f.r; f.vx = -Math.abs(f.vx); }

            f.trail.unshift({ x: f.x, y: f.y });
            if (f.trail.length > 9) f.trail.pop();

            if (f.y >= groundY) {
                falling.splice(i, 1);
                landed(f);
                if (state !== 'playing') return;
            }
        }

        for (var p = particles.length - 1; p >= 0; p--) {
            var q = particles[p];
            q.age += dt;
            if (q.age >= q.life) { particles.splice(p, 1); continue; }
            q.x += q.vx * dt;
            q.y += q.vy * dt;
            q.vy += 210 * dt;
            q.vx *= 0.98;
        }

        for (var b = beams.length - 1; b >= 0; b--) {
            beams[b].age += dt;
            if (beams[b].age >= beams[b].life) beams.splice(b, 1);
        }

        if (TH.skyObject === 'awan') {
            for (var c = 0; c < clouds.length; c++) {
                clouds[c].x += clouds[c].drift * dt;
                if (clouds[c].x > 1.2) { clouds[c].x = -0.2; clouds[c].y = 0.06 + Math.random() * 0.46; }
            }
        } else {
            for (var s = 0; s < stars.length; s++) {
                stars[s].y += stars[s].drift * dt;
                if (stars[s].y > 1) { stars[s].y -= 1; stars[s].x = Math.random(); }
            }
        }

        shake      = Math.max(0, shake - dt * 42);
        hurt       = Math.max(0, hurt - dt * 1.8);
        wrongFlash = Math.max(0, wrongFlash - dt * 4.2);
        comboPop   = Math.max(0, comboPop - dt * 2.4);
    }

    // ---------- gambar: latar ----------

    function drawSky() {
        var g = ctx.createLinearGradient(0, 0, 0, H);
        g.addColorStop(0, TH.skyTop);
        g.addColorStop(isTerang ? 0.42 : 0.55, TH.skyMid);
        g.addColorStop(1, TH.skyBottom);
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, W, H);

        if (TH.skyObject === 'awan') {
            var sx = W * 0.78, sy = groundY - 76;
            var sun = ctx.createRadialGradient(sx, sy, 0, sx, sy, 175);
            sun.addColorStop(0, hexToRgba(TH.accent, 0.78));
            sun.addColorStop(0.32, hexToRgba(TH.accent, 0.3));
            sun.addColorStop(1, hexToRgba(TH.accent, 0));
            ctx.fillStyle = sun;
            ctx.beginPath(); ctx.arc(sx, sy, 175, 0, TAU); ctx.fill();
            ctx.fillStyle = hexToRgba(TH.accent, 0.95);
            ctx.beginPath(); ctx.arc(sx, sy, 30, 0, TAU); ctx.fill();
        } else if (TH.skyObject === 'bintang') {
            var neb = ctx.createRadialGradient(W * 0.22, H * 0.28, 0, W * 0.22, H * 0.28, W * 0.42);
            neb.addColorStop(0, 'rgba(96, 84, 200, .22)');
            neb.addColorStop(1, 'rgba(96, 84, 200, 0)');
            ctx.fillStyle = neb;
            ctx.fillRect(0, 0, W, H);
        }
    }

    function puff(x, y, r) { ctx.beginPath(); ctx.arc(x, y, r, 0, TAU); ctx.fill(); }

    function drawSkyDetail() {
        if (TH.skyObject === 'tidak_ada') return;
        if (TH.skyObject === 'awan') {
            for (var c = 0; c < clouds.length; c++) {
                var k = clouds[c];
                var x = k.x * W, y = k.y * groundY, s = k.s;
                ctx.fillStyle = 'rgba(255, 255, 255, ' + k.a.toFixed(2) + ')';
                puff(x, y, 32 * s);
                puff(x + 30 * s, y + 7 * s, 25 * s);
                puff(x - 31 * s, y + 9 * s, 21 * s);
                puff(x + 7 * s, y - 15 * s, 23 * s);
            }
            return;
        }
        for (var i = 0; i < stars.length; i++) {
            var st = stars[i];
            ctx.globalAlpha = 0.35 + 0.45 * (0.5 + 0.5 * Math.sin(clock * 1.6 + st.phase));
            ctx.fillStyle = '#dfe6ff';
            ctx.beginPath();
            ctx.arc(st.x * W, st.y * groundY, st.r, 0, TAU);
            ctx.fill();
        }
        ctx.globalAlpha = 1;
    }

    function drawBase() {
        var cx = W / 2;
        var y  = groundY;
        var s  = Math.max(0.8, Math.min(2.2, W / 640));
        var ratio = lives / LEVEL.lives;

        var glow = ctx.createLinearGradient(0, y - 90 * s, 0, y);
        glow.addColorStop(0, hexToRgba(TH.accent, 0));
        glow.addColorStop(1, hexToRgba(TH.accent, 0.09 + 0.19 * ratio));
        ctx.fillStyle = glow;
        ctx.fillRect(0, y - 90 * s, W, 90 * s);

        var ground = ctx.createLinearGradient(0, y, 0, H);
        ground.addColorStop(0, TH.groundTop);
        ground.addColorStop(1, TH.groundBottom);
        ctx.fillStyle = ground;
        ctx.fillRect(0, y, W, H - y);

        var domeR = 32 * s, hallW = 68 * s, hallH = 26 * s, towerX = 96 * s, towerH = 52 * s;

        ctx.fillStyle = TH.wall;
        ctx.beginPath(); ctx.rect(cx - hallW, y - hallH, hallW * 2, hallH); ctx.fill();
        [-towerX, towerX].forEach(function (dx) {
            ctx.beginPath(); ctx.rect(cx + dx - 7 * s, y - towerH, 14 * s, towerH); ctx.fill();
        });

        ctx.fillStyle = TH.dome;
        ctx.beginPath(); ctx.arc(cx, y - hallH, domeR, Math.PI, TAU); ctx.fill();
        ctx.beginPath(); ctx.rect(cx - 1.5 * s, y - hallH - domeR - 12 * s, 3 * s, 12 * s); ctx.fill();
        [-towerX, towerX].forEach(function (dx) {
            ctx.beginPath(); ctx.arc(cx + dx, y - towerH, 8 * s, Math.PI, TAU); ctx.fill();
        });

        ctx.fillStyle = isTerang
            ? 'rgba(84, 56, 30, ' + (0.35 + 0.35 * ratio).toFixed(3) + ')'
            : 'rgba(255, 198, 106, ' + (0.25 + 0.45 * ratio).toFixed(3) + ')';
        for (var i = -2; i <= 2; i++) {
            ctx.fillRect(cx + i * 24 * s - 3 * s, y - hallH * 0.68, 6 * s, 9 * s);
        }

        ctx.strokeStyle = hexToRgba(TH.accent, 0.22 + 0.5 * ratio);
        ctx.lineWidth = 2;
        ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(W, y); ctx.stroke();
    }

    // ---------- gambar: boss ----------

    var BOSS_PAINTERS = {
        pocong: drawPocong, wewe: drawWewe, genderuwo: drawGenderuwo,
        kelelawar: drawKelelawar, ufo: drawUfo, drone: drawDrone,
        mecha: drawMecha, satelit: drawSatelit, kapal: drawKapal, inti_ai: drawIntiAi
    };

    function bossHue() { return LEVEL.bossHue; }
    function bossBulletColors() { return LEVEL.bulletColors; }
    function fxFor(kind) { return kind === 'bullet' ? LEVEL.bulletEffect : LEVEL.meteorEffect; }

    function drawBoss() {
        if (!boss) return;
        var bob = Math.sin(clock * 2.2) * 7;
        var s = Math.max(0.85, Math.min(1.9, W / 700));

        ctx.save();
        ctx.translate(boss.x, boss.y + bob);
        ctx.scale(s, s);

        if (bossCharge > 0) {
            var ring = ctx.createRadialGradient(0, 0, 10, 0, 0, 90);
            ring.addColorStop(0, 'hsla(' + bossHue() + ', 100%, 65%, ' + (bossCharge * 0.5).toFixed(3) + ')');
            ring.addColorStop(1, 'hsla(' + bossHue() + ', 100%, 55%, 0)');
            ctx.fillStyle = ring;
            ctx.beginPath(); ctx.arc(0, 0, 90, 0, TAU); ctx.fill();
        }

        (BOSS_PAINTERS[LEVEL.bossSprite] || drawPocong)();

        if (boss.flash > 0) {
            ctx.globalCompositeOperation = 'lighter';
            ctx.fillStyle = 'rgba(255,255,255,' + (boss.flash * 0.5).toFixed(3) + ')';
            ctx.beginPath(); ctx.arc(0, 0, 62, 0, TAU); ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
        }

        ctx.restore();
        drawBossHp();
    }

    function eyes(lx, ly, r, color) {
        ctx.fillStyle = color;
        ctx.beginPath(); ctx.arc(-lx, ly, r, 0, TAU); ctx.fill();
        ctx.beginPath(); ctx.arc(lx, ly, r, 0, TAU); ctx.fill();
    }

    function drawPocong() {
        ctx.fillStyle = '#f2f0e6';
        ctx.beginPath();
        ctx.moveTo(-32, 52);
        ctx.quadraticCurveTo(-40, -18, 0, -44);
        ctx.quadraticCurveTo(40, -18, 32, 52);
        ctx.closePath();
        ctx.fill();
        ctx.fillStyle = '#d8d4c2';
        ctx.fillRect(-34, 44, 68, 10);
        ctx.beginPath(); ctx.ellipse(0, -44, 11, 9, 0, 0, TAU); ctx.fill();
        ctx.fillStyle = '#efece0';
        ctx.beginPath(); ctx.ellipse(0, -6, 22, 26, 0, 0, TAU); ctx.fill();
        eyes(8, -10, 4.5, '#1b1b22');
        ctx.fillStyle = '#4a4a55';
        ctx.fillRect(-7, 6, 14, 2.5);
    }

    function drawWewe() {
        ctx.fillStyle = '#14101f';
        ctx.beginPath();
        ctx.moveTo(-44, 56);
        ctx.quadraticCurveTo(-52, -20, 0, -46);
        ctx.quadraticCurveTo(52, -20, 44, 56);
        ctx.closePath();
        ctx.fill();
        ctx.fillStyle = '#d9c9b4';
        ctx.beginPath(); ctx.ellipse(0, -14, 17, 20, 0, 0, TAU); ctx.fill();
        ctx.fillStyle = '#0d0a14';
        ctx.beginPath();
        ctx.moveTo(-22, -20);
        ctx.quadraticCurveTo(0, -52, 22, -20);
        ctx.quadraticCurveTo(26, 30, 14, 40);
        ctx.quadraticCurveTo(10, 4, 0, -2);
        ctx.quadraticCurveTo(-10, 4, -14, 40);
        ctx.quadraticCurveTo(-26, 30, -22, -20);
        ctx.closePath();
        ctx.fill();
        eyes(7, -16, 3.6, '#ff3b4d');
    }

    function drawGenderuwo() {
        ctx.fillStyle = '#2a1a12';
        ctx.beginPath(); ctx.ellipse(0, 18, 46, 40, 0, 0, TAU); ctx.fill();
        ctx.beginPath(); ctx.ellipse(0, -24, 30, 26, 0, 0, TAU); ctx.fill();
        ctx.strokeStyle = '#3d2618';
        ctx.lineWidth = 4;
        for (var i = -4; i <= 4; i++) {
            ctx.beginPath();
            ctx.moveTo(i * 9, -44);
            ctx.lineTo(i * 11, -56 - Math.abs(i) * 2);
            ctx.stroke();
        }
        ctx.fillStyle = '#42291a';
        ctx.beginPath(); ctx.ellipse(-44, 6, 12, 20, 0.3, 0, TAU); ctx.fill();
        ctx.beginPath(); ctx.ellipse(44, 6, 12, 20, -0.3, 0, TAU); ctx.fill();
        eyes(12, -26, 5.5, '#ff2f2f');
        ctx.fillStyle = '#f5e9d0';
        ctx.beginPath();
        ctx.moveTo(-10, -8); ctx.lineTo(-5, 2); ctx.lineTo(0, -8);
        ctx.lineTo(5, 2); ctx.lineTo(10, -8);
        ctx.closePath();
        ctx.fill();
    }

    function drawKelelawar() {
        var flap = Math.sin(clock * 6) * 0.5;
        ctx.fillStyle = '#241536';
        [-1, 1].forEach(function (dir) {
            ctx.save();
            ctx.scale(dir, 1);
            ctx.rotate(flap * 0.25);
            ctx.beginPath();
            ctx.moveTo(14, -6);
            ctx.quadraticCurveTo(52, -34, 76, -10);
            ctx.quadraticCurveTo(58, -4, 60, 14);
            ctx.quadraticCurveTo(44, 2, 34, 18);
            ctx.quadraticCurveTo(26, 4, 14, 14);
            ctx.closePath();
            ctx.fill();
            ctx.restore();
        });
        ctx.fillStyle = '#35204d';
        ctx.beginPath(); ctx.ellipse(0, 4, 18, 24, 0, 0, TAU); ctx.fill();
        ctx.beginPath(); ctx.ellipse(0, -22, 15, 13, 0, 0, TAU); ctx.fill();
        ctx.fillStyle = '#35204d';
        ctx.beginPath();
        ctx.moveTo(-13, -30); ctx.lineTo(-7, -46); ctx.lineTo(-2, -30); ctx.closePath(); ctx.fill();
        ctx.beginPath();
        ctx.moveTo(13, -30); ctx.lineTo(7, -46); ctx.lineTo(2, -30); ctx.closePath(); ctx.fill();
        eyes(6, -24, 3.6, '#ffd23b');
    }

    function drawUfo() {
        ctx.fillStyle = 'rgba(120, 255, 220, .16)';
        ctx.beginPath();
        ctx.moveTo(-18, 14); ctx.lineTo(18, 14); ctx.lineTo(54, 88); ctx.lineTo(-54, 88);
        ctx.closePath();
        ctx.fill();
        ctx.fillStyle = '#bfe9ff';
        ctx.beginPath(); ctx.ellipse(0, -16, 26, 20, 0, Math.PI, TAU); ctx.fill();
        ctx.fillStyle = '#2e3a52';
        ctx.beginPath(); ctx.ellipse(0, -20, 9, 12, 0, 0, TAU); ctx.fill();
        ctx.beginPath(); ctx.ellipse(0, -34, 7, 7, 0, 0, TAU); ctx.fill();
        ctx.fillStyle = '#8e9bb5';
        ctx.beginPath(); ctx.ellipse(0, 0, 62, 18, 0, 0, TAU); ctx.fill();
        ctx.fillStyle = '#5d6b86';
        ctx.beginPath(); ctx.ellipse(0, 6, 50, 11, 0, 0, TAU); ctx.fill();
        for (var i = -2; i <= 2; i++) {
            var on = (Math.floor(clock * 4) + i + 5) % 5 === 0;
            ctx.fillStyle = on ? '#6bffd2' : '#2c6d5c';
            ctx.beginPath(); ctx.arc(i * 20, 6, 4.5, 0, TAU); ctx.fill();
        }
    }

    function roundRect(x, y, w, h, r) {
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.lineTo(x + w - r, y);
        ctx.quadraticCurveTo(x + w, y, x + w, y + r);
        ctx.lineTo(x + w, y + h - r);
        ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
        ctx.lineTo(x + r, y + h);
        ctx.quadraticCurveTo(x, y + h, x, y + h - r);
        ctx.lineTo(x, y + r);
        ctx.quadraticCurveTo(x, y, x + r, y);
        ctx.closePath();
    }

    var ROTORS = [[-1, -1], [1, -1], [-1, 1], [1, 1]];

    function drawDrone() {
        var spin = clock * 22;

        ctx.strokeStyle = '#39404f';
        ctx.lineWidth = 7;
        ROTORS.forEach(function (d) {
            ctx.beginPath(); ctx.moveTo(0, 0); ctx.lineTo(d[0] * 48, d[1] * 30); ctx.stroke();
        });

        ROTORS.forEach(function (d, i) {
            var rx = d[0] * 48, ry = d[1] * 30;
            ctx.fillStyle = '#20252f';
            ctx.beginPath(); ctx.arc(rx, ry, 7, 0, TAU); ctx.fill();
            ctx.strokeStyle = 'rgba(190, 228, 255, .45)';
            ctx.lineWidth = 3;
            ctx.beginPath(); ctx.arc(rx, ry, 22, spin + i, spin + i + 4.4); ctx.stroke();
            ctx.beginPath(); ctx.arc(rx, ry, 16, -spin - i, -spin - i + 3.4); ctx.stroke();
        });

        ctx.fillStyle = '#2b3240'; roundRect(-30, -20, 60, 40, 12); ctx.fill();
        ctx.fillStyle = '#3c4658'; roundRect(-22, -14, 44, 18, 8); ctx.fill();
        ctx.fillStyle = 'rgba(255, 90, 90, .3)';
        ctx.beginPath(); ctx.ellipse(0, 9, 25, 12, 0, 0, TAU); ctx.fill();
        ctx.fillStyle = '#ff3b3b';
        ctx.beginPath(); ctx.ellipse(0, 9, 14, 6, 0, 0, TAU); ctx.fill();
    }

    function drawMecha() {
        ctx.fillStyle = '#4a5666';
        roundRect(-62, -18, 34, 30, 8); ctx.fill();
        roundRect(28, -18, 34, 30, 8); ctx.fill();

        ctx.fillStyle = '#39434f';
        roundRect(-58, 10, 16, 34, 5); ctx.fill();
        roundRect(42, 10, 16, 34, 5); ctx.fill();
        ctx.fillStyle = '#ff9a3c';
        ctx.beginPath(); ctx.arc(-50, 44, 6, 0, TAU); ctx.fill();
        ctx.beginPath(); ctx.arc(50, 44, 6, 0, TAU); ctx.fill();

        ctx.fillStyle = '#5b6a7d'; roundRect(-30, -22, 60, 56, 10); ctx.fill();
        ctx.fillStyle = '#3d4756'; roundRect(-22, 6, 44, 22, 6); ctx.fill();

        var pulse = 0.65 + 0.35 * Math.sin(clock * 5);
        ctx.fillStyle = 'rgba(255, 150, 50, ' + pulse.toFixed(2) + ')';
        ctx.beginPath(); ctx.arc(0, -4, 11, 0, TAU); ctx.fill();

        ctx.fillStyle = '#6b7b90'; roundRect(-16, -48, 32, 26, 7); ctx.fill();
        ctx.fillStyle = '#31e0ff'; roundRect(-11, -40, 22, 7, 3); ctx.fill();
    }

    function drawSatelit() {
        ctx.fillStyle = '#1b3a6b';
        roundRect(-96, -22, 56, 44, 3); ctx.fill();
        roundRect(40, -22, 56, 44, 3); ctx.fill();
        ctx.strokeStyle = 'rgba(130, 205, 255, .5)';
        ctx.lineWidth = 1.5;
        for (var i = 1; i < 4; i++) {
            ctx.beginPath(); ctx.moveTo(-96 + i * 14, -22); ctx.lineTo(-96 + i * 14, 22); ctx.stroke();
            ctx.beginPath(); ctx.moveTo(40 + i * 14, -22); ctx.lineTo(40 + i * 14, 22); ctx.stroke();
        }

        ctx.strokeStyle = '#8894a8'; ctx.lineWidth = 4;
        ctx.beginPath(); ctx.moveTo(-40, 0); ctx.lineTo(-24, 0); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(24, 0); ctx.lineTo(40, 0); ctx.stroke();

        ctx.fillStyle = '#d9dee8'; roundRect(-24, -26, 48, 52, 8); ctx.fill();
        ctx.fillStyle = '#aab3c2'; roundRect(-24, -6, 48, 12, 3); ctx.fill();

        ctx.strokeStyle = '#8894a8'; ctx.lineWidth = 3;
        ctx.beginPath(); ctx.moveTo(0, 22); ctx.lineTo(0, 34); ctx.stroke();
        ctx.fillStyle = '#eef2f8';
        ctx.beginPath(); ctx.ellipse(0, 34, 22, 12, 0, 0, Math.PI); ctx.fill();

        ctx.fillStyle = Math.floor(clock * 6) % 2 === 0 ? '#39e07f' : '#166b3c';
        ctx.beginPath(); ctx.arc(0, -16, 6, 0, TAU); ctx.fill();
    }

    function drawKapal() {
        ctx.fillStyle = '#1d2233';
        ctx.beginPath();
        ctx.moveTo(0, 46);
        ctx.lineTo(-86, 4);
        ctx.lineTo(-40, -8);
        ctx.lineTo(-16, -34);
        ctx.lineTo(16, -34);
        ctx.lineTo(40, -8);
        ctx.lineTo(86, 4);
        ctx.closePath();
        ctx.fill();

        ctx.fillStyle = '#2c3350';
        ctx.beginPath();
        ctx.moveTo(0, 26); ctx.lineTo(-30, 2); ctx.lineTo(0, -24); ctx.lineTo(30, 2);
        ctx.closePath(); ctx.fill();

        ctx.fillStyle = '#7a5cf0';
        ctx.beginPath(); ctx.ellipse(0, -4, 9, 14, 0, 0, TAU); ctx.fill();

        var glow = 0.55 + 0.45 * Math.sin(clock * 7);
        ctx.fillStyle = 'rgba(122, 92, 240, ' + glow.toFixed(2) + ')';
        [-52, -30, 30, 52].forEach(function (x) {
            ctx.beginPath(); ctx.ellipse(x, -12, 7, 4, 0, 0, TAU); ctx.fill();
        });
    }

    function drawIntiAi() {
        var rings = [[58, clock * 1.3, '255, 79, 196'],
                     [46, -clock * 0.9, '120, 220, 255'],
                     [74, clock * 2.1, '180, 120, 255']];
        rings.forEach(function (ring) {
            ctx.save();
            ctx.rotate(ring[1]);
            ctx.strokeStyle = 'rgba(' + ring[2] + ', .8)';
            ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.ellipse(0, 0, ring[0], ring[0] * 0.34, 0, 0, TAU);
            ctx.stroke();
            ctx.restore();
        });

        ctx.fillStyle = 'rgba(255, 120, 220, .55)';
        for (var i = 0; i < 6; i++) {
            var a = clock * 2.1 + i * TAU / 6;
            ctx.save();
            ctx.translate(Math.cos(a) * 64, Math.sin(a) * 26);
            ctx.rotate(a);
            ctx.fillRect(-5, -5, 10, 10);
            ctx.restore();
        }

        var pulse = 0.7 + 0.3 * Math.sin(clock * 6);
        var core = ctx.createRadialGradient(0, 0, 3, 0, 0, 36);
        core.addColorStop(0, '#ffffff');
        core.addColorStop(0.4, 'rgba(255, 79, 196, ' + pulse.toFixed(2) + ')');
        core.addColorStop(1, 'rgba(120, 40, 160, 0)');
        ctx.fillStyle = core;
        ctx.beginPath(); ctx.arc(0, 0, 36, 0, TAU); ctx.fill();

        ctx.fillStyle = '#2a0b22';
        ctx.beginPath(); ctx.ellipse(0, 0, 7, 15, 0, 0, TAU); ctx.fill();
    }

    function drawBossHp() {
        var w = Math.min(320, W * 0.6), x = (W - w) / 2, y = 16;
        var pct = bossHp / LEVEL.bossHp;

        ctx.fillStyle = 'rgba(8, 10, 28, .65)';
        ctx.fillRect(x - 3, y - 3, w + 6, 16);
        ctx.fillStyle = 'rgba(255,255,255,.14)';
        ctx.fillRect(x, y, w, 10);
        ctx.fillStyle = 'hsl(' + (pct * 110) + ', 85%, 55%)';
        ctx.fillRect(x, y, w * pct, 10);

        ctx.font = '700 11px system-ui, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        ctx.fillStyle = isTerang ? '#16233f' : '#dbe3ff';
        ctx.fillText(LEVEL.bossName.toUpperCase(), W / 2, y + 15);
    }

    function drawBossIntro() {
        var t = 1 - phaseTimer / BOSS_INTRO;
        var pop = Math.min(1, t * 3);
        ctx.save();
        ctx.translate(W / 2, H * 0.42);
        ctx.scale(0.7 + pop * 0.3, 0.7 + pop * 0.3);
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.font = '800 15px system-ui, sans-serif';
        ctx.fillStyle = isTerang ? 'rgba(190, 30, 40, .95)' : 'rgba(255, 120, 120, .9)';
        ctx.fillText('BOSS ' + LEVEL.label, 0, -34);
        ctx.font = '800 40px system-ui, sans-serif';
        ctx.fillStyle = isTerang ? '#10192e' : '#fff';
        ctx.fillText(LEVEL.bossName, 0, 0);
        ctx.font = '600 13px system-ui, sans-serif';
        ctx.fillStyle = isTerang ? 'rgba(26, 40, 70, .9)' : 'rgba(210, 220, 255, .85)';
        ctx.fillText('Senjata: ' + LEVEL.bossWeapon, 0, 30);
        ctx.restore();
    }

    // ---------- gambar: objek jatuh ----------

    function drawBeams() {
        var ox = W / 2, oy = groundY - 14;
        for (var i = 0; i < beams.length; i++) {
            var b = beams[i];
            var a = 1 - b.age / b.life;
            var color = b.color || '#8ce6ff';
            ctx.strokeStyle = hexToRgba(color, a * 0.85);
            ctx.lineWidth = 2 + a * 3;
            ctx.shadowColor = hexToRgba(color, 0.9);
            ctx.shadowBlur = 12;
            ctx.beginPath(); ctx.moveTo(ox, oy); ctx.lineTo(b.x, b.y); ctx.stroke();
            ctx.shadowBlur = 0;
        }
    }

    function drawFalling(f) {
        var colors = f.kind === 'bullet' ? bossBulletColors() : ['#ffdca6', '#ff8b3d', '#8b2b07'];
        if (f.returning) colors = ['#ffffff', colors[0], colors[1]];   // memantul = menyala terang
        var i, p, k;

        for (i = f.trail.length - 1; i >= 1; i--) {
            p = f.trail[i];
            k = 1 - i / f.trail.length;
            ctx.fillStyle = hexToRgba(colors[1], k * (f.returning ? 0.65 : 0.4));
            ctx.beginPath(); ctx.arc(p.x, p.y, f.r * k * 0.82, 0, TAU); ctx.fill();
        }

        ctx.save();
        ctx.translate(f.x, f.y);

        var halo = ctx.createRadialGradient(0, 0, f.r * 0.45, 0, 0, f.r * 1.95);
        halo.addColorStop(0, hexToRgba(colors[1], 0.5));
        halo.addColorStop(1, hexToRgba(colors[1], 0));
        ctx.fillStyle = halo;
        ctx.beginPath(); ctx.arc(0, 0, f.r * 1.95, 0, TAU); ctx.fill();

        ctx.save();
        ctx.rotate(f.angle);
        var rock = ctx.createRadialGradient(-f.r * 0.35, -f.r * 0.4, f.r * 0.12, 0, 0, f.r);
        rock.addColorStop(0, colors[0]);
        rock.addColorStop(0.45, colors[1]);
        rock.addColorStop(1, colors[2]);
        ctx.fillStyle = rock;
        ctx.beginPath(); ctx.arc(0, 0, f.r, 0, TAU); ctx.fill();
        ctx.fillStyle = hexToRgba(colors[2], 0.4);
        ctx.beginPath(); ctx.arc(f.r * 0.38, -f.r * 0.26, f.r * 0.19, 0, TAU); ctx.fill();
        ctx.beginPath(); ctx.arc(-f.r * 0.3, f.r * 0.36, f.r * 0.13, 0, TAU); ctx.fill();
        ctx.restore();

        // inti gelap + huruf tidak ikut berputar supaya selalu terbaca
        ctx.fillStyle = 'rgba(14, 9, 4, .82)';
        ctx.beginPath(); ctx.arc(0, 0, f.r * 0.58, 0, TAU); ctx.fill();

        ctx.font = '800 ' + Math.round(f.r * 0.92) + 'px ui-monospace, Consolas, "Courier New", monospace';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.shadowColor = hexToRgba(colors[0], 0.95);
        ctx.shadowBlur = 10;
        ctx.fillStyle = '#fff6e2';
        ctx.fillText(f.char.toUpperCase(), 0, f.r * 0.05);
        ctx.shadowBlur = 0;
        ctx.restore();
    }

    // Warna ini diketik admin, jadi nilai aneh tidak boleh membuat kanvas gagal menggambar.
    function hexToRgba(hex, a) {
        var s = String(hex || '').replace('#', '');
        if (s.length === 3) s = s[0] + s[0] + s[1] + s[1] + s[2] + s[2];
        var n = parseInt(s, 16);
        if (!isFinite(n)) n = 0;
        return 'rgba(' + ((n >> 16) & 255) + ',' + ((n >> 8) & 255) + ',' + (n & 255) + ',' + a + ')';
    }

    function drawParticles() {
        for (var i = 0; i < particles.length; i++) {
            var q = particles[i];
            var a = 1 - q.age / q.life;
            ctx.fillStyle = 'hsla(' + q.hue + ', 100%, ' + (55 + 25 * a) + '%, ' + a.toFixed(3) + ')';
            ctx.beginPath(); ctx.arc(q.x, q.y, q.size * a, 0, TAU); ctx.fill();
        }
    }

    function drawCombo() {
        if (combo < 2) return;
        var pop = 1 + comboPop * 0.35;
        ctx.save();
        ctx.translate(W / 2, H * 0.62);
        ctx.scale(pop, pop);
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        var rgb = isTerang ? '150, 80, 0' : '255, 224, 130';
        ctx.font = '800 34px ui-monospace, Consolas, monospace';
        ctx.fillStyle = 'rgba(' + rgb + ', ' + (0.26 + comboPop * 0.45).toFixed(3) + ')';
        ctx.fillText(combo + '×', 0, 0);
        ctx.font = '700 11px system-ui, sans-serif';
        ctx.fillStyle = 'rgba(' + rgb + ', ' + (0.18 + comboPop * 0.3).toFixed(3) + ')';
        ctx.fillText('KOMBO', 0, 24);
        ctx.restore();
    }

    function drawDamage() {
        if (hurt > 0) {
            var v = ctx.createRadialGradient(W / 2, H / 2, Math.min(W, H) * 0.25, W / 2, H / 2, Math.max(W, H) * 0.7);
            v.addColorStop(0, 'rgba(255, 40, 40, 0)');
            v.addColorStop(1, 'rgba(255, 40, 40, ' + (hurt * 0.5).toFixed(3) + ')');
            ctx.fillStyle = v;
            ctx.fillRect(0, 0, W, H);
        }
        if (wrongFlash > 0) {
            // kilatan putih tak terlihat di langit pagi, jadi dibalik jadi gelap
            ctx.fillStyle = isTerang
                ? 'rgba(20, 30, 60, ' + (wrongFlash * 0.12).toFixed(3) + ')'
                : 'rgba(255, 255, 255, ' + (wrongFlash * 0.09).toFixed(3) + ')';
            ctx.fillRect(0, 0, W, H);
        }
    }

    function draw() {
        ctx.save();
        if (shake > 0.2) {
            ctx.translate((Math.random() - 0.5) * shake, (Math.random() - 0.5) * shake);
        }
        drawSky();
        drawSkyDetail();
        drawBeams();
        drawBase();
        drawCombo();                 // di bawah objek jatuh supaya tidak menutupi huruf
        if (phase === 'boss') drawBoss();
        for (var i = 0; i < falling.length; i++) drawFalling(falling[i]);
        drawParticles();
        if (phase === 'bossIntro') drawBossIntro();
        ctx.restore();
        drawDamage();
    }

    // ---------- loop ----------

    function frame(now) {
        var dt = (now - lastFrame) / 1000;
        lastFrame = now;
        if (!(dt > 0)) dt = 0;
        if (dt > 0.05) dt = 0.05;   // lompatan besar (tab pindah / lag) tidak dihitung sebagai waktu main

        if (state === 'playing') {
            update(dt);
            hudTimer += dt;
            if (hudTimer >= 0.25) {
                hudTimer = 0;
                hud.time.textContent = Math.floor(elapsed);
                hud.wpm.textContent = wpm();
            }
        }
        draw();
        requestAnimationFrame(frame);
    }

    // ---------- input ----------

    function onKeyDown(e) {
        if (e.ctrlKey || e.metaKey || e.altKey) return;

        if (state === 'intro') {
            if (e.code === 'Space' || e.key === 'Enter') { e.preventDefault(); startGame(); }
            return;
        }
        // Jeda singkat supaya ketikan sisa dari pertempuran tidak langsung
        // melompati layar hasil begitu boss tumbang.
        if (state === 'won' || state === 'lost') {
            if (e.code !== 'Space' && e.key !== 'Enter') return;
            e.preventDefault();
            if (now() - finishedAt < 600) return;
            if (state === 'lost') { startGame(); }
            else if (nextLevelBtn) { nextLevelBtn.click(); }
            return;
        }

        if (e.key === 'Escape') { e.preventDefault(); setPaused(state === 'playing'); return; }
        if (state === 'paused') {
            if (e.code === 'Space') { e.preventDefault(); setPaused(false); }
            return;
        }
        if (e.repeat) return;

        var key = e.key.length === 1 ? e.key.toLowerCase() : '';
        if (LEVEL.keys.indexOf(key) === -1) return;
        e.preventDefault();
        handleChar(key);
    }

    /**
     * Satu pintu untuk dua jalur ketikan: keyboard fisik (keydown) dan keyboard
     * layar di HP (event input). Kalau sebuah tombol menyalakan keduanya, yang
     * kedua diabaikan supaya tidak terhitung dua kali.
     */
    function handleChar(key) {
        if (state !== 'playing' || LEVEL.keys.indexOf(key) === -1) return;

        var t = now();
        if (key === lastChar && t - lastCharAt < 40) return;
        lastChar = key;
        lastCharAt = t;

        var target = null;
        for (var i = 0; i < falling.length; i++) {
            if (falling[i].char === key && !falling[i].returning &&
                (target === null || falling[i].y > target.y)) {
                target = falling[i];
            }
        }
        if (target === null) { wrongKey(); return; }
        hitFalling(target);
    }

    window.addEventListener('keydown', onKeyDown);

    // blur() penting: tombol yang masih ter-fokus ikut tertekan saat pemain menekan SPASI
    pauseBtn.addEventListener('click', function () { this.blur(); setPaused(state === 'playing'); });
    document.getElementById('startBtn').addEventListener('click', function () { this.blur(); startGame(); });
    document.getElementById('resumeBtn').addEventListener('click', function () { this.blur(); setPaused(false); });

    // Di HP: sentuhan pada arena memunculkan keyboard layar lewat kotak tak terlihat.
    // focus() hanya membuka keyboard kalau dipanggil di dalam sentuhan pengguna,
    // jadi ini sengaja menumpang pada event click yang menggelembung dari tombol.
    function focusSoftKey() {
        if (!isTouch || state !== 'playing') return;
        try { softKey.focus({ preventScroll: true }); } catch (e) { softKey.focus(); }
    }

    softKey.addEventListener('input', function () {
        var typed = softKey.value;
        softKey.value = '';
        if (typed) handleChar(typed.charAt(typed.length - 1).toLowerCase());
    });

    if (isTouch) {
        wrap.addEventListener('click', focusSoftKey);
    }

    var fsBtn = document.getElementById('fsBtn');
    var hudBar = document.getElementById('hudBar');
    var hudHome = hudBar.parentNode;
    var canFullscreen = wrap.requestFullscreen || wrap.webkitRequestFullscreen;

    function onFullscreenChange() {
        var full = isFullscreen();
        if (full) {
            wrap.appendChild(hudBar);
            hudBar.classList.add('hud-float');
        } else {
            hudHome.insertBefore(hudBar, hudHome.firstChild);
            hudBar.classList.remove('hud-float');
        }
        fsBtn.innerHTML = full
            ? '<i class="bi bi-fullscreen-exit"></i> Keluar'
            : '<i class="bi bi-arrows-fullscreen"></i> Layar Penuh';
        resize();
    }

    if (canFullscreen) {
        fsBtn.addEventListener('click', function () {
            this.blur();
            if (isFullscreen()) {
                (document.exitFullscreen || document.webkitExitFullscreen).call(document);
            } else {
                canFullscreen.call(wrap);
            }
        });
        document.addEventListener('fullscreenchange', onFullscreenChange);
        document.addEventListener('webkitfullscreenchange', onFullscreenChange);
    } else {
        fsBtn.classList.add('d-none');
    }
    document.getElementById('retryBtn').addEventListener('click', startGame);
    document.getElementById('replayBtn').addEventListener('click', startGame);
    document.addEventListener('visibilitychange', function () {
        if (document.hidden && state === 'playing') setPaused(true);
    });
    window.addEventListener('blur', function () {
        if (state === 'playing') setPaused(true);
    });

    if (window.ResizeObserver) {
        new ResizeObserver(resize).observe(wrap);
    } else {
        window.addEventListener('resize', resize);
    }

    if (isTouch) {
        document.getElementById('touchNotice').classList.remove('d-none');
    }

    // ---------- start ----------

    buildSky();
    reset();
    resize();
    lastFrame = performance.now();
    requestAnimationFrame(frame);
})();
</script>
@endpush
