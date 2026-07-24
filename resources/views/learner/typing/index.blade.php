@extends('layouts.learner')

@section('title', 'Latihan Mengetik 10 Jari')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-lock-fill me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-keyboard me-1"></i> Latihan Mengetik 10 Jari</h4>
    <p class="text-muted mb-0">Selesaikan tiap tahap untuk membuka tahap berikutnya. Semakin tinggi tahap, semakin menantang.</p>
</div>

<div class="row g-3">
    @forelse($levels as $level)
        @php
            $best = $bestAttempts->get($level->id);
            $isPassed = $passedLevelIds->contains($level->id);
            $isUnlocked = $unlocked[$level->id] ?? false;
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 {{ $isUnlocked ? '' : 'opacity-75' }}">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary">Tahap {{ $level->level_number }}</span>
                        @if($isPassed)
                            <span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Lulus</span>
                        @elseif(!$isUnlocked)
                            <span class="badge bg-secondary"><i class="bi bi-lock-fill me-1"></i>Terkunci</span>
                        @endif
                    </div>

                    <h5 class="fw-bold">{{ $level->name }}</h5>
                    <p class="text-muted small flex-grow-1">{{ $level->description }}</p>

                    <div class="mb-2">
                        <code class="fs-6">{{ strtoupper($level->allowed_keys) }}</code>
                    </div>

                    <!-- Aturan singkat -->
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @if($level->hasTimeLimit())
                            <span class="badge bg-primary-subtle text-primary-emphasis"><i class="bi bi-stopwatch me-1"></i>{{ $level->time_limit_seconds }} dtk</span>
                        @endif
                        @unless($level->allow_backspace)
                            <span class="badge bg-danger-subtle text-danger-emphasis"><i class="bi bi-backspace me-1"></i>Tanpa backspace</span>
                        @endunless
                        @unless($level->allow_space)
                            <span class="badge bg-danger-subtle text-danger-emphasis"><i class="bi bi-space me-1"></i>Tanpa spasi</span>
                        @endunless
                    </div>

                    <!-- Syarat lulus -->
                    @if($level->hasPassCriteria())
                        <div class="small text-muted mb-2">
                            <i class="bi bi-trophy-fill text-warning me-1"></i> Syarat lulus:
                            @if($level->min_wpm > 0) ≥{{ $level->min_wpm }} WPM @endif
                            @if($level->min_accuracy > 0) · benar ≥{{ $level->min_accuracy }}% @endif
                            @if($level->max_error_percent < 100) · salah ≤{{ $level->max_error_percent }}% @endif
                        </div>
                    @endif

                    @if($best)
                        <div class="small text-muted mb-3">
                            <i class="bi bi-bar-chart-fill me-1"></i>
                            Rekor: <strong>{{ $best->wpm }} WPM</strong> ·
                            {{ $best->correct_words }} benar / {{ $best->wrong_words }} salah
                        </div>
                    @endif

                    @if($isUnlocked)
                        <a href="{{ route('learner.typing.show', $level->id) }}" class="btn {{ $isPassed ? 'btn-outline-primary' : 'btn-primary' }} mt-auto">
                            <i class="bi bi-play-fill me-1"></i> {{ $best ? 'Latihan Lagi' : 'Mulai Latihan' }}
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
                <i class="bi bi-keyboard display-4 d-block mb-2 opacity-50"></i>
                Belum ada tahap latihan tersedia.
            </div>
        </div>
    @endforelse
</div>

@endsection
