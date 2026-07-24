@extends('layouts.admin')

@section('title', 'Raport - ' . $learner->nama_lengkap)

@section('content')
<div class="container-fluid px-2">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">{{ $learner->nama_lengkap }}</h4>
            <p class="text-muted mb-0">Kelas {{ $learner->grade_level }}</p>
        </div>
        <a href="{{ route('admin.raport.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Raport
        </a>
    </div>

    <!-- Ringkasan -->
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Tugas</div>
                    <div class="fs-4 fw-bold">{{ $totalTugas }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-muted small">Selesai</div>
                    <div class="fs-4 fw-bold">{{ $totalSelesai }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-muted small">Rata-rata Nilai</div>
                    <div class="fs-4 fw-bold">{{ $rataRata }}%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="text-muted small">Predikat</div>
                    <div class="fs-5 fw-bold mt-1">
                        <span class="badge {{ match(true) {
                            $predikat === 'Sangat Baik' => 'bg-success',
                            $predikat === 'Baik' => 'bg-primary',
                            $predikat === 'Cukup' => 'bg-warning text-dark',
                            default => 'bg-danger',
                        } }}">{{ $predikat }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Tugas -->
    <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm">
        <table class="table table-sm table-compact table-bordered table-hover bg-white mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 1%;">No.</th>
                    <th class="px-3 py-2 text-left">Judul Tugas</th>
                    <th class="px-3 py-2 text-left">Tanggal Submit</th>
                    <th class="px-3 py-2 text-left">Nilai</th>
                    <th class="px-3 py-2 text-left">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignmentLearners as $al)
                    @php
                        $maxScore = $al->assignment->questions->sum('points');
                        $persentase = ($al->status === 'selesai' && $maxScore > 0)
                            ? round(($al->total_score / $maxScore) * 100, 1)
                            : null;
                    @endphp
                    <tr>
                        <td class="px-3 py-1">{{ $loop->iteration }}</td>
                        <td class="px-3 py-1">{{ $al->assignment->title }}</td>
                        <td class="px-3 py-1">{{ $al->submitted_at ? $al->submitted_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-3 py-1">
                            @if($al->status === 'selesai')
                                {{ $al->total_score ?? 0 }} / {{ $maxScore }}
                            @else
                                <span class="badge bg-secondary">Belum Selesai</span>
                            @endif
                        </td>
                        <td class="px-3 py-1">{{ $persentase !== null ? $persentase . '%' : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">Belum ada tugas untuk murid ini.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($assignmentLearners->isNotEmpty())
                <tfoot>
                    <tr class="table-light">
                        <td colspan="4" class="px-3 py-2 text-end fw-bold">RATA-RATA</td>
                        <td class="px-3 py-2 fw-bold">{{ $rataRata }}%</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
