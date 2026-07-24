@extends('layouts.guru')

@section('title', 'Master Latihan Mengetik')

@push('styles')
<style>
    .rule-chip {
        display: inline-flex; align-items: center; gap: 0.3rem;
        font-size: 0.72rem; font-weight: 600;
        padding: 0.2rem 0.5rem; border-radius: 999px;
    }
    .rule-on  { background: rgba(25,135,84,.12); color: #157347; }
    .rule-off { background: rgba(220,53,69,.12); color: #b02a37; }
    .rule-info{ background: rgba(79,70,229,.10); color: #4f46e5; }
    .criteria-box { background: #f8f9fc; border: 1px solid var(--lems-border, #e7e8ee); border-radius: 0.6rem; padding: 0.85rem; }

    /* Pastikan isi modal tahap bisa di-scroll di layar pendek — body dibatasi
       tingginya lalu overflow, sehingga footer (Simpan/Batal) selalu terlihat. */
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
        <h5 class="fw-bold mb-1">Master Latihan Mengetik 10 Jari</h5>
        <p class="text-muted mb-0 small">Atur tahapan, tombol, mode ketik (backspace/spasi), dan syarat lulus. Murid harus lulus satu tahap untuk membuka tahap berikutnya.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLevelModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Tahap
    </button>
</div>

<div class="row g-3">
    @forelse($levels as $level)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary">Tahap {{ $level->level_number }}</span>
                        <div class="d-flex gap-1">
                            <form action="{{ route('guru.typing-levels.duplicate', $level->id) }}" method="POST"
                                onsubmit="return confirm('Salin tahap ini sebagai tahap baru?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary" title="Salin tahap ini"><i class="bi bi-files"></i></button>
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Edit tahap" data-bs-toggle="modal" data-bs-target="#editLevelModal{{ $level->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('guru.typing-levels.destroy', $level->id) }}" method="POST"
                                onsubmit="return confirm('Hapus tahap ini? Semua riwayat percobaan murid untuk tahap ini juga akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus tahap"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                        </div>
                    </div>
                    <h6 class="fw-bold">{{ $level->name }}</h6>
                    <p class="text-muted small mb-2">{{ $level->description }}</p>
                    <div class="mb-2">
                        <span class="text-muted small d-block mb-1">Tombol yang dilatih:</span>
                        <code class="fs-6">{{ strtoupper($level->allowed_keys) }}</code>
                    </div>
                    <div class="mb-2">
                        @php $wordCount = $level->word_bank ? count(array_filter(preg_split('/[\s,]+/u', trim($level->word_bank)))) : 0; @endphp
                        @if($wordCount > 0)
                            <span class="badge bg-success-subtle text-success-emphasis">{{ $wordCount }} kata di bank</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">Belum ada bank kata (teks acak)</span>
                        @endif
                    </div>

                    <!-- Mode ketik -->
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="rule-chip {{ $level->allow_backspace ? 'rule-on' : 'rule-off' }}">
                            <i class="bi bi-backspace"></i> Backspace {{ $level->allow_backspace ? 'boleh' : 'tidak' }}
                        </span>
                        <span class="rule-chip {{ $level->allow_space ? 'rule-on' : 'rule-off' }}">
                            <i class="bi bi-space"></i> Spasi {{ $level->allow_space ? 'boleh' : 'tidak' }}
                        </span>
                        @if($level->hasTimeLimit())
                            <span class="rule-chip rule-info"><i class="bi bi-stopwatch"></i> {{ $level->time_limit_seconds }} dtk</span>
                        @endif
                    </div>

                    <!-- Syarat lulus -->
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @if($level->hasPassCriteria())
                            <span class="rule-chip rule-info"><i class="bi bi-speedometer2"></i> ≥ {{ $level->min_wpm }} WPM</span>
                            <span class="rule-chip rule-info"><i class="bi bi-check2-circle"></i> Benar ≥ {{ $level->min_accuracy }}%</span>
                            <span class="rule-chip rule-info"><i class="bi bi-x-circle"></i> Salah ≤ {{ $level->max_error_percent }}%</span>
                        @else
                            <span class="rule-chip" style="background:#eef0f4;color:#6b7280;">Tanpa syarat lulus (bebas lanjut)</span>
                        @endif
                    </div>

                    <div class="text-muted small">
                        <i class="bi bi-clipboard-data me-1"></i> {{ $level->attempts_count }} kali percobaan oleh murid
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editLevelModal{{ $level->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('guru.typing-levels.update', $level->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Tahap {{ $level->level_number }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @include('guru.typing-levels._form-fields', ['level' => $level])
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
                <i class="bi bi-keyboard display-4 d-block mb-2 opacity-50"></i>
                Belum ada tahap latihan mengetik.
            </div>
        </div>
    @endforelse
</div>

<!-- Add Modal -->
<div class="modal fade" id="addLevelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('guru.typing-levels.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tahap Latihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('guru.typing-levels._form-fields', ['level' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
