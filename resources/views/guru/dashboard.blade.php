@extends('layouts.guru')

@section('title', 'Dasbor Guru')

@push('styles')
<style>
    .stat-card { position: relative; overflow: hidden; }
    .stat-card .card-body { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
    .stat-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: var(--fp-ink-muted); margin-bottom: 0.4rem; }
    .stat-value { font-size: 1.9rem; font-weight: 700; color: var(--fp-ink); line-height: 1; }
    .stat-icon { width: 46px; height: 46px; border-radius: 0.8rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .icon-indigo { background: rgba(79,70,229,.1); color: #4f46e5; }
    .icon-green { background: rgba(25,135,84,.12); color: #157347; }
    .icon-orange { background: rgba(253,126,20,.14); color: #b25600; }

    .schedule-row { display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1rem; border-radius: 0.75rem; }
    .schedule-row:hover { background: #f8f9fc; }
    .schedule-time { min-width: 88px; font-weight: 700; font-size: 0.85rem; color: var(--fp-primary); }
    .schedule-info .subject { font-weight: 600; font-size: 0.92rem; color: var(--fp-ink); }
    .schedule-info .meta { font-size: 0.78rem; color: var(--fp-ink-muted); }
</style>
@endpush

@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-1">👋 Selamat datang, {{ Auth::user()->name }}</h4>
    <p class="text-muted mb-0">Berikut ringkasan jadwal dan absensi Anda hari ini.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div>
                    <p class="stat-label mb-1">Jadwal Hari Ini</p>
                    <p class="stat-value mb-0">{{ $jadwalHariIni->count() }}</p>
                </div>
                <div class="stat-icon icon-indigo"><i class="bi bi-calendar-day-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div>
                    <p class="stat-label mb-1">Sudah Diabsen Hari Ini</p>
                    <p class="stat-value mb-0">{{ $sudahDiisiHariIni }} / {{ $jadwalHariIni->count() }}</p>
                </div>
                <div class="stat-icon icon-green"><i class="bi bi-check2-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div>
                    <p class="stat-label mb-1">Total Jam Mengajar / Minggu</p>
                    <p class="stat-value mb-0">{{ $totalJadwalMinggu }}</p>
                </div>
                <div class="stat-icon icon-orange"><i class="bi bi-calendar-week-fill"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-clock-history me-1"></i> Jadwal Mengajar Hari Ini
                    @if($hariIni)
                        <span class="text-muted fw-normal">({{ ucfirst($hariIni) }})</span>
                    @endif
                </h6>

                @if($jadwalHariIni->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x display-6 d-block mb-2 opacity-50"></i>
                        Tidak ada jadwal mengajar hari ini.
                    </div>
                @else
                    @foreach($jadwalHariIni as $jp)
                        <div class="schedule-row">
                            <div class="schedule-time">
                                {{ \Carbon\Carbon::parse($jp->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($jp->jam_selesai)->format('H:i') }}
                            </div>
                            <div class="schedule-info flex-grow-1">
                                <div class="subject">{{ $jp->subject->name }}</div>
                                <div class="meta">Kelas {{ $jp->grade_level }} · Jam ke-{{ $jp->jam_ke }}</div>
                            </div>
                            <a href="{{ route('attendance.index', ['kelas' => $jp->grade_level, 'jam_pelajaran_id' => $jp->id]) }}"
                               class="btn btn-sm btn-primary">
                                Isi Absensi
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill me-1"></i> Aksi Cepat</h6>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary mb-2 text-start">
                    <i class="bi bi-clipboard2-check-fill me-2"></i> Isi Absensi Murid
                </a>
                <a href="{{ route('guru.assignments.index') }}" class="btn btn-outline-primary mb-2 text-start">
                    <i class="bi bi-journal-text me-2"></i> Kelola Tugas
                </a>
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary text-start">
                    <i class="bi bi-person-gear me-2"></i> Edit Profil Saya
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
