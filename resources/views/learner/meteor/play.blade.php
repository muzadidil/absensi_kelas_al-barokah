@extends('layouts.learner')

@section('title', 'Game 10 Jari')

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
    .meteor-page .blink { animation: meteorBlink 1.2s ease-in-out infinite; }
    @keyframes meteorBlink { 50% { opacity: .3; } }

    .meteor-page .hud {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .4rem .75rem;
        margin-bottom: .6rem;
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
            <h5 class="fw-bold mb-1"><i class="bi bi-rocket-takeoff me-1"></i> Game 10 Jari</h5>
            <p class="text-muted small mb-0">
                Jari istirahat di <code>A S D F G H J K L ;</code> — ketik huruf pada meteor sebelum menghantam Al-Barokah.
            </p>
        </div>
        <a href="{{ route('learner.dashboard') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div id="touchNotice" class="alert alert-warning py-2 small d-none">
        <i class="bi bi-keyboard me-1"></i>
        Game ini butuh <strong>keyboard fisik</strong>. Di HP/tablet tanpa keyboard, permainan tidak bisa dimainkan.
    </div>

    <div class="card">
        <div class="card-body">

            <div class="hud">
                <span class="chip">Nyawa <b class="lives" id="hudLives"></b></span>
                <span class="chip">Skor <b id="hudScore">0</b></span>
                <span class="chip">Kombo <b id="hudCombo">—</b></span>
                <span class="chip">Hancur <b id="hudDestroyed">0</b></span>
                <span class="chip">Akurasi <b id="hudAcc">100%</b></span>
                <span class="chip">WPM <b id="hudWpm">0</b></span>
                <span class="chip">Waktu <b id="hudTime">0</b>dtk</span>
                <button type="button" id="pauseBtn" class="btn btn-outline-secondary btn-sm ms-auto d-none">
                    <i class="bi bi-pause-fill"></i> Jeda
                </button>
            </div>

            <div class="arena-wrap" id="meteorArena">
                <canvas id="meteorCanvas"></canvas>

                <div class="overlay" id="introOverlay">
                    <h4 class="mb-2">Selamat datang, {{ $learner->nama_lengkap }}!</h4>
                    <p class="mb-4">Apakah kamu siap menyelamatkan Al-Barokah dari Meteor?</p>
                    <p class="blink mb-0">Tekan <span class="hint-key">SPASI</span> jika siap</p>
                </div>

                <div class="overlay d-none" id="pauseOverlay">
                    <h4 class="mb-2"><i class="bi bi-pause-circle me-1"></i> Jeda</h4>
                    <p class="mb-0">Tekan <span class="hint-key">SPASI</span> untuk lanjut</p>
                </div>

                <div class="overlay d-none" id="overOverlay">
                    <h4 class="mb-1">Markas Al-Barokah kebobolan!</h4>
                    <p class="small mb-0" id="overSubtitle"></p>

                    <div class="result-grid">
                        <div><div class="num" id="resScore">0</div><div class="lbl">Skor</div></div>
                        <div><div class="num" id="resWpm">0</div><div class="lbl">WPM</div></div>
                        <div><div class="num" id="resAcc">0%</div><div class="lbl">Akurasi</div></div>
                        <div><div class="num" id="resCombo">0</div><div class="lbl">Kombo Terbaik</div></div>
                    </div>

                    <button type="button" id="restartBtn" class="btn btn-primary px-4">
                        <i class="bi bi-arrow-repeat me-1"></i> Main Lagi
                    </button>
                    <p class="small mt-3 mb-0">atau tekan <span class="hint-key">SPASI</span></p>
                </div>
            </div>

            <p class="text-muted small mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Kecepatan jatuh meteor tetap — yang bertambah adalah <em>jumlah</em> meteornya.
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

    var LETTERS       = ['a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', ';'];
    var START_LIVES   = 5;
    var FALL_SECONDS  = 7.0;   // konstan di semua ukuran layar — kesulitan naik dari jumlah, bukan kecepatan
    var SPAWN_START   = 1.60;  // detik antar meteor di awal
    var SPAWN_MIN     = 0.55;  // detik antar meteor saat tersibuk
    var RAMP_SECONDS  = 120;   // lama ramp dari SPAWN_START ke SPAWN_MIN
    var GROUND_H      = 56;
    var TAU           = Math.PI * 2;

    var canvas  = document.getElementById('meteorCanvas');
    var wrap    = document.getElementById('meteorArena');
    if (!canvas || !wrap) return;
    var ctx = canvas.getContext('2d');

    var introOverlay = document.getElementById('introOverlay');
    var pauseOverlay = document.getElementById('pauseOverlay');
    var overOverlay  = document.getElementById('overOverlay');
    var pauseBtn     = document.getElementById('pauseBtn');

    var hud = {
        lives:     document.getElementById('hudLives'),
        score:     document.getElementById('hudScore'),
        combo:     document.getElementById('hudCombo'),
        destroyed: document.getElementById('hudDestroyed'),
        acc:       document.getElementById('hudAcc'),
        wpm:       document.getElementById('hudWpm'),
        time:      document.getElementById('hudTime')
    };

    var W = 0, H = 0, groundY = 0;
    var state = 'intro';           // intro | playing | paused | over
    var lives, score, combo, bestCombo, destroyed, missed, wrongKeys;
    var elapsed, clock, spawnAcc, spawnGap;
    var meteors, particles, beams, stars;
    var shake, hurt, wrongFlash, comboPop;
    var lastFrame = 0, hudTimer = 0;

    // ---------- ukuran & bintang ----------

    function buildStars() {
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
    }

    function resize() {
        var cssW = Math.max(280, Math.round(wrap.clientWidth));
        var cssH = Math.round(Math.min(560, Math.max(330, cssW * 0.58)));
        var dpr  = Math.min(2, window.devicePixelRatio || 1);
        var pxW  = Math.round(cssW * dpr);
        var pxH  = Math.round(cssH * dpr);

        // menugaskan canvas.width mengosongkan kanvas, jadi lakukan hanya saat ukurannya berubah
        if (W === cssW && H === cssH && canvas.width === pxW && canvas.height === pxH) return;

        var sx = W > 0 ? cssW / W : 1;
        var sy = H > 0 ? cssH / H : 1;

        W = cssW;
        H = cssH;
        groundY = H - GROUND_H;

        canvas.width  = pxW;
        canvas.height = pxH;
        canvas.style.height = cssH + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        if (meteors) {
            for (var i = 0; i < meteors.length; i++) {
                var m = meteors[i];
                m.x *= sx;
                m.y *= sy;
                m.trail.length = 0;
                m.speed = ((groundY + m.r + 10) / FALL_SECONDS) * m.speedVar;
            }
        }
    }

    // ---------- siklus permainan ----------

    function reset() {
        lives = START_LIVES;
        score = 0; combo = 0; bestCombo = 0;
        destroyed = 0; missed = 0; wrongKeys = 0;
        elapsed = 0; clock = 0;
        spawnAcc = 0; spawnGap = SPAWN_START;
        meteors = []; particles = []; beams = [];
        shake = 0; hurt = 0; wrongFlash = 0; comboPop = 0;
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

    function syncHud() {
        hud.lives.innerHTML =
            '<i class="bi bi-heart-fill"></i>'.repeat(lives) +
            '<i class="bi bi-heart gone"></i>'.repeat(START_LIVES - lives);
        hud.score.textContent     = score;
        hud.destroyed.textContent = destroyed;
        hud.acc.textContent       = accuracy() + '%';
        hud.combo.textContent     = combo >= 2 ? combo + '× (skor ×' + multiplier() + ')' : '—';
    }

    function multiplier() {
        return 1 + Math.min(4, Math.floor(combo / 5));
    }

    function show(el)  { el.classList.remove('d-none'); }
    function hide(el)  { el.classList.add('d-none'); }

    function startGame() {
        reset();
        state = 'playing';
        hide(introOverlay); hide(pauseOverlay); hide(overOverlay);
        show(pauseBtn);
        spawn();
    }

    function setPaused(on) {
        if (state !== 'playing' && state !== 'paused') return;
        state = on ? 'paused' : 'playing';
        if (on) { show(pauseOverlay); } else { hide(pauseOverlay); }
        pauseBtn.innerHTML = on
            ? '<i class="bi bi-play-fill"></i> Lanjut'
            : '<i class="bi bi-pause-fill"></i> Jeda';
    }

    function endGame() {
        state = 'over';
        hide(pauseBtn);
        document.getElementById('resScore').textContent = score;
        document.getElementById('resWpm').textContent   = wpm();
        document.getElementById('resAcc').textContent   = accuracy() + '%';
        document.getElementById('resCombo').textContent = bestCombo;
        document.getElementById('overSubtitle').textContent =
            destroyed + ' meteor dihancurkan, ' + missed + ' lolos dalam ' + Math.round(elapsed) + ' detik.';
        show(overOverlay);
    }

    // ---------- meteor ----------

    function spawn() {
        var onScreen = meteors.map(function (m) { return m.char; });
        var pool = LETTERS.filter(function (c) { return onScreen.indexOf(c) === -1; });
        if (pool.length === 0) pool = LETTERS;
        var char = pool[(Math.random() * pool.length) | 0];

        var r = 21 + Math.random() * 8;
        var margin = r + 12;
        var span = Math.max(1, W - margin * 2);

        // best-candidate sampling: sebar horizontal supaya meteor tidak menumpuk
        var x = margin + Math.random() * span;
        if (meteors.length > 0) {
            var bestGap = -1;
            for (var i = 0; i < 12; i++) {
                var cand = margin + Math.random() * span;
                var nearest = Infinity;
                for (var j = 0; j < meteors.length; j++) {
                    nearest = Math.min(nearest, Math.abs(meteors[j].x - cand));
                }
                if (nearest > bestGap) { bestGap = nearest; x = cand; }
            }
        }

        var speedVar = 0.92 + Math.random() * 0.16;
        meteors.push({
            char: char,
            x: x,
            y: -r - 10,
            r: r,
            vx: (Math.random() - 0.5) * 26,
            speedVar: speedVar,
            speed: ((groundY + r + 10) / FALL_SECONDS) * speedVar,
            angle: Math.random() * TAU,
            spin: (Math.random() - 0.5) * 1.8,
            trail: []
        });
    }

    function burst(x, y, amount, spread, upward) {
        for (var i = 0; i < amount; i++) {
            var a = upward
                ? -Math.PI / 2 + (Math.random() - 0.5) * 2.2
                : Math.random() * TAU;
            var sp = 40 + Math.random() * spread;
            particles.push({
                x: x, y: y,
                vx: Math.cos(a) * sp,
                vy: Math.sin(a) * sp,
                life: 0.45 + Math.random() * 0.45,
                age: 0,
                size: 1.5 + Math.random() * 2.8,
                hue: 18 + Math.random() * 32
            });
        }
    }

    function hitMeteor(m) {
        meteors.splice(meteors.indexOf(m), 1);
        destroyed++;
        combo++;
        if (combo > bestCombo) bestCombo = combo;
        if (combo >= 2) comboPop = 1;
        score += 10 * multiplier();
        beams.push({ x: m.x, y: m.y, age: 0, life: 0.15 });
        burst(m.x, m.y, 20, 150, false);
        shake = Math.max(shake, 3);
        syncHud();
    }

    function impact(m) {
        missed++;
        combo = 0;
        lives = Math.max(0, lives - 1);
        shake = 16;
        hurt = 1;
        burst(m.x, groundY, 28, 190, true);
        syncHud();
        if (lives <= 0) endGame();
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

        var t = Math.min(1, elapsed / RAMP_SECONDS);
        spawnGap = SPAWN_START + (SPAWN_MIN - SPAWN_START) * t;

        spawnAcc += dt;
        if (spawnAcc >= spawnGap) { spawnAcc -= spawnGap; spawn(); }

        for (var i = meteors.length - 1; i >= 0; i--) {
            var m = meteors[i];
            m.y += m.speed * dt;
            m.x += m.vx * dt;
            m.angle += m.spin * dt;

            if (m.x < m.r)     { m.x = m.r;     m.vx =  Math.abs(m.vx); }
            if (m.x > W - m.r) { m.x = W - m.r; m.vx = -Math.abs(m.vx); }

            m.trail.unshift({ x: m.x, y: m.y });
            if (m.trail.length > 9) m.trail.pop();

            if (m.y >= groundY) {
                meteors.splice(i, 1);
                impact(m);
                if (state === 'over') return;
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

        for (var s = 0; s < stars.length; s++) {
            stars[s].y += stars[s].drift * dt;
            if (stars[s].y > 1) { stars[s].y -= 1; stars[s].x = Math.random(); }
        }

        shake      = Math.max(0, shake - dt * 42);
        hurt       = Math.max(0, hurt - dt * 1.8);
        wrongFlash = Math.max(0, wrongFlash - dt * 4.2);
        comboPop   = Math.max(0, comboPop - dt * 2.4);
    }

    // ---------- gambar ----------

    function drawSky() {
        var g = ctx.createLinearGradient(0, 0, 0, H);
        g.addColorStop(0, '#04061a');
        g.addColorStop(0.55, '#101a45');
        g.addColorStop(1, '#1d2764');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, W, H);

        var neb = ctx.createRadialGradient(W * 0.22, H * 0.28, 0, W * 0.22, H * 0.28, W * 0.42);
        neb.addColorStop(0, 'rgba(96, 84, 200, .22)');
        neb.addColorStop(1, 'rgba(96, 84, 200, 0)');
        ctx.fillStyle = neb;
        ctx.fillRect(0, 0, W, H);

        var neb2 = ctx.createRadialGradient(W * 0.82, H * 0.14, 0, W * 0.82, H * 0.14, W * 0.34);
        neb2.addColorStop(0, 'rgba(40, 120, 190, .18)');
        neb2.addColorStop(1, 'rgba(40, 120, 190, 0)');
        ctx.fillStyle = neb2;
        ctx.fillRect(0, 0, W, H);
    }

    function drawStars() {
        for (var i = 0; i < stars.length; i++) {
            var s = stars[i];
            var a = 0.35 + 0.45 * (0.5 + 0.5 * Math.sin(clock * 1.6 + s.phase));
            ctx.globalAlpha = a;
            ctx.fillStyle = '#dfe6ff';
            ctx.beginPath();
            ctx.arc(s.x * W, s.y * groundY, s.r, 0, TAU);
            ctx.fill();
        }
        ctx.globalAlpha = 1;
    }

    function drawBase() {
        var cx = W / 2;
        var y  = groundY;
        var s  = Math.max(0.8, Math.min(1.7, W / 640));
        var ratio = lives / START_LIVES;

        var glow = ctx.createLinearGradient(0, y - 90 * s, 0, y);
        glow.addColorStop(0, 'rgba(90, 170, 255, 0)');
        glow.addColorStop(1, 'rgba(110, 200, 255, ' + (0.08 + 0.18 * ratio).toFixed(3) + ')');
        ctx.fillStyle = glow;
        ctx.fillRect(0, y - 90 * s, W, 90 * s);

        var ground = ctx.createLinearGradient(0, y, 0, H);
        ground.addColorStop(0, '#16371f');
        ground.addColorStop(1, '#040c07');
        ctx.fillStyle = ground;
        ctx.fillRect(0, y, W, H - y);

        // siluet markas: kubah tengah + dua menara
        var domeR = 32 * s;
        var hallW = 68 * s;
        var hallH = 26 * s;
        var towerX = 96 * s;
        var towerH = 52 * s;

        ctx.fillStyle = '#04150c';
        ctx.beginPath();
        ctx.rect(cx - hallW, y - hallH, hallW * 2, hallH);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(cx, y - hallH, domeR, Math.PI, TAU);
        ctx.fill();
        ctx.beginPath();
        ctx.rect(cx - 1.5 * s, y - hallH - domeR - 12 * s, 3 * s, 12 * s);
        ctx.fill();

        [-towerX, towerX].forEach(function (dx) {
            ctx.beginPath();
            ctx.rect(cx + dx - 7 * s, y - towerH, 14 * s, towerH);
            ctx.fill();
            ctx.beginPath();
            ctx.arc(cx + dx, y - towerH, 8 * s, Math.PI, TAU);
            ctx.fill();
        });

        // jendela menyala — menegaskan ini "yang sedang dijaga"
        ctx.fillStyle = 'rgba(255, 198, 106, ' + (0.25 + 0.45 * ratio).toFixed(3) + ')';
        for (var i = -2; i <= 2; i++) {
            ctx.fillRect(cx + i * 24 * s - 3 * s, y - hallH * 0.68, 6 * s, 9 * s);
        }

        ctx.strokeStyle = 'rgba(150, 220, 255, ' + (0.22 + 0.5 * ratio).toFixed(3) + ')';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(W, y);
        ctx.stroke();
    }

    function drawBeams() {
        var originX = W / 2;
        var originY = groundY - 14;
        for (var i = 0; i < beams.length; i++) {
            var b = beams[i];
            var a = 1 - b.age / b.life;
            ctx.strokeStyle = 'rgba(140, 230, 255, ' + (a * 0.85).toFixed(3) + ')';
            ctx.lineWidth = 2 + a * 3;
            ctx.shadowColor = 'rgba(120, 220, 255, .9)';
            ctx.shadowBlur = 12;
            ctx.beginPath();
            ctx.moveTo(originX, originY);
            ctx.lineTo(b.x, b.y);
            ctx.stroke();
            ctx.shadowBlur = 0;
        }
    }

    function drawMeteor(m) {
        var i, p, f;

        for (i = m.trail.length - 1; i >= 1; i--) {
            p = m.trail[i];
            f = 1 - i / m.trail.length;
            ctx.fillStyle = 'rgba(255, 138, 55, ' + (f * 0.4).toFixed(3) + ')';
            ctx.beginPath();
            ctx.arc(p.x, p.y, m.r * f * 0.82, 0, TAU);
            ctx.fill();
        }

        ctx.save();
        ctx.translate(m.x, m.y);

        var halo = ctx.createRadialGradient(0, 0, m.r * 0.45, 0, 0, m.r * 1.95);
        halo.addColorStop(0, 'rgba(255, 168, 70, .5)');
        halo.addColorStop(1, 'rgba(255, 120, 40, 0)');
        ctx.fillStyle = halo;
        ctx.beginPath();
        ctx.arc(0, 0, m.r * 1.95, 0, TAU);
        ctx.fill();

        ctx.save();
        ctx.rotate(m.angle);
        var rock = ctx.createRadialGradient(-m.r * 0.35, -m.r * 0.4, m.r * 0.12, 0, 0, m.r);
        rock.addColorStop(0, '#ffdca6');
        rock.addColorStop(0.45, '#ff8b3d');
        rock.addColorStop(1, '#8b2b07');
        ctx.fillStyle = rock;
        ctx.beginPath();
        ctx.arc(0, 0, m.r, 0, TAU);
        ctx.fill();
        ctx.fillStyle = 'rgba(110, 38, 8, .4)';
        ctx.beginPath();
        ctx.arc(m.r * 0.38, -m.r * 0.26, m.r * 0.19, 0, TAU);
        ctx.fill();
        ctx.beginPath();
        ctx.arc(-m.r * 0.3, m.r * 0.36, m.r * 0.13, 0, TAU);
        ctx.fill();
        ctx.restore();

        // inti gelap + huruf tidak ikut berputar supaya selalu terbaca
        ctx.fillStyle = 'rgba(14, 9, 4, .82)';
        ctx.beginPath();
        ctx.arc(0, 0, m.r * 0.58, 0, TAU);
        ctx.fill();

        ctx.font = '800 ' + Math.round(m.r * 0.92) + 'px ui-monospace, Consolas, "Courier New", monospace';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.shadowColor = 'rgba(255, 190, 90, .95)';
        ctx.shadowBlur = 10;
        ctx.fillStyle = '#ffeec4';
        ctx.fillText(m.char.toUpperCase(), 0, m.r * 0.05);
        ctx.shadowBlur = 0;
        ctx.restore();
    }

    function drawParticles() {
        for (var i = 0; i < particles.length; i++) {
            var q = particles[i];
            var a = 1 - q.age / q.life;
            ctx.fillStyle = 'hsla(' + q.hue + ', 100%, ' + (55 + 25 * a) + '%, ' + a.toFixed(3) + ')';
            ctx.beginPath();
            ctx.arc(q.x, q.y, q.size * a, 0, TAU);
            ctx.fill();
        }
    }

    function drawCombo() {
        if (combo < 2) return;
        var pop = 1 + comboPop * 0.35;
        ctx.save();
        ctx.translate(W / 2, H * 0.17);
        ctx.scale(pop, pop);
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.font = '800 34px ui-monospace, Consolas, monospace';
        ctx.fillStyle = 'rgba(255, 224, 130, ' + (0.28 + comboPop * 0.5).toFixed(3) + ')';
        ctx.fillText(combo + '×', 0, 0);
        ctx.font = '700 11px system-ui, sans-serif';
        ctx.fillStyle = 'rgba(255, 224, 130, ' + (0.2 + comboPop * 0.35).toFixed(3) + ')';
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
            ctx.fillStyle = 'rgba(255, 255, 255, ' + (wrongFlash * 0.09).toFixed(3) + ')';
            ctx.fillRect(0, 0, W, H);
        }
    }

    function draw() {
        ctx.save();
        if (shake > 0.2) {
            ctx.translate((Math.random() - 0.5) * shake, (Math.random() - 0.5) * shake);
        }
        drawSky();
        drawStars();
        drawBeams();
        drawBase();
        drawCombo();                 // di bawah meteor supaya tidak pernah menutupi huruf
        for (var i = 0; i < meteors.length; i++) drawMeteor(meteors[i]);
        drawParticles();
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
                hud.wpm.textContent  = wpm();
            }
        }
        draw();
        requestAnimationFrame(frame);
    }

    // ---------- input ----------

    function onKeyDown(e) {
        if (e.ctrlKey || e.metaKey || e.altKey) return;

        if (state === 'intro' || state === 'over') {
            if (e.code === 'Space' || e.key === 'Enter') { e.preventDefault(); startGame(); }
            return;
        }
        if (e.key === 'Escape') { e.preventDefault(); setPaused(state === 'playing'); return; }
        if (state === 'paused') {
            if (e.code === 'Space') { e.preventDefault(); setPaused(false); }
            return;
        }
        if (e.repeat) return;

        var key = e.key.length === 1 ? e.key.toLowerCase() : '';
        if (LETTERS.indexOf(key) === -1) return;
        e.preventDefault();

        var target = null;
        for (var i = 0; i < meteors.length; i++) {
            if (meteors[i].char === key && (target === null || meteors[i].y > target.y)) {
                target = meteors[i];
            }
        }
        if (target === null) { wrongKey(); return; }
        hitMeteor(target);
    }

    window.addEventListener('keydown', onKeyDown);
    pauseBtn.addEventListener('click', function () { setPaused(state === 'playing'); });
    document.getElementById('restartBtn').addEventListener('click', startGame);
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

    if (window.matchMedia && window.matchMedia('(pointer: coarse)').matches) {
        document.getElementById('touchNotice').classList.remove('d-none');
    }

    // ---------- start ----------

    buildStars();
    reset();
    resize();
    lastFrame = performance.now();
    requestAnimationFrame(frame);
})();
</script>
@endpush
