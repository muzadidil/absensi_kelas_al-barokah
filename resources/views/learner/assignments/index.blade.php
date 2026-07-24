<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tugas Saya | Sistem Absensi Kelas Al-Barokah</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4" style="max-width: 800px;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h2 class="fw-bold mb-1">📚 Tugas Saya</h2>
                <p class="text-muted mb-0">{{ $learner->nama_lengkap }} &mdash; Kelas {{ $learner->grade_level }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('learner.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
                <form method="POST" action="{{ route('learner.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @forelse($assignmentLearners as $al)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="mb-1">{{ $al->assignment->title }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-calendar-event me-1"></i>
                            @if($al->assignment->deadline)
                                Deadline: {{ $al->assignment->deadline->format('d/m/Y H:i') }}
                            @else
                                Tidak ada batas waktu
                            @endif
                        </p>
                        <span class="badge {{ $al->status === 'selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $al->status === 'selesai' ? 'Selesai' : 'Belum Dikerjakan' }}
                        </span>
                        @if($al->status === 'selesai')
                            <span class="text-muted small ms-2">Nilai: {{ $al->total_score ?? '-' }}</span>
                        @endif
                    </div>
                    <div>
                        @if($al->status === 'selesai')
                            <a href="{{ route('learner.assignments.show', $al->assignment_id) }}" class="btn btn-success">
                                <i class="bi bi-eye me-1"></i> Lihat Hasil
                            </a>
                        @else
                            <a href="{{ route('learner.assignments.show', $al->assignment_id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil-square me-1"></i> Kerjakan
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                Belum ada tugas untuk kamu.
            </div>
        @endforelse
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
