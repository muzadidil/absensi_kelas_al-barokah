@extends('layouts.learner')

@section('title', 'Latihan Mengetik 10 Jari')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-keyboard me-1"></i> Latihan Mengetik 10 Jari</h4>
    <p class="text-muted mb-0">Pilih tahap latihan di bawah. Semakin tinggi tahap, semakin banyak tombol yang dilatih.</p>
</div>

<div class="row g-3">
    @forelse($levels as $level)
        @php $best = $bestAttempts->get($level->id); @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-primary align-self-start mb-2">Tahap {{ $level->level_number }}</span>
                    <h5 class="fw-bold">{{ $level->name }}</h5>
                    <p class="text-muted small flex-grow-1">{{ $level->description }}</p>
                    <div class="mb-3">
                        <code class="fs-6">{{ strtoupper($level->allowed_keys) }}</code>
                    </div>

                    @if($best)
                        <div class="small text-muted mb-3">
                            <i class="bi bi-trophy-fill text-warning me-1"></i>
                            Rekor terbaik: <strong>{{ $best->wpm }} WPM</strong>, akurasi {{ $best->accuracy }}%
                        </div>
                    @endif

                    <a href="{{ route('learner.typing.show', $level->id) }}" class="btn btn-primary mt-auto">
                        <i class="bi bi-play-fill me-1"></i> {{ $best ? 'Latihan Lagi' : 'Mulai Latihan' }}
                    </a>
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
