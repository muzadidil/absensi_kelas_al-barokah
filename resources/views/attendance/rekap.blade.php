@extends('layouts.admin')

@section('title', 'Rekap Absensi')

@push('styles')
<style>
    .rekap-toolbar {
        background: #fff;
        border-radius: var(--lems-radius);
        box-shadow: var(--lems-shadow-sm);
        padding: 1.25rem 1.5rem;
    }
    .rekap-toolbar label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--lems-ink-muted);
        margin-bottom: 0.35rem;
    }
    .rekap-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin: 1.25rem 0;
    }
    .rekap-summary .chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #fff;
        border-radius: 999px;
        padding: 0.5rem 1rem;
        box-shadow: var(--lems-shadow-sm);
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--lems-ink);
    }
    .rekap-summary .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .rekap-card {
        background: #fff;
        border-radius: var(--lems-radius);
        box-shadow: var(--lems-shadow-sm);
        overflow: hidden;
    }
    .rekap-table-wrap {
        overflow: auto;
        max-height: 70vh;
    }
    table.rekap-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        font-size: 0.875rem;
    }
    table.rekap-table thead th {
        position: sticky;
        top: 0;
        z-index: 3;
        background: #f8f9fc;
        color: var(--lems-ink);
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        padding: 0.85rem 0.9rem;
        border-bottom: 1px solid #e9ecf3;
        white-space: nowrap;
        text-align: center;
    }
    table.rekap-table thead th .subject-name {
        display: block;
        font-weight: 500;
        text-transform: none;
        color: var(--lems-ink-muted);
        font-size: 0.72rem;
        margin-top: 0.15rem;
    }
    table.rekap-table thead th:first-child,
    table.rekap-table tbody td:first-child {
        position: sticky;
        left: 0;
        z-index: 2;
        background: #f8f9fc;
        text-align: left;
        min-width: 200px;
    }
    table.rekap-table thead th:first-child {
        z-index: 4;
    }
    table.rekap-table tbody td {
        padding: 0.65rem 0.9rem;
        border-bottom: 1px solid #f0f1f6;
        text-align: center;
        vertical-align: middle;
    }
    table.rekap-table tbody tr:hover td {
        background: #fbfbfe;
    }
    table.rekap-table tbody tr:hover td:first-child {
        background: #f2f3fb;
    }
    .learner-name {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 600;
        color: var(--lems-ink);
    }
    .learner-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--lems-accent-soft);
        color: var(--lems-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.7rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .status-hadir { background: rgba(25, 135, 84, 0.12); color: #157347; }
    .status-sakit { background: rgba(253, 126, 20, 0.14); color: #b25600; }
    .status-izin  { background: rgba(13, 110, 253, 0.12); color: #0a58ca; }
    .status-alpa  { background: rgba(220, 53, 69, 0.12); color: #b02a37; }
    .status-empty { color: #c3c7d3; font-size: 0.9rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 pb-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">Rekap Absensi</h5>
    </div>

    <form method="GET" class="rekap-toolbar row g-3 align-items-end mb-0">
        <div class="col-6 col-md-3">
            <label class="d-block">Kelas</label>
            <select name="kelas" class="form-select">
                <option value="" disabled @selected(!$kelas)>-- Pilih Kelas --</option>
                @foreach($gradeLevels as $gradeLevel)
                    <option value="{{ $gradeLevel->name }}" @selected($kelas === $gradeLevel->name)>{{ $gradeLevel->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="d-block">Dari Tanggal</label>
            <input type="date" name="mulai" class="form-control" value="{{ $mulai }}">
        </div>
        <div class="col-6 col-md-3">
            <label class="d-block">Sampai Tanggal</label>
            <input type="date" name="selesai" class="form-control" value="{{ $selesai }}">
        </div>
        <div class="col-6 col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-filter-circle-fill me-1"></i> Tampilkan
            </button>
        </div>
    </form>

    @if(!$kelas)
        <div class="text-center text-muted py-5">
            <i class="bi bi-clipboard-data display-4 d-block mb-2 opacity-50"></i>
            Pilih kelas terlebih dahulu untuk melihat rekap absensi.
        </div>
    @elseif($columns->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox display-4 d-block mb-2 opacity-50"></i>
            Belum ada data absensi pada rentang tanggal ini.
        </div>
    @else
        @php
            $totalHadir = 0; $totalSakit = 0; $totalIzin = 0; $totalAlpa = 0;
            foreach ($matrix as $row) {
                foreach ($row as $att) {
                    match($att->status) {
                        'hadir' => $totalHadir++,
                        'sakit' => $totalSakit++,
                        'izin' => $totalIzin++,
                        'alpa' => $totalAlpa++,
                    };
                }
            }
        @endphp

        <div class="rekap-summary">
            <div class="chip"><span class="dot" style="background:#198754"></span> Hadir: {{ $totalHadir }}</div>
            <div class="chip"><span class="dot" style="background:#fd7e14"></span> Sakit: {{ $totalSakit }}</div>
            <div class="chip"><span class="dot" style="background:#0d6efd"></span> Izin: {{ $totalIzin }}</div>
            <div class="chip"><span class="dot" style="background:#dc3545"></span> Alpa: {{ $totalAlpa }}</div>
            <div class="chip"><span class="dot" style="background:#c3c7d3"></span> Murid: {{ $learners->count() }}</div>
        </div>

        <div class="rekap-card">
            <div class="rekap-table-wrap">
                <table class="rekap-table">
                    <thead>
                        <tr>
                            <th>Nama Murid</th>
                            @foreach($columns as $col)
                                <th>
                                    {{ $col['tanggal']->format('d/m/Y') }}<br>
                                    {{ \Carbon\Carbon::parse($col['jp']->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($col['jp']->jam_selesai)->format('H:i') }}
                                    · Jam ke-{{ $col['jp']->jam_ke }}
                                    <span class="subject-name">{{ $col['jp']->subject->name }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($learners as $learner)
                            <tr>
                                <td>
                                    <div class="learner-name">
                                        <span class="learner-avatar">{{ strtoupper(substr($learner->nama_lengkap, 0, 1)) }}</span>
                                        {{ $learner->nama_lengkap }}
                                    </div>
                                </td>
                                @foreach($columns as $col)
                                    @php $att = $matrix[$learner->id][$col['key']] ?? null; @endphp
                                    <td>
                                        @if($att)
                                            @php
                                                $icon = match($att->status) {
                                                    'hadir' => 'bi-check-circle-fill',
                                                    'sakit' => 'bi-thermometer-half',
                                                    'izin' => 'bi-file-earmark-text-fill',
                                                    'alpa' => 'bi-x-circle-fill',
                                                };
                                                $label = match($att->status) {
                                                    'hadir' => 'Hadir',
                                                    'sakit' => 'Sakit',
                                                    'izin' => 'Izin',
                                                    'alpa' => 'Alpa',
                                                };
                                            @endphp
                                            <span class="status-pill status-{{ $att->status }}"
                                                  @if($att->keterangan) title="{{ $att->keterangan }}" data-bs-toggle="tooltip" @endif>
                                                <i class="bi {{ $icon }}"></i> {{ $label }}
                                            </span>
                                        @else
                                            <span class="status-empty">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
</script>
@endsection
