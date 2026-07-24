@extends('layouts.learner')

@section('title', 'Dasbor Siswa')

@section('content')

    @push('styles')
        <style>
            .stat-card {
                border: none;
                border-radius: 1rem;
                box-shadow: var(--lems-shadow-sm);
                transition: transform 0.18s ease, box-shadow 0.18s ease;
            }
            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: var(--lems-shadow-md);
            }
            .stat-card .card-body {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                padding: 1.25rem 1.4rem;
            }
            .stat-label {
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: var(--lems-ink-muted);
            }
            .stat-value {
                font-size: 1.85rem;
                font-weight: 700;
                color: var(--lems-ink);
            }
            .stat-icon {
                flex-shrink: 0;
                width: 48px;
                height: 48px;
                border-radius: 0.85rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.3rem;
            }
            .stat-icon-belum { color: #fd7e14; background: rgba(253, 126, 20, 0.12); }
            .stat-icon-selesai { color: #198754; background: rgba(25, 135, 84, 0.12); }
            .stat-icon-rata { color: #4f6bed; background: rgba(79, 107, 237, 0.12); }
        </style>
    @endpush

    <div class="mb-3">
        <h4 class="fw-bold mb-1">👋 Selamat Datang, {{ $learner->nama_lengkap }}</h4>
        <p class="text-muted mb-0">Ini adalah dasbor kamu. Pantau tugas dan nilai di sini.</p>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label mb-2">Belum Dikerjakan</p>
                        <p class="stat-value mb-0">{{ $belumCount }}</p>
                    </div>
                    <div class="stat-icon stat-icon-belum">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label mb-2">Tugas Selesai</p>
                        <p class="stat-value mb-0">{{ $selesaiCount }}</p>
                    </div>
                    <div class="stat-icon stat-icon-selesai">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label mb-2">Rata-rata Nilai</p>
                        <p class="stat-value mb-0">{{ $rataRata }}%</p>
                    </div>
                    <div class="stat-icon stat-icon-rata">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card stat-card">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><i class="bi bi-journal-text me-1"></i> Tugas Saya</h5>
                        <p class="text-muted mb-0">Lihat dan kerjakan tugas yang ditugaskan untukmu.</p>
                    </div>
                    <a href="{{ route('learner.assignments.index') }}" class="btn btn-primary">Buka Tugas</a>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card stat-card">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><i class="bi bi-keyboard me-1"></i> Latihan Mengetik 10 Jari</h5>
                        <p class="text-muted mb-0">Latih kecepatan &amp; akurasi mengetikmu bertahap.</p>
                    </div>
                    <a href="{{ route('learner.typing.index') }}" class="btn btn-primary">Mulai Latihan</a>
                </div>
            </div>
        </div>
    </div>
@endsection
