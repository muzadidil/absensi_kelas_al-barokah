@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">🧑‍💼 Selamat Datang, {{ Auth::user()->name }}</h2>
        <p class="text-muted">Ini adalah Dasbor Guru Anda. Kelola tugas dan data Anda di sini.</p>
    </div>

    <div class="row g-4">
        <!-- Attendance Log -->
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-1 text-primary mb-3"></i>
                    <h5 class="card-title">Isi Absensi</h5>
                    <p class="card-text">Catat kehadiran murid untuk <br>jam pelajaran yang Anda ampu.</p>
                    <a href="{{ route('attendance.index') }}" class="btn btn-primary w-100">Isi Absensi</a>
                </div>
            </div>
        </div>

        <!-- Profile -->
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-gear fs-1 text-success mb-3"></i>
                    <h5 class="card-title">Profil Saya</h5>
                    <p class="card-text">Perbarui data pribadi dan informasi akun Anda.</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-success w-100">Edit Profil</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
