@extends('layouts.admin')

@section('title', 'Attendance')

@push('styles')
    <style>
        .qr-form-container,
        .table-container {
            background: #ffffff;
            color: #111827;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .custom-gray-head {
            background-color: rgb(90, 88, 88) !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .attendance-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }
        .custom-toast-border {
            border: 1px solid rgb(47, 15, 253) !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2) !important;
        }
        .rounded-alert {
            border: 1px solid rgb(47, 15, 253) !important;
            border-radius: 12px !important;
        }
    </style>
@endpush

@section('content')

    <div class="text-center mb-1">
        <h2 class="fw-bold">Learner Attendance</h2>
        <p class="text-muted">Select a learner and log the session</p>
    </div>
    <div class="text-center">
        <h5 class="fw-semibold mb-1">Current Date and Time</h5>
        <div id="realtime-clock" class="fs-5 text-primary fw-bold"></div>
    </div>

    <div class="attendance-container">
        <!-- Attendance Form -->
        <div class="qr-form-container flex-fill">
            <form method="POST" action="{{ route('admin.attendance.store') }}" class="text-center">
                @csrf
                <div class="mb-4 text-start">
                    <label for="learner_id" class="form-label fw-semibold">Select Learner</label>
                    <select name="learner_id" id="learner_id" class="form-select" required>
                        <option value="" disabled selected>-- Choose a learner --</option>
                        @foreach ($learners as $learner)
                            <option value="{{ $learner->id }}">{{ $learner->lname }}, {{ $learner->fname }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4 text-start">
                    <label class="form-label">Select Session</label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach (['am_in' => 'AM IN', 'am_out' => 'AM OUT', 'pm_in' => 'PM IN', 'pm_out' => 'PM OUT'] as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="session" id="{{ $value }}" value="{{ $value }}" {{ $loop->first ? 'checked' : '' }}>
                                <label class="form-check-label small text-dark" for="{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Log Attendance</button>
            </form>
        </div>

        <!-- Attendance Table -->
        <div class="table-container flex-fill">
            <h6 class="text-muted mb-3">As of {{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="custom-gray-head">
                        <tr>
                            <th class="text-center" style="width: 1%;">No.</th>
                            <th>Name</th>
                            <th>AM IN</th>
                            <th>AM OUT</th>
                            <th>PM IN</th>
                            <th>PM OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $index => $attendance)
                            <tr>
                                <td class="text-center">{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}</td>
                                <td>{{ $attendance->learner->lname }}, {{ $attendance->learner->fname }}</td>
                                <td>{{ $attendance->am_in ? \Carbon\Carbon::parse($attendance->am_in)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->am_out ? \Carbon\Carbon::parse($attendance->am_out)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->pm_in ? \Carbon\Carbon::parse($attendance->pm_in)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->pm_out ? \Carbon\Carbon::parse($attendance->pm_out)->format('h:i A') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-warning">No attendance records for today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Showing {{ $attendances->firstItem() ?? 0 }} to {{ $attendances->lastItem() ?? 0 }} of {{ $attendances->total() }} entries
                </small>
                <div>{{ $attendances->links() }}</div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'custom-toast-border' }
            });
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: @json(session('warning')),
                confirmButtonColor: '#f0ad4e',
                timer: 2500,
                customClass: { popup: 'rounded-alert' }
            });
        });
    </script>
@endif

<script>
    function updateClock() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        };
        document.getElementById('realtime-clock').textContent = now.toLocaleString('en-US', options);
    }

    setInterval(updateClock, 1000);
    updateClock(); // initial call
</script>
@endpush
