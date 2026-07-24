@extends($layout ?? 'layouts.admin')

@section('title', 'Absensi')

@section('content')
<div class="container-fluid px-2">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
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

    <h5 class="mb-3">Absensi Murid</h5>

    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label small mb-1">Tanggal</label>
            <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}" onchange="this.form.submit()">
        </div>
        <div class="col-auto">
            <label class="form-label small mb-1">Kelas</label>
            <select name="kelas" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="semua" @selected($kelas === 'semua' || !$kelas)>Semua Kelas</option>
                @foreach($gradeLevels as $gradeLevel)
                    <option value="{{ $gradeLevel->name }}" @selected($kelas === $gradeLevel->name)>{{ $gradeLevel->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if(!$hari)
        <div class="alert alert-warning">Tanggal yang dipilih adalah hari Minggu — tidak ada jadwal pelajaran.</div>
    @elseif($jamPelajaranList->isEmpty())
        <div class="alert alert-light border text-muted">Tidak ada jadwal pelajaran untuk hari {{ ucfirst($hari) }} pada kelas ini.</div>
    @else
        <div class="d-flex flex-wrap gap-2 mb-3">
            @foreach($jamPelajaranList as $jp)
                <a href="{{ route('attendance.index', ['tanggal' => $tanggal, 'kelas' => $kelas ?? 'semua', 'jam_pelajaran_id' => $jp->id]) }}"
                   class="btn btn-sm {{ $jamPelajaranId == $jp->id ? 'btn-primary' : 'btn-outline-primary' }}">
                    Jam ke-{{ $jp->jam_ke }} · {{ $jp->subject->name }} · {{ $jp->grade_level }}
                    ({{ \Carbon\Carbon::parse($jp->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($jp->jam_selesai)->format('H:i') }})
                </a>
            @endforeach
        </div>
    @endif

    @if($selectedJamPelajaran)
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">
                    {{ $selectedJamPelajaran->subject->name }} — Kelas {{ $selectedJamPelajaran->grade_level }}
                    — Jam ke-{{ $selectedJamPelajaran->jam_ke }}
                    ({{ \Carbon\Carbon::parse($selectedJamPelajaran->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($selectedJamPelajaran->jam_selesai)->format('H:i') }})
                    — Guru: {{ $selectedJamPelajaran->guru->name }}
                </h6>

                @if($learners->isEmpty())
                    <p class="text-muted">Belum ada murid di kelas ini.</p>
                @else
                    <form method="POST" action="{{ route('attendance.store') }}">
                        @csrf
                        <input type="hidden" name="jam_pelajaran_id" value="{{ $selectedJamPelajaran->id }}">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 1%;">No.</th>
                                        <th>Nama</th>
                                        <th style="width: 160px;">Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($learners as $learner)
                                        @php $existing = $existingAttendance->get($learner->id); @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $learner->nama_lengkap }}</td>
                                            <td>
                                                <select name="status[{{ $learner->id }}]" class="form-select form-select-sm status-select" data-learner="{{ $learner->id }}" required>
                                                    <option value="hadir" @selected(!$existing || $existing->status === 'hadir')>Hadir</option>
                                                    <option value="sakit" @selected($existing?->status === 'sakit')>Sakit</option>
                                                    <option value="izin" @selected($existing?->status === 'izin')>Izin</option>
                                                    <option value="alpa" @selected($existing?->status === 'alpa')>Alpa</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="keterangan[{{ $learner->id }}]"
                                                    class="form-control form-control-sm keterangan-input"
                                                    data-learner="{{ $learner->id }}"
                                                    value="{{ $existing->keterangan ?? '' }}"
                                                    placeholder="Keterangan (untuk Sakit/Izin)"
                                                    {{ (!$existing || in_array($existing->status, ['hadir', 'alpa'])) ? 'disabled' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('.status-select').forEach(function (select) {
        select.addEventListener('change', function () {
            const learnerId = this.dataset.learner;
            const keteranganInput = document.querySelector('.keterangan-input[data-learner="' + learnerId + '"]');
            const needsKeterangan = this.value === 'sakit' || this.value === 'izin';
            keteranganInput.disabled = !needsKeterangan;
            if (!needsKeterangan) {
                keteranganInput.value = '';
            }
        });
    });
</script>
@endsection
