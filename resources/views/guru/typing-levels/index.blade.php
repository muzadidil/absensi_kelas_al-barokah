@extends('layouts.guru')

@section('title', 'Master Latihan Mengetik')

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
        <p class="text-muted mb-0 small">Atur tahapan &amp; tombol yang dilatih di setiap tahap. Murid berlatih lewat menu "Latihan Mengetik".</p>
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
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editLevelModal{{ $level->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('guru.typing-levels.destroy', $level->id) }}" method="POST"
                                onsubmit="return confirm('Hapus tahap ini? Semua riwayat percobaan murid untuk tahap ini juga akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                            </form>
                        </div>
                    </div>
                    <h6 class="fw-bold">{{ $level->name }}</h6>
                    <p class="text-muted small mb-2">{{ $level->description }}</p>
                    <div class="mb-2">
                        <span class="text-muted small d-block mb-1">Tombol yang dilatih:</span>
                        <code class="fs-6">{{ strtoupper($level->allowed_keys) }}</code>
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-clipboard-data me-1"></i> {{ $level->attempts_count }} kali percobaan oleh murid
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editLevelModal{{ $level->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('guru.typing-levels.update', $level->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Tahap</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nomor Tahap</label>
                                <input type="number" name="level_number" class="form-control" value="{{ $level->level_number }}" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Tahap</label>
                                <input type="text" name="name" class="form-control" value="{{ $level->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tombol yang Dilatih <span class="text-muted small">(ketik huruf/simbol tanpa spasi, mis. asdfghjkl;)</span></label>
                                <input type="text" name="allowed_keys" class="form-control" value="{{ $level->allowed_keys }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-muted small">(opsional)</span></label>
                                <textarea name="description" class="form-control" rows="2">{{ $level->description }}</textarea>
                            </div>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('guru.typing-levels.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tahap Latihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Tahap</label>
                        <input type="number" name="level_number" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Tahap</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Tahap 4: Angka" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tombol yang Dilatih <span class="text-muted small">(ketik huruf/simbol tanpa spasi, mis. asdfghjkl;)</span></label>
                        <input type="text" name="allowed_keys" class="form-control" placeholder="asdfghjkl;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-muted small">(opsional)</span></label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
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
