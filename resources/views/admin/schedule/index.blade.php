@extends('layouts.admin')

@section('title', 'Jadwal Pelajaran')

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

    <!-- Mata Pelajaran -->
    <div class="sticky-top bg-white shadow-sm py-2 mb-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Mata Pelajaran</h5>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah
            </button>
        </div>
    </div>

    <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm mb-4" style="max-height: 260px;">
        <table class="table table-sm table-compact table-bordered table-hover bg-white mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 1%;">No.</th>
                    <th>Nama</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $subject->name }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-secondary btn-sm rounded-pill px-2 py-1 me-1"
                                data-bs-toggle="modal" data-bs-target="#editSubjectModal{{ $subject->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST"
                                onsubmit="return confirm('Hapus mata pelajaran ini?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm rounded-pill px-2 py-1">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="editSubjectModal{{ $subject->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border border-1 border-primary rounded-4 shadow">
                                <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header py-2 px-3">
                                        <h5 class="modal-title">Edit Mata Pelajaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="name" class="form-control" value="{{ $subject->name }}" required>
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
                    <tr><td colspan="3" class="text-center text-muted">Belum ada mata pelajaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Jadwal Pelajaran -->
    <div class="sticky-top bg-white shadow-sm py-2 mb-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">Jadwal Pelajaran</h5>
            <div class="d-flex gap-2">
                <form method="GET" class="d-flex">
                    <select name="kelas" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="semua" @selected($kelas === 'semua' || !$kelas)>Semua Kelas</option>
                        @foreach($gradeLevels as $gradeLevel)
                            <option value="{{ $gradeLevel->name }}" @selected($kelas === $gradeLevel->name)>{{ $gradeLevel->name }}</option>
                        @endforeach
                    </select>
                </form>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addJamPelajaranModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Jam Pelajaran
                </button>
            </div>
        </div>
    </div>

    @forelse(\App\Models\JamPelajaran::HARI_LIST as $hari)
        @if($jadwal->has($hari))
            <h6 class="text-uppercase text-muted mt-3 mb-1">{{ ucfirst($hari) }}</h6>
            <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm mb-2">
                <table class="table table-sm table-compact table-bordered table-hover bg-white mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 1%;">Jam ke</th>
                            <th>Waktu</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwal[$hari] as $jp)
                            <tr>
                                <td>{{ $jp->jam_ke }}</td>
                                <td>{{ \Carbon\Carbon::parse($jp->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($jp->jam_selesai)->format('H:i') }}</td>
                                <td>{{ $jp->grade_level }}</td>
                                <td>{{ $jp->subject->name }}</td>
                                <td>{{ $jp->guru->name }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-2 py-1 me-1"
                                        data-bs-toggle="modal" data-bs-target="#editJamPelajaranModal{{ $jp->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.jam-pelajaran.destroy', $jp->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus jam pelajaran ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm rounded-pill px-2 py-1">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editJamPelajaranModal{{ $jp->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border border-1 border-primary rounded-4 shadow">
                                        <form action="{{ route('admin.jam-pelajaran.update', $jp->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header py-2 px-3">
                                                <h5 class="modal-title">Edit Jam Pelajaran</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Kelas</label>
                                                        <select name="grade_level" class="form-select" required>
                                                            @foreach($gradeLevels as $gradeLevel)
                                                                <option value="{{ $gradeLevel->name }}" @selected($jp->grade_level === $gradeLevel->name)>{{ $gradeLevel->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Hari</label>
                                                        <select name="hari" class="form-select" required>
                                                            @foreach(\App\Models\JamPelajaran::HARI_LIST as $h)
                                                                <option value="{{ $h }}" @selected($jp->hari === $h)>{{ ucfirst($h) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Jam ke</label>
                                                        <input type="number" name="jam_ke" min="1" max="20" class="form-control" value="{{ $jp->jam_ke }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Mulai</label>
                                                        <input type="time" name="jam_mulai" class="form-control" value="{{ \Carbon\Carbon::parse($jp->jam_mulai)->format('H:i') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Selesai</label>
                                                        <input type="time" name="jam_selesai" class="form-control" value="{{ \Carbon\Carbon::parse($jp->jam_selesai)->format('H:i') }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Mata Pelajaran</label>
                                                        <select name="subject_id" class="form-select" required>
                                                            @foreach($subjects as $subject)
                                                                <option value="{{ $subject->id }}" @selected($jp->subject_id === $subject->id)>{{ $subject->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Guru</label>
                                                        <select name="guru_id" class="form-select" required>
                                                            @foreach($gurus as $guru)
                                                                <option value="{{ $guru->id }}" @selected($jp->guru_id === $guru->id)>{{ $guru->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @empty
    @endforelse

    @if($jadwal->isEmpty())
        <div class="alert alert-light border text-muted">Belum ada jadwal pelajaran.</div>
    @endif
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-1 border-primary rounded-4 shadow">
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-lg"></i> Tambah Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Matematika" required>
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

<!-- Add Jam Pelajaran Modal -->
<div class="modal fade" id="addJamPelajaranModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-1 border-primary rounded-4 shadow">
            <form action="{{ route('admin.jam-pelajaran.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-lg"></i> Tambah Jam Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kelas</label>
                            <select name="grade_level" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Kelas --</option>
                                @foreach($gradeLevels as $gradeLevel)
                                    <option value="{{ $gradeLevel->name }}">{{ $gradeLevel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hari</label>
                            <select name="hari" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Hari --</option>
                                @foreach(\App\Models\JamPelajaran::HARI_LIST as $h)
                                    <option value="{{ $h }}">{{ ucfirst($h) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jam ke</label>
                            <input type="number" name="jam_ke" min="1" max="20" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Mapel --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guru</label>
                            <select name="guru_id" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Guru --</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
