<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dasbor Murid | Sistem Absensi Kelas Al-Barokah</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold">👋 Selamat Datang, {{ $learner->nama_lengkap }}</h2>
            <p class="text-muted">Ini adalah Dasbor Murid Anda. Akses fitur dan info terbaru di bawah ini.</p>

            <form method="POST" action="{{ route('learner.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </form>
        </div>

        @php
            $tugasBelumCount = $learner->assignmentLearners()->where('status', 'belum')->count();
        @endphp

        <div class="row g-4 justify-content-center">
            <!-- Tugas Saya -->
            <div class="col-md-6">
                <div class="card border-0 shadow h-100">
                    <div class="card-body text-center position-relative">
                        @if($tugasBelumCount > 0)
                            <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger">
                                {{ $tugasBelumCount }}
                            </span>
                        @endif
                        <i class="bi bi-journal-text fs-1 text-primary mb-3"></i>
                        <h5 class="card-title">Tugas Saya</h5>
                        <p class="card-text">
                            @if($tugasBelumCount > 0)
                                Ada {{ $tugasBelumCount }} tugas yang belum dikerjakan.
                            @else
                                Tidak ada tugas yang menunggu dikerjakan.
                            @endif
                        </p>
                        <a href="{{ route('learner.assignments.index') }}" class="btn btn-primary w-100">Buka</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
