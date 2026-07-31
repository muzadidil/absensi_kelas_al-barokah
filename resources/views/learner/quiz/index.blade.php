@extends('layouts.learner')

@section('title', 'Kuis Pilihan Ganda')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
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
    <a href="{{ route('learner.quiz.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i> Semua Mapel
    </a>
    <h4 class="fw-bold mb-1"><i class="bi bi-ui-checks me-1"></i> Kuis {{ $subject->name }}</h4>
    <p class="text-muted mb-0">Jawab benar untuk lanjut, salah = ulang dari awal tahap. Lulus satu tahap membuka tahap berikutnya. Semangat, makin sering coba makin jago! 💪</p>
</div>

<div class="row g-3">
    @forelse($levels as $level)
        @php
            $isPassed = $passedLevelIds->contains($level->id);
            $isUnlocked = $unlocked[$level->id] ?? false;
            $attempts = $attemptCounts[$level->id] ?? 0;
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

                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge bg-primary-subtle text-primary-emphasis"><i class="bi bi-list-ol me-1"></i>{{ $level->questions_count }} soal</span>
                        @if($level->reset_to_first_on_fail)
                            <span class="badge bg-danger-subtle text-danger-emphasis"><i class="bi bi-fire me-1"></i>Pamungkas</span>
                        @endif
                    </div>

                    @if($attempts > 0)
                        <div class="small text-muted mb-3">
                            <i class="bi bi-arrow-repeat me-1"></i> Sudah dicoba <strong>{{ $attempts }}×</strong>
                        </div>
                    @endif

                    @if($isUnlocked && $level->questions_count > 0)
                        <a href="{{ route('learner.quiz.play', $level->id) }}" class="btn {{ $isPassed ? 'btn-outline-primary' : 'btn-primary' }} mt-auto">
                            <i class="bi bi-play-fill me-1"></i> {{ $isPassed ? 'Ulangi' : 'Mulai' }}
                        </a>
                    @elseif($isUnlocked)
                        <button type="button" class="btn btn-secondary mt-auto" disabled>Belum ada soal</button>
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
                <i class="bi bi-ui-checks display-4 d-block mb-2 opacity-50"></i>
                Belum ada kuis tersedia.
            </div>
        </div>
    @endforelse
</div>

@endsection
