@extends('layouts.admin')

@section('title', 'Register User | Sistem Absensi Kelas Al-Barokah')

@section('content')
<div class="container mt-3">
  @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif

  @if(session('emailSuccess'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('emailSuccess') }}',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true
        });
    </script>
  @endif

  <h4 class="mb-4 d-flex align-items-center gap-2">
      <i class="bi bi-person-plus-fill"></i>
      Register New User
  </h4>

  <form method="POST" action="{{ route('admin.register.user') }}" id="registerUserForm">
      @csrf

      <div class="row">
          <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Name</label>
              <input type="text" name="name" class="form-control border border-secondary-subtle" placeholder="Enter full name" required>
          </div>

          <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" name="email" class="form-control border border-secondary-subtle" placeholder="example@email.com" required>
          </div>
      </div>

      <div class="row">
          <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Password</label>
              <input type="password" name="password" class="form-control border border-secondary-subtle" placeholder="Enter password" required>
          </div>

          <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control border border-secondary-subtle" placeholder="Re-enter password" required>
          </div>

          <div class="col-md-4 mb-3">
              <label class="form-label fw-semibold">Role</label>
              <select name="role" class="form-select border border-secondary-subtle" required>
                  <option value="">-- Select Role --</option>
                  <option value="learner">Learner</option>
                  <option value="guru">Guru</option>
                  <option value="admin">Admin</option>
              </select>
          </div>
      </div>

      <div class="d-flex justify-content-end mt-3">
          <button type="submit" id="submitBtn" class="btn btn-primary d-flex align-items-center gap-2">
              <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="submitSpinner"></span>
              <span id="submitText"><i class="bi bi-save2 me-1"></i>Register User</span>
          </button>
      </div>
  </form>
</div>

@push('scripts')
<script>
    document.getElementById('registerUserForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        const spinner = document.getElementById('submitSpinner');
        const text = document.getElementById('submitText');

        btn.disabled = true;
        spinner.classList.remove('d-none');
        text.innerHTML = 'Registering...';
    });
</script>
@endpush
@endsection
