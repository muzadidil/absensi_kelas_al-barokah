@extends('layouts.admin')

@section('title', 'Tingkat Kelas & Kelompok')

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    timer: 3000,
                    timerProgressBar: true,
                    confirmButtonText: 'OK'
                });
            });
        </script>
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

    <div class="row g-3">
        <!-- Tingkat Kelas -->
        <div class="col-md-6">
            <div class="sticky-top bg-white shadow-sm py-2 mb-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="mb-0">Tingkat Kelas</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeLevelModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah
                    </button>
                </div>
            </div>

            <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm">
                <table class="table table-sm table-compact table-bordered table-hover bg-white mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 1%;">No.</th>
                            <th>Nama</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gradeLevels as $gradeLevel)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $gradeLevel->name }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-2 py-1 me-1"
                                        data-bs-toggle="modal" data-bs-target="#editGradeLevelModal{{ $gradeLevel->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.grade-levels.destroy', $gradeLevel->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus tingkat kelas ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm rounded-pill px-2 py-1">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editGradeLevelModal{{ $gradeLevel->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border border-1 border-primary rounded-4 shadow">
                                        <form action="{{ route('admin.grade-levels.update', $gradeLevel->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header py-2 px-3">
                                                <h5 class="modal-title">Edit Tingkat Kelas</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label">Nama</label>
                                                <input type="text" name="name" class="form-control" value="{{ $gradeLevel->name }}" required>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-end">
                                                <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                                                    style="background-color: transparent !important; border-color: #0d6efd; color: #0d6efd;"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Belum ada tingkat kelas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kelompok -->
        <div class="col-md-6">
            <div class="sticky-top bg-white shadow-sm py-2 mb-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="mb-0">Kelompok</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah
                    </button>
                </div>
            </div>

            <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm">
                <table class="table table-sm table-compact table-bordered table-hover bg-white mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 1%;">No.</th>
                            <th>Nama</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $section->name }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-2 py-1 me-1"
                                        data-bs-toggle="modal" data-bs-target="#editSectionModal{{ $section->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.sections.destroy', $section->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus kelompok ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm rounded-pill px-2 py-1">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editSectionModal{{ $section->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border border-1 border-primary rounded-4 shadow">
                                        <form action="{{ route('admin.sections.update', $section->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header py-2 px-3">
                                                <h5 class="modal-title">Edit Kelompok</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label">Nama</label>
                                                <input type="text" name="name" class="form-control" value="{{ $section->name }}" required>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-end">
                                                <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                                                    style="background-color: transparent !important; border-color: #0d6efd; color: #0d6efd;"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Belum ada kelompok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Grade Level Modal -->
<div class="modal fade" id="addGradeLevelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-1 border-primary rounded-4 shadow">
            <form action="{{ route('admin.grade-levels.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i> Tambah Tingkat Kelas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Kelas 1 Iqro" required>
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                        style="background-color: transparent !important; border-color: #0d6efd; color: #0d6efd;"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-1 border-primary rounded-4 shadow">
            <form action="{{ route('admin.sections.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i> Tambah Kelompok
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Kelompok Ikhwan" required>
                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                        style="background-color: transparent !important; border-color: #0d6efd; color: #0d6efd;"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
