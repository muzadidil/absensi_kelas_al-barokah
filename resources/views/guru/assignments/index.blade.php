@extends('layouts.guru')

@section('title', 'Manajemen Tugas')

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

    <!-- Info Message -->
    @if(session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: '{{ session('info') }}',
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
            <h5 class="mb-0">Daftar Tugas</h5>

            <div class="d-flex gap-2 align-items-center">
                <!-- Filter Kelas -->
                <select id="filterKelas" class="form-select form-select-sm" style="width: auto;"
                    onchange="window.location.href = this.value ? '{{ route('guru.assignments.index') }}?kelas=' + encodeURIComponent(this.value) : '{{ route('guru.assignments.index') }}'">
                    <option value="" {{ (!$kelas || $kelas === 'semua') ? 'selected' : '' }}>Semua Kelas</option>
                    @foreach($gradeLevels as $gradeLevel)
                        <option value="{{ $gradeLevel->name }}" {{ $kelas === $gradeLevel->name ? 'selected' : '' }}>{{ $gradeLevel->name }}</option>
                    @endforeach
                </select>

                <!-- Add Assignment Button -->
                <a href="{{ route('guru.assignments.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-journal-plus me-1"></i> Buat Tugas Baru
                </a>
            </div>
        </div>

        <div class="mt-2">
            <small class="text-muted">
                @if($kelas && $kelas !== 'semua')
                    Menampilkan {{ $assignments->count() }} tugas dari kelas {{ $kelas }}
                @else
                    Menampilkan {{ $assignments->count() }} tugas
                @endif
            </small>
        </div>
    </div>

    <!-- Assignment Table -->
    <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm mt-2">
        <table class="table table-sm table-compact table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th style="width: 1%;">No.</th>
                    <th class="px-3 py-2 text-left">Judul</th>
                    <th class="px-3 py-2 text-left">Target</th>
                    <th class="px-3 py-2 text-left">Jumlah Soal</th>
                    <th class="px-3 py-2 text-left">Deadline</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($assignments as $assignment)
                    @php
                        $totalDitugaskan = $assignment->assignmentLearners->count();
                        $totalSelesai = $assignment->assignmentLearners->where('status', 'selesai')->count();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-1">{{ $loop->iteration }}</td>
                        <td class="px-3 py-1">{{ $assignment->title }}</td>
                        <td class="px-3 py-1">{{ $assignment->grade_level ?: 'Individual' }}</td>
                        <td class="px-3 py-1">{{ $assignment->questions->count() }}</td>
                        <td class="px-3 py-1">{{ $assignment->deadline ? $assignment->deadline->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-3 py-1">{{ $totalSelesai }} / {{ $totalDitugaskan }} selesai</td>
                        <td class="px-3 py-1 text-center">
                            <!-- Detail Button -->
                            <a href="{{ route('guru.assignments.show', $assignment->id) }}"
                                class="btn btn-outline-primary btn-sm rounded-pill d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1">
                                <i class="bi bi-eye"></i>
                            </a>

                            <!-- Edit Button -->
                            <button type="button"
                                class="btn btn-secondary btn-sm rounded-pill d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1"
                                data-bs-toggle="modal"
                                data-bs-target="#editAssignmentModal{{ $assignment->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <!-- Delete Form + Button -->
                            <form id="deleteAssignmentForm{{ $assignment->id }}" action="{{ route('guru.assignments.destroy', $assignment->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button"
                                class="btn btn-danger btn-sm rounded-pill d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1"
                                onclick="confirmDeleteAssignment({{ $assignment->id }}, @js($assignment->title))">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editAssignmentModal{{ $assignment->id }}" tabindex="-1" aria-labelledby="editAssignmentLabel{{ $assignment->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-centered">
                          <div class="modal-content border border-1 border-primary rounded-4 shadow">
                          <form action="{{ route('guru.assignments.update', $assignment->id) }}" method="POST">
                              @csrf
                              @method('PUT')
                              <div class="modal-header py-2 px-3">
                              <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="editAssignmentLabel{{ $assignment->id }}">
                                  <i class="bi bi-pencil-square"></i>
                                  Edit Tugas
                              </h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>

                              <div class="modal-body pt-1">
                              <div class="container-fluid">
                                  <div class="row g-3 mb-3 align-items-start">
                                  <div class="col-md-12">
                                      <label class="form-label">Judul Tugas</label>
                                      <input type="text" name="title" class="form-control" value="{{ $assignment->title }}" required>
                                  </div>
                                  </div>

                                  <div class="row g-3 mb-3 align-items-start">
                                  <div class="col-md-12">
                                      <label class="form-label">Deskripsi <span class="text-muted small">(opsional)</span></label>
                                      <textarea name="description" class="form-control" rows="3">{{ $assignment->description }}</textarea>
                                  </div>
                                  </div>

                                  <div class="row g-3 mb-3 align-items-start">
                                    <div class="col-md-6">
                                        <label class="form-label d-block">Target</label>
                                        <div class="d-flex gap-3 pt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="target_type" id="editTargetKelas{{ $assignment->id }}" value="kelas" onchange="toggleEditTargetFields({{ $assignment->id }})" {{ $assignment->grade_level ? 'checked' : '' }}>
                                                <label class="form-check-label" for="editTargetKelas{{ $assignment->id }}">Untuk Kelas</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="target_type" id="editTargetIndividu{{ $assignment->id }}" value="individu" onchange="toggleEditTargetFields({{ $assignment->id }})" {{ !$assignment->grade_level ? 'checked' : '' }}>
                                                <label class="form-check-label" for="editTargetIndividu{{ $assignment->id }}">Murid Tertentu</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 {{ $assignment->grade_level ? '' : 'd-none' }}" id="editGradeLevelField{{ $assignment->id }}">
                                        <label class="form-label">Pilih Kelas</label>
                                        <select name="grade_level" class="form-select">
                                        <option value="" disabled>Pilih Tingkat</option>
                                        @foreach($gradeLevels as $gradeLevel)
                                            <option value="{{ $gradeLevel->name }}" @selected($assignment->grade_level === $gradeLevel->name)>{{ $gradeLevel->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                  </div>

                                  <div class="row g-3 mb-3 align-items-start">
                                    <div class="col-md-6">
                                        <label class="form-label">Deadline <span class="text-muted small">(opsional)</span></label>
                                        <input type="datetime-local" name="deadline" class="form-control" value="{{ $assignment->deadline ? $assignment->deadline->format('Y-m-d\TH:i') : '' }}">
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
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Belum ada tugas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function confirmDeleteAssignment(id, title) {
        Swal.fire({
            title: 'Hapus Tugas?',
            html: 'Tugas <strong>' + title + '</strong> beserta semua soal, penugasan, dan jawaban murid akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteAssignmentForm' + id).submit();
            }
        });
    }

    function toggleEditTargetFields(id) {
        const isKelas = document.getElementById('editTargetKelas' + id).checked;
        document.getElementById('editGradeLevelField' + id).classList.toggle('d-none', !isKelas);
    }
</script>
@endsection
