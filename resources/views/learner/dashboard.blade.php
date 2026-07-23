@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">👋 Welcome, {{ Auth::user()->name }}</h2>
        <p class="text-muted">This is your personalized Learner Dashboard. Access your tools and updates below.</p>
    </div>

    <div class="row g-4">
        <!-- Attendance -->
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard2-check fs-1 text-primary mb-3"></i>
                    <h5 class="card-title">Attendance</h5>
                    <p class="card-text">Log your daily attendance.</p>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-primary w-100">Open</a>
                </div>
            </div>
        </div>

        <!-- Profile -->
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle fs-1 text-success mb-3"></i>
                    <h5 class="card-title">My Profile</h5>
                    <p class="card-text">View or update your learner <br>account details.</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-success w-100">Go to Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
