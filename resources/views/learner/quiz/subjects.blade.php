@extends('layouts.learner')

@section('title', 'Kuis Pilihan Ganda')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-ui-checks me-1"></i> Kuis Pilihan Ganda</h4>
    <p class="text-muted mb-0">Pilih mata pelajaran untuk mulai kuis berjenjangnya.</p>
</div>

<div class="row g-3">
    @forelse($subjects as $subject)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('learner.quiz.subject', $subject->id) }}" class="text-decoration-none text-reset">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold">{{ $subject->name }}</h5>
                        <div class="small text-muted mb-3">{{ $subject->levels_count }} tahap tersedia</div>
                        <span class="btn btn-primary mt-auto"><i class="bi bi-play-fill me-1"></i> Mulai</span>
                    </div>
                </div>
            </a>
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
