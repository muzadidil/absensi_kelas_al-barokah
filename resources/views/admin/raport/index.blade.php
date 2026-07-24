@extends('layouts.admin')

@section('title', 'Raport Siswa')

@section('content')
<div class="container-fluid px-2">

    <!-- Sticky header -->
    <div class="sticky-top bg-white shadow-sm py-2 mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Raport Siswa</h5>

            <!-- Filter Kelas -->
            <select id="filterKelas" class="form-select form-select-sm" style="width: auto;"
                onchange="window.location.href = this.value ? '{{ route('admin.raport.index') }}?kelas=' + encodeURIComponent(this.value) : '{{ route('admin.raport.index') }}'">
                <option value="" {{ (!$kelas || $kelas === 'semua') ? 'selected' : '' }}>Semua Kelas</option>
                @foreach($gradeLevels as $gradeLevel)
                    <option value="{{ $gradeLevel->name }}" {{ $kelas === $gradeLevel->name ? 'selected' : '' }}>{{ $gradeLevel->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm">
        <table class="table table-sm table-compact table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th style="width: 1%;">No.</th>
                    <th class="px-3 py-2 text-left">Nama Siswa</th>
                    <th class="px-3 py-2 text-left">Kelas</th>
                    <th class="px-3 py-2 text-left">Tugas Selesai</th>
                    <th class="px-3 py-2 text-left">Rata-rata Nilai</th>
                    <th class="px-3 py-2 text-left">Predikat</th>
                    <th class="px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekap as $row)
                    <tr>
                        <td class="px-3 py-1">{{ $loop->iteration }}</td>
                        <td class="px-3 py-1">{{ $row['learner']->nama_lengkap }}</td>
                        <td class="px-3 py-1">{{ $row['learner']->grade_level }}</td>
                        <td class="px-3 py-1">{{ $row['jumlah_selesai'] }} / {{ $row['jumlah_total'] }}</td>
                        <td class="px-3 py-1">{{ $row['rata_rata'] }}%</td>
                        <td class="px-3 py-1">
                            <span class="badge {{ match(true) {
                                $row['predikat'] === 'Sangat Baik' => 'bg-success',
                                $row['predikat'] === 'Baik' => 'bg-primary',
                                $row['predikat'] === 'Cukup' => 'bg-warning text-dark',
                                default => 'bg-danger',
                            } }}">
                                {{ $row['predikat'] }}
                            </span>
                        </td>
                        <td class="px-3 py-1 text-center">
                            <a href="{{ route('admin.raport.show', $row['learner']->id) }}" class="btn btn-sm btn-primary rounded-pill">
                                <i class="bi bi-file-earmark-text me-1"></i> Lihat Raport
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Belum ada data murid.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
