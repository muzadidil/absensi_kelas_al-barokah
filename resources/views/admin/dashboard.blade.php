@extends('layouts.admin')

@section('title', 'Sistem Absensi Kelas Al-Barokah')

@section('content')

    @push('styles')
        <style>
            .card-border-left {
                border-left: 8px solid !important;
                border-radius: 0.75rem;
            }
            .card-users { border-color: #0d6efd !important; }
            .card-learners { border-color: #198754 !important; }
            .card-guru { border-color: #6f42c1 !important; }
            .card-mails { border-color: #fd7e14 !important; }
            .card-attendance { border-color: #20c997 !important; }

            .text-purple { color: #6f42c1 !important; }
            .text-teal { color: #20c997 !important; }
        </style>
    @endpush

    <div class="row g-3 mt-1">
        <!-- Total Users -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-users text-center shadow-sm">
                <div class="card-body text-primary">
                    <i class="bi bi-people-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Pengguna</h5>
                    <p class="display-6 mb-0">{{ $userCount }}</p>
                </div>
            </div>
        </div>

        <!-- Total Learners -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-learners text-center shadow-sm">
                <div class="card-body text-success">
                    <i class="bi bi-person-workspace display-6 mb-2"></i>
                    <h5 class="card-title">Total Murid</h5>
                    <p class="display-6 mb-0">{{ $learnerCount }}</p>
                </div>
            </div>
        </div>
        <!-- Total Guru -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-guru text-center shadow-sm">
                <div class="card-body text-purple">
                    <i class="bi bi-person-badge-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Guru</h5>
                    <p class="display-6 mb-0">{{ $guruCount }}</p>
                </div>
            </div>
        </div>

        <!-- Total Mail Logs -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-mails text-center shadow-sm">
                <div class="card-body text-warning">
                    <i class="bi bi-envelope-paper-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Log Email</h5>
                    <p class="display-6 mb-0">{{ $mailLogCount }}</p>
                </div>
            </div>
        </div>

        <!-- Total Attendance Logs -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-attendance text-center shadow-sm">
                <div class="card-body text-teal">
                    <i class="bi bi-clipboard2-check-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Absensi</h5>
                    <p class="display-6 mb-0">{{ $attendanceCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Distribusi Murid vs Guru</h5>
                    <canvas id="userChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Log Email</h5>
                    <canvas id="logChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="progress mt-3" style="height: 6px;">
        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $learnerCount / max(1, $userCount) * 100 }}%"></div>
    </div>
@endsection

@push('scripts')
    @if(session('emailSuccess'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Pengirim Email',
                text: '{{ session('emailSuccess') }}',
                confirmButtonColor: '#3085d6',
                timer: 4000,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        const userChart = new Chart(document.getElementById('userChart'), {
            type: 'doughnut',
            data: {
                labels: ['Murid', 'Guru'],
                datasets: [{
                    data: [{{ $learnerCount }}, {{ $guruCount }}],
                    backgroundColor: ['#198754', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        const logChart = new Chart(document.getElementById('logChart'), {
            type: 'bar',
            data: {
                labels: ['Email'],
                datasets: [{
                    label: 'Total',
                    data: [{{ $mailLogCount }}],
                    backgroundColor: ['#fd7e14']
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });
    </script>

@endpush
