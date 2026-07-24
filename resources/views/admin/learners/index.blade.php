@extends('layouts.admin')

@section('title', 'Manajemen Murid')

@section('content')
<div class="container-fluid px-2">

    <!-- Success Message -->
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
 
    <!-- Sticky header -->
    <div class="sticky-top bg-white shadow-sm py-2 mb-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Murid</h5>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.class-settings.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-gear-fill me-1"></i> Kelola Tingkat Kelas & Tahun Ajaran
                </a>
                <!-- Add Learner Button -->
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLearnerModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Murid
                </button>
            </div>
        </div>
    </div>

    @if($gradeLevels->isEmpty() || $sections->isEmpty())
        <div class="alert alert-warning mt-2">
            Belum ada data Tingkat Kelas / Tahun Ajaran. Silakan
            <a href="{{ route('admin.class-settings.index') }}">tambahkan dulu di sini</a>
            sebelum menambah data murid.
        </div>
    @endif

    <!-- Learner Table -->
    <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm">
         <table class="table table-sm table-compact table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th style="width: 1%;">No.</th>
                    <th class="px-3 py-2 text-left">Nama</th>
                    <th class="px-3 py-2 text-left">Email</th>
                    <th class="px-3 py-2 text-left">Tingkat Kelas</th>
                    <th class="px-3 py-2 text-left">Tahun Ajaran</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($learners as $learner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-1">{{ $loop->iteration }}</td>
                        <td class="px-3 py-1">{{ $learner->fname }} {{ $learner->mname }} {{ $learner->lname }}</td>
                        <td class="px-3 py-1">{{ $learner->email ?: '-' }}</td>
                        <td class="px-3 py-1">{{ $learner->grade_level }}</td>
                        <td class="px-3 py-1">{{ $learner->section }}</td>
                        <td class="px-3 py-1 text-center">
                            <!-- Edit Button -->
                            <button type="button"
                                class="btn btn-secondary btn-sm rounded-pill d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1"
                                data-bs-toggle="modal"
                                data-bs-target="#editLearnerModal{{ $learner->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <!-- Delete Form -->
                            <form action="{{ route('admin.learners.destroy', $learner->id) }}" method="POST"
                                onsubmit="return confirm('Hapus murid ini?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm rounded-pill d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editLearnerModal{{ $learner->id }}" tabindex="-1" aria-labelledby="editLearnerLabel{{ $learner->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-centered">
                          <div class="modal-content border border-1 border-primary rounded-4 shadow">
                          <form action="{{ route('admin.learners.update', $learner->id) }}" method="POST">
                              @csrf
                              @method('PUT')
                              <div class="modal-header py-2 px-3">
                              <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="editLearnerLabel{{ $learner->id }}">
                                  <i class="bi bi-pencil-square"></i>
                                  Edit Murid
                              </h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>

                              <div class="modal-body pt-1">
                              <div class="container-fluid">
                                  <div class="row g-3 mb-3">
                                  <div class="col-md-4">
                                      <label class="form-label">Nama Depan</label>
                                      <input type="text" name="fname" class="form-control" value="{{ $learner->fname }}" required>
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label">Nama Tengah</label>
                                      <input type="text" name="mname" class="form-control" value="{{ $learner->mname }}">
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label">Nama Belakang</label>
                                      <input type="text" name="lname" class="form-control" value="{{ $learner->lname }}" required>
                                  </div>
                                  </div>

                                  <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Email <span class="text-muted small">(opsional)</span></label>
                                        <input type="email" name="email" class="form-control" value="{{ $learner->email }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tingkat Kelas</label>
                                        <select name="grade_level" class="form-select" required>
                                        <option disabled>Pilih Tingkat</option>
                                        @foreach($gradeLevels as $gradeLevel)
                                            <option value="{{ $gradeLevel->name }}" @selected($learner->grade_level === $gradeLevel->name)>{{ $gradeLevel->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tahun Ajaran</label>
                                        <select name="section" class="form-select" required>
                                        <option disabled>Pilih Tahun Ajaran</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->name }}" @selected($learner->section === $section->name)>{{ $section->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                  </div>
                              </div>
                              </div>

                              <div class="modal-footer d-flex justify-content-end">
                              <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                                      style="background-color: transparent !important; border-color: #0d6efd; color: #0d6efd;"
                                      data-bs-dismiss="modal">
                                      Batal
                                  </button>
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
</div>

<!-- Bootstrap Modal For Add -->
<div class="modal fade" id="addLearnerModal" tabindex="-1" aria-labelledby="addLearnerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-1 border-primary rounded-4 shadow"  style="z-index: 1055;">
      <form action="{{ route('admin.learners.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom-0">
            <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="addLearnerModalLabel">
                <i class="bi bi-person-plus-fill"></i>
                Tambah Murid
            </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body pt-0">
          <div class="container-fluid">

            <!-- First Row: Names -->
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="fname" class="form-label">Nama Depan</label>
                <input type="text" name="fname" class="form-control rounded-3" placeholder="Nama Depan" required>
              </div>

              <div class="col-md-4">
                <label for="mname" class="form-label">Nama Tengah</label>
                <input type="text" name="mname" class="form-control rounded-3" placeholder="Nama Tengah">
              </div>

              <div class="col-md-4">
                <label for="lname" class="form-label">Nama Belakang</label>
                <input type="text" name="lname" class="form-control rounded-3" placeholder="Nama Belakang" required>
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="email" class="form-label">Email <span class="text-muted small">(opsional)</span></label>
                <input type="email" name="email" class="form-control rounded-3" placeholder="Email">
              </div>
              <div class="col-md-4">
                <label for="grade_level" class="form-label">Tingkat Kelas</label>
                <select name="grade_level" class="form-select rounded-3" required>
                  <option value="" selected disabled>Pilih Tingkat</option>
                  @foreach($gradeLevels as $gradeLevel)
                      <option value="{{ $gradeLevel->name }}">{{ $gradeLevel->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-4">
                <label for="section" class="form-label">Tahun Ajaran</label>
                <select name="section" class="form-select rounded-3" required>
                  <option value="" selected disabled>Pilih Tahun Ajaran</option>
                  @foreach($sections as $section)
                      <option value="{{ $section->name }}">{{ $section->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

          </div>
        </div>

        <!-- Modal Footer Buttons -->
        <div class="modal-footer border-top-0 d-flex justify-content-end">
            <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                style="background-color: transparent !important; border-color: #0d6efd; color: #0d6efd;"
                data-bs-dismiss="modal">
                Batal
            </button>
          <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
