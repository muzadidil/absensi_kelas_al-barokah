@extends('layouts.admin')

@section('title', 'Pantau Tugas')

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
            <h5 class="mb-0">Pantau Tugas</h5>

            <div class="d-flex gap-2 align-items-center">
                <!-- Filter Kelas -->
                <select id="filterKelas" class="form-select form-select-sm" style="width: auto;"
                    onchange="window.location.href = this.value ? '{{ route('admin.assignments.index') }}?kelas=' + encodeURIComponent(this.value) : '{{ route('admin.assignments.index') }}'">
                    <option value="" {{ (!$kelas || $kelas === 'semua') ? 'selected' : '' }}>Semua Kelas</option>
                    @foreach($gradeLevels as $gradeLevel)
                        <option value="{{ $gradeLevel->name }}" {{ $kelas === $gradeLevel->name ? 'selected' : '' }}>{{ $gradeLevel->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-2">
            <small class="text-muted">
                Tugas dibuat &amp; dikelola oleh Guru. Admin hanya memantau dan menilai jawaban essay.
                @if($kelas && $kelas !== 'semua')
                    Menampilkan {{ $assignments->count() }} tugas dari kelas {{ $kelas }}.
                @else
                    Menampilkan {{ $assignments->count() }} tugas.
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
                            <a href="{{ route('admin.assignments.show', $assignment->id) }}"
                                class="btn btn-outline-primary btn-sm rounded-pill d-inline-flex align-items-center justify-content-center gap-1 px-3 py-1">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Belum ada tugas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
