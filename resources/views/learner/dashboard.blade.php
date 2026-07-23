@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">👋 Selamat Datang, {{ Auth::user()->name }}</h2>
        <p class="text-muted">Ini adalah Dasbor Murid Anda. Akses fitur dan info terbaru di bawah ini.</p>
    </div>

    <div class="row g-4">
        <!-- Attendance -->
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard2-check fs-1 text-primary mb-3"></i>
                    <h5 class="card-title">Absensi</h5>
                    <p class="card-text">Catat absensi harian Anda.</p>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-primary w-100">Buka</a>
                </div>
            </div>
        </div>

        <!-- Profile -->
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle fs-1 text-success mb-3"></i>
                    <h5 class="card-title">Profil Saya</h5>
                    <p class="card-text">Lihat atau perbarui data <br>murid Anda.</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-success w-100">Ke Profil</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
