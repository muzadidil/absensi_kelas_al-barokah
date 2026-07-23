@extends('layouts.admin')

@section('title', 'Pengguna Terdaftar')

@section('content')
<div class="container">
    <!-- Loader Overlay -->
    <div id="loader">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
            <span class="visually-hidden">Memuat...</span>
        </div>
        <div class="mt-3 text-primary" id="loaderMessage">
            Memuat data...
        </div>
    </div>


    <!-- Sticky header -->
    <div class="sticky-top bg-white shadow-sm py-2 mb-0">
        <div class="d-flex flex-column flex-md-row flex-wrap justify-content-between align-items-start align-items-md-center">
            <h5 class="mb-2 mb-md-0">Pengguna Terdaftar</h5>
            <div class="d-flex flex-wrap gap-2">
                <!-- <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-house-door-fill"></i>
                </a> -->
                <a href="{{ route('admin.register.form') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus-fill"></i> Daftarkan
                </a>
            </div>
        </div>
    </div>

    <!-- Mail Form and Table -->
    <form id="sendMailForm" method="POST" action="{{ route('users.sendmail') }}">
        @csrf

        <table class="table table-sm table-compact table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th style="width:1%"><input type="checkbox" id="selectAll"></th>
                    <th style="width:1%">No.</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Tanggal Daftar</th>
                    <th class="text-center" style="width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td><input type="checkbox" name="recipients[]" value="{{ $user->id }}" class="recipient-checkbox"></td>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->getRoleNames()->first() ?? 'Tanpa Role' }}</td>
                        <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                        <td class="text-center">
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" form="delete-user-{{ $user->id }}"
                                onclick="return confirm('Hapus {{ $user->name }}?')">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Tidak ada pengguna.</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary & Send Button -->
        <div class="d-flex justify-content-between align-items-center mt-3" style="font-size: 0.85rem;">
            <div class="small text-muted">
                Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} data
            </div>
            <div class="pagination-wrapper small">
                {{ $users->links() }}
            </div>
            <div>
                <button type="submit" id="sendEmailBtn" class="btn btn-primary btn-sm">
                    <i class="bi bi-send-fill"></i> Kirim Email ke Terpilih
                </button>
            </div>
        </div>
    </form>

    <!-- Edit Modal -->
        @foreach($users as $user)
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-content border border-1 border-primary rounded-4 shadow">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Pengguna</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label>Role</label>
                                <select name="role" class="form-select" required>
                                    @foreach(Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Modal Footer Buttons -->
                        <div class="modal-footer border-top-0 d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-4"
                                style="background-color: transparent !important; border-color: #0d6efd; color: #0d6efd;"
                                data-bs-dismiss="modal">
                                Batal
                            </button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

    <!-- Hidden DELETE Forms -->
    @foreach($users as $user)
        <form id="delete-user-{{ $user->id }}" method="POST" action="{{ route('users.destroy', $user) }}" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>
@endsection

@push('head')
    <style>
        #loader {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
    </style>
@endpush

@push('scripts')
@if(session('emailSuccess'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Email Terkirim',
        text: '{{ session('emailSuccess') }}',
        timer: 4000,
        showConfirmButton: false
    });
</script>
@endif

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 2500,
            showConfirmButton: false
        });
    </script>
@endif


<script>
    const loader = document.getElementById('loader');
    const loaderMessage = document.getElementById('loaderMessage');
    const form = document.getElementById('sendMailForm');
    const sendEmailBtn = document.getElementById('sendEmailBtn');

    // Trigger loader ONLY when the send email button is clicked
    sendEmailBtn.addEventListener('click', function () {
        loaderMessage.textContent = 'Mengirim email selamat datang...';
        loader.style.display = 'flex';
    });

    // Show loader when the page first starts loading
    window.addEventListener('DOMContentLoaded', () => {
        loaderMessage.textContent = 'Memuat data...';
        loader.style.display = 'flex';
    });

    // Hide loader once everything is rendered
    window.addEventListener('load', () => {
        loader.style.display = 'none';
    });

    // Checkbox logic
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.recipient-checkbox')
                .forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
