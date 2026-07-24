@extends('layouts.admin')

@section('title', 'Raport - ' . $learner->nama_lengkap)

@section('content')
<div class="container-fluid px-2">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">{{ $learner->nama_lengkap }}</h4>
            <p class="text-muted mb-0">Kelas {{ $learner->grade_level }}</p>
        </div>
        <a href="{{ route('admin.raport.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Raport
        </a>
    </div>

    <!-- Kuis Pilihan Ganda (kegigihan) -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-ui-checks me-1"></i> Kuis Pilihan Ganda</h6>
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
                        <div class="text-muted small">Predikat Kuis</div>
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
            @endif
        </div>
    </div>
</div>
@endsection
