@extends('layouts.guru')

@section('title', 'Master Kuis Pilihan Ganda')

@push('styles')
<style>
    .rule-chip {
        display: inline-flex; align-items: center; gap: 0.3rem;
        font-size: 0.72rem; font-weight: 600;
        padding: 0.2rem 0.5rem; border-radius: 999px;
    }
    .rule-info { background: rgba(79,70,229,.10); color: #4f46e5; }
    .rule-warn { background: rgba(220,53,69,.12); color: #b02a37; }

    #addLevelModal .modal-body,
    [id^="editLevelModal"] .modal-body {
        max-height: calc(100vh - 190px);
        overflow-y: auto;
    }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-1">Master Kuis Pilihan Ganda</h5>
        <p class="text-muted mb-0 small">Kuis berjenjang ala game — soal satu per satu, salah sedikit ulang dari awal tahap. Murid harus lulus satu tahap untuk membuka tahap berikutnya.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLevelModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Tahap
    </button>
</div>

<div class="row g-3">
    @forelse($levels as $level)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary">Tahap {{ $level->level_number }}</span>
                        <div class="d-flex gap-1">
                            <form action="{{ route('guru.quiz-levels.duplicate', $level->id) }}" method="POST"
                                onsubmit="return confirm('Salin tahap ini (beserta semua soalnya) sebagai tahap baru?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary" title="Salin tahap ini"><i class="bi bi-files"></i></button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Edit info tahap" data-bs-toggle="modal" data-bs-target="#editLevelModal{{ $level->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('guru.quiz-levels.destroy', $level->id) }}" method="POST"
                                onsubmit="return confirm('Hapus tahap ini? Semua soal & riwayat percobaan murid ikut terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus tahap"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                        </div>
                    </div>

                    <h6 class="fw-bold">{{ $level->name }}</h6>
                    <p class="text-muted small flex-grow-1">{{ $level->description }}</p>

                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="rule-chip rule-info"><i class="bi bi-list-ol"></i> {{ $level->questions_count }} soal</span>
                        @if($level->reset_to_first_on_fail)
                            <span class="rule-chip rule-warn"><i class="bi bi-fire"></i> Mode Pamungkas</span>
                        @endif
                    </div>

                    <div class="text-muted small mb-3">
                        <i class="bi bi-clipboard-data me-1"></i> {{ $level->attempts_count }} kali percobaan oleh murid
                    </div>

                    <a href="{{ route('guru.quiz-levels.show', $level->id) }}" class="btn btn-outline-primary mt-auto">
                        <i class="bi bi-pencil-fill me-1"></i> Kelola Soal
                    </a>
                </div>
            </div>
        </div>

        <!-- Edit info tahap -->
        <div class="modal fade" id="editLevelModal{{ $level->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('guru.quiz-levels.update', $level->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Tahap {{ $level->level_number }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @include('guru.quiz-levels._level-fields', ['level' => $level])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center text-muted py-5">
                <i class="bi bi-ui-checks display-4 d-block mb-2 opacity-50"></i>
                Belum ada tahap kuis. Klik "Tambah Tahap" untuk mulai.
            </div>
        </div>
    @endforelse
</div>

<!-- Tambah tahap -->
<div class="modal fade" id="addLevelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('guru.quiz-levels.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tahap Kuis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('guru.quiz-levels._level-fields', ['level' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Kelola Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
