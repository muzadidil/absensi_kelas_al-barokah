@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">🧑‍💼 Welcome, {{ Auth::user()->name }}</h2>
        <p class="text-muted">This is your Guru Dashboard. Manage your tasks and records here.</p>
    </div>

    <div class="row g-4">
        <!-- Attendance Log -->
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-1 text-primary mb-3"></i>
                    <h5 class="card-title">Attendance Log</h5>
                    <p class="card-text">Review your attendance records or <br>log entries.</p>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-primary w-100">View Attendance</a>
                </div>
            </div>
        </div>

        <!-- Profile -->
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-gear fs-1 text-success mb-3"></i>
                    <h5 class="card-title">My Profile</h5>
                    <p class="card-text">Update your personal details and account information.</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-success w-100">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
