@extends('layouts.learner')

@section('title', 'Raport Saya')

@section('content')

    @push('styles')
        <style>
            .stat-card {
                border: none;
                border-radius: 1rem;
                box-shadow: var(--lems-shadow-sm);
            }
        </style>
    @endpush

    <div class="mb-3">
        <h4 class="fw-bold mb-1"><i class="bi bi-file-earmark-bar-graph me-1"></i> Raport Saya</h4>
        <p class="text-muted mb-0">Ringkasan perjuanganmu di Kuis Pilihan Ganda.</p>
    </div>

    <!-- Kuis Pilihan Ganda (fokus kegigihan) -->
    <div class="card stat-card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-1"><i class="bi bi-ui-checks me-1"></i> Kuis Pilihan Ganda</h6>
            <p class="text-muted small mb-3">Makin sering coba, makin jago. Yang penting kamu terus berjuang! 💪</p>
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="text-center p-2 rounded" style="background:#f8f9fc;">
                        <div class="text-muted small">Tahap Tembus</div>
                        <div class="fs-4 fw-bold">{{ $kuis['tahap_tembus'] }} / {{ $kuis['total_tahap'] }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="text-center p-2 rounded" style="background:#f8f9fc;">
                        <div class="text-muted small">Progres</div>
                        <div class="fs-4 fw-bold">{{ $kuis['progres_persen'] }}%</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="text-center p-2 rounded" style="background:#f8f9fc;">
                        <div class="text-muted small">Total Percobaan</div>
                        <div class="fs-4 fw-bold text-primary">{{ $kuis['total_percobaan'] }}×</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="text-center p-2 rounded" style="background:#f8f9fc;">
                        <div class="text-muted small">Predikat</div>
                        <div class="fs-6 fw-bold mt-1">
                            <span class="badge {{ match($kuis['predikat']) {
                                'Tuntas' => 'bg-success',
                                'Baik' => 'bg-primary',
                                'Berkembang' => 'bg-info text-dark',
                                'Mulai Jalan' => 'bg-warning text-dark',
                                default => 'bg-secondary',
                            } }}">{{ $kuis['predikat'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @if($kuis['total_tahap'] > 0)
                <div class="progress mt-3" style="height: 8px; border-radius: 999px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $kuis['progres_persen'] }}%;"></div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('learner.quiz.index') }}" class="btn btn-primary">
                        <i class="bi bi-play-fill me-1"></i> Lanjut Kerjakan Kuis
                    </a>
                </div>
            @else
                <p class="text-muted text-center mt-3 mb-0">Belum ada kuis tersedia.</p>
            @endif
        </div>
    </div>
@endsection
