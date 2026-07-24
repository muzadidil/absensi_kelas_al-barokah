@extends('layouts.admin')

@section('title', 'Rekap Absensi')

@section('content')
<div class="container-fluid px-2">

    <h5 class="mb-3">Rekap Absensi</h5>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label small mb-1">Kelas</label>
            <select name="kelas" class="form-select form-select-sm" required>
                <option value="" disabled @selected(!$kelas)>-- Pilih Kelas --</option>
                @foreach($gradeLevels as $gradeLevel)
                    <option value="{{ $gradeLevel->name }}" @selected($kelas === $gradeLevel->name)>{{ $gradeLevel->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label small mb-1">Dari Tanggal</label>
            <input type="date" name="mulai" class="form-control form-control-sm" value="{{ $mulai }}">
        </div>
        <div class="col-auto">
            <label class="form-label small mb-1">Sampai Tanggal</label>
            <input type="date" name="selesai" class="form-control form-control-sm" value="{{ $selesai }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
        </div>
    </form>

    @if(!$kelas)
        <div class="alert alert-light border text-muted">Pilih kelas terlebih dahulu untuk melihat rekap.</div>
    @elseif($columns->isEmpty())
        <div class="alert alert-light border text-muted">Belum ada data absensi pada rentang tanggal ini.</div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-bordered bg-white text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-start" style="min-width: 180px;">Nama / Tanggal</th>
                        @foreach($columns as $col)
                            <th style="min-width: 110px; font-size: 0.75rem;">
                                {{ $col['tanggal']->format('d/m/Y') }}<br>
                                {{ \Carbon\Carbon::parse($col['jp']->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($col['jp']->jam_selesai)->format('H:i') }}<br>
                                Jam ke-{{ $col['jp']->jam_ke }} ({{ $col['jp']->subject->name }})
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($learners as $learner)
                        <tr>
                            <td class="text-start">{{ $learner->nama_lengkap }}</td>
                            @foreach($columns as $col)
                                @php $att = $matrix[$learner->id][$col['key']] ?? null; @endphp
                                <td>
                                    @if($att)
                                        @php
                                            $badge = match($att->status) {
                                                'hadir' => 'success',
                                                'sakit' => 'warning',
                                                'izin' => 'info',
                                                'alpa' => 'danger',
                                            };
                                            $label = match($att->status) {
                                                'hadir' => 'Hadir',
                                                'sakit' => 'Sakit',
                                                'izin' => 'Izin',
                                                'alpa' => 'Alpa',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badge }}" title="{{ $att->keterangan }}">{{ $label }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
