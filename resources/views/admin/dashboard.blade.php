@extends('layouts.admin')

@section('title', 'Sistem Absensi Kelas Al-Barokah')

@section('content')

    @push('styles')
        <style>
            .stat-card {
                border: none;
                border-radius: 1rem;
                box-shadow: var(--lems-shadow-sm);
                transition: transform 0.18s ease, box-shadow 0.18s ease;
            }
            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: var(--lems-shadow-md);
            }
            .stat-card .card-body {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                padding: 1.25rem 1.4rem;
            }
            .stat-label {
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: var(--lems-ink-muted);
            }
            .stat-value {
                font-size: 1.85rem;
                font-weight: 700;
                color: var(--lems-ink);
            }
            .stat-icon {
                flex-shrink: 0;
                width: 48px;
                height: 48px;
                border-radius: 0.85rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.3rem;
            }
            .stat-icon-users { color: #0d6efd; background: rgba(13, 110, 253, 0.12); }
            .stat-icon-learners { color: #198754; background: rgba(25, 135, 84, 0.12); }
            .stat-icon-guru { color: #6f42c1; background: rgba(111, 66, 193, 0.12); }
            .stat-icon-mails { color: #fd7e14; background: rgba(253, 126, 20, 0.12); }
            .stat-icon-attendance { color: #20c997; background: rgba(32, 201, 151, 0.12); }

            .chart-card {
                border: none;
                border-radius: 1rem;
                box-shadow: var(--lems-shadow-sm);
            }
            .chart-card .card-title {
                font-size: 0.95rem;
                font-weight: 600;
                color: var(--lems-ink);
            }
        </style>
    @endpush

    <div class="row g-3 mt-1">
        <!-- Total Users -->
        <div class="col-md-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label mb-2">Total Pengguna</p>
                        <p class="stat-value mb-0">{{ $userCount }}</p>
                    </div>
                    <div class="stat-icon stat-icon-users">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Learners -->
        <div class="col-md-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label mb-2">Total Murid</p>
                        <p class="stat-value mb-0">{{ $learnerCount }}</p>
                    </div>
                    <div class="stat-icon stat-icon-learners">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="col-md-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label mb-2">Total Guru</p>
                        <p class="stat-value mb-0">{{ $guruCount }}</p>
                    </div>
                    <div class="stat-icon stat-icon-guru">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mail Logs -->
        <div class="col-md-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label mb-2">Total Log Email</p>
                        <p class="stat-value mb-0">{{ $mailLogCount }}</p>
                    </div>
                    <div class="stat-icon stat-icon-mails">
                        <i class="bi bi-envelope-paper-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Attendance Logs -->
        <div class="col-md-6 col-xl-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label mb-2">Total Absensi</p>
                        <p class="stat-value mb-0">{{ $attendanceCount }}</p>
                    </div>
                    <div class="stat-icon stat-icon-attendance">
                        <i class="bi bi-clipboard2-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-3">
        <div class="col-md-6">
            <div class="card chart-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Distribusi Murid vs Guru</h5>
                    <canvas id="userChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card chart-card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Log Email</h5>
                    <canvas id="logChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="progress mt-4" style="height: 6px; border-radius: 999px; background-color: rgba(16,24,40,0.06);">
        <div class="progress-bar" role="progressbar" style="width: {{ $learnerCount / max(1, $userCount) * 100 }}%; background-color: var(--lems-accent); border-radius: 999px;"></div>
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
                    backgroundColor: ['#2a78d6', '#eb6834'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 16 } } }
            }
        });

        const logChart = new Chart(document.getElementById('logChart'), {
            type: 'bar',
            data: {
                labels: ['Email'],
                datasets: [{
                    label: 'Total',
                    data: [{{ $mailLogCount }}],
                    backgroundColor: ['#eb6834'],
                    borderRadius: 6,
                    maxBarThickness: 56
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(16,24,40,0.06)' } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    </script>

@endpush
