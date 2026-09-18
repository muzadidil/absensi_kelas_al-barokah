@extends('layouts.learner')

@section('title', 'Game 10 Jari')

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-lock-fill me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-rocket-takeoff me-1"></i> Game 10 Jari</h4>
    <p class="text-muted mb-0">
        Selamatkan Al-Barokah dari meteor. Tiap JILID ditutup lawan <strong>boss</strong> —
        kalahkan bossnya sebelum nyawa habis untuk membuka JILID berikutnya.
        Jari istirahat di <code>A S D F G H J K L ;</code>.
    </p>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
    <span class="badge bg-primary-subtle text-primary-emphasis py-2 px-3">
        <i class="bi bi-patch-check me-1"></i> JILID tembus: <strong>{{ $passed->count() }}</strong> / {{ $levels->count() }}
    </span>
    <span class="badge bg-primary-subtle text-primary-emphasis py-2 px-3">
        <i class="bi bi-speedometer2 me-1"></i> WPM terbaik: <strong>{{ $bestWpm }}</strong>
        <span class="opacity-75">(target ~25)</span>
    </span>
    @if($learner->meteor_checkpoint_level > 0)
        <span class="badge bg-success-subtle text-success-emphasis py-2 px-3">
            <i class="bi bi-flag-fill me-1"></i> Checkpoint aman di JILID {{ $learner->meteor_checkpoint_level }}
        </span>
    @endif
</div>

<div class="row g-3">
    @forelse($levels as $level)
        @php
            $isPassed = $passed->contains($level->level_number);
            $isUnlocked = $unlocked[$level->level_number] ?? false;
            $attempts = $attemptCounts[$level->id] ?? 0;
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 {{ $isUnlocked ? '' : 'opacity-75' }}">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary">{{ $level->display_label }}</span>
                        @if($isPassed)
                            <span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Tembus</span>
                        @elseif(!$isUnlocked)
                            <span class="badge bg-secondary"><i class="bi bi-lock-fill me-1"></i>Terkunci</span>
                        @endif
                    </div>

                    <h5 class="fw-bold mb-1">Boss: {{ $level->boss_name }}</h5>
                    <p class="text-muted small flex-grow-1 mb-2">
                        Senjata <strong>{{ $level->boss_weapon_name }}</strong> —
                        tiap serangan memuntahkan {{ $level->boss_bullets_per_shot }} huruf sekaligus.
                    </p>

                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge bg-primary-subtle text-primary-emphasis">
                            <i class="bi bi-asterisk me-1"></i>{{ $level->wave_target }} meteor
                        </span>
                        <span class="badge bg-danger-subtle text-danger-emphasis">
                            <i class="bi bi-heart-fill me-1"></i>{{ $level->lives }} nyawa
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                            <i class="bi bi-type me-1"></i>{{ strlen($level->allowed_keys) }} huruf
                        </span>
                        @if($level->theme === 'pagi')
                            <span class="badge bg-warning-subtle text-warning-emphasis">
                                <i class="bi bi-sunrise me-1"></i>Pagi
                            </span>
                        @endif
                        @if($level->bullet_returns)
                            <span class="badge bg-info-subtle text-info-emphasis">
                                <i class="bi bi-arrow-repeat me-1"></i>Peluru memantul
                            </span>
                        @endif
                        @if($level->is_checkpoint)
                            <span class="badge bg-success-subtle text-success-emphasis">
                                <i class="bi bi-flag-fill me-1"></i>Checkpoint
                            </span>
                        @endif
                    </div>

                    @if($attempts > 0)
                        <div class="small text-muted mb-3">
                            <i class="bi bi-arrow-repeat me-1"></i> Sudah dicoba <strong>{{ $attempts }}×</strong>
                        </div>
                    @endif

                    @if($isUnlocked)
                        <a href="{{ route('learner.meteor.play', $level->id) }}"
                           class="btn {{ $isPassed ? 'btn-outline-primary' : 'btn-primary' }} mt-auto">
                            <i class="bi bi-play-fill me-1"></i> {{ $isPassed ? 'Ulangi' : 'Mulai' }}
                        </a>
                    @else
                        <button type="button" class="btn btn-secondary mt-auto" disabled>
                            <i class="bi bi-lock-fill me-1"></i> Terkunci
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">
                <i class="bi bi-rocket-takeoff display-4 d-block mb-2 opacity-50"></i>
                Belum ada JILID. Jalankan <code>php artisan db:seed --class=MeteorGameLevelSeeder</code>.
            </div>
        </div>
    @endforelse
</div>

<p class="text-muted small mt-4 mb-0">
    <i class="bi bi-info-circle me-1"></i>
    Gagal membuat progres diulang dari
    {{ $learner->meteor_checkpoint_level > 0 ? 'checkpoint terakhir (JILID ' . $learner->meteor_checkpoint_level . ')' : 'JILID 1' }}.
    Kecepatan jatuh meteor sama di semua JILID — yang bertambah jumlah huruf dan kerapatannya.
    WPM hanya tolok ukur, bukan syarat lulus.
</p>

@endsection
