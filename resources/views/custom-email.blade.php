@extends('layouts.admin')

@section('title', 'Kirim Email Kustom')

@push('head')
    <!-- Trix Editor (WYSIWYG) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2.0.0/dist/trix.css">
    <style>
        trix-editor {
            min-height: 150px;
            background: #fff;
        }
         /* Loader overlay */
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
        }
    </style>
@endpush

@section('content')
<div class="container">
    <h4 class="mb-4">Kirim Email Kustom ke Pengguna Terpilih</h4>

    <!-- Loader Overlay -->
    <div id="loader">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
            <span class="visually-hidden">Mengirim email...</span>
        </div>
        <div class="mt-3 text-primary">
            Mengirim email kustom...
        </div>
    </div>

    <form id="sendCustomEmailForm" action="{{ route('email.custom.send') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Subjek Email</label>
            <input type="text" name="subject" class="form-control" required>
        </div>

        <!-- Form group -->
        <div class="mb-3">
            <label class="form-label">Pesan</label>

            <!-- Hidden input field that will hold the actual content -->
            <input id="x" type="hidden" name="content" required>

            <!-- Trix editor linked to the hidden input -->
            <trix-editor input="x" placeholder="Tulis isi email di sini..."></trix-editor>
        </div>

        <div class="mb-3">
            <label class="form-label">Pilih Penerima</label>
            <div class="border rounded bg-white p-2" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 1%"><input type="checkbox" id="selectAll"></th>
                            <th style="width: 1%">No.</th>
                            <th>Nama</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox" name="recipients[]" value="{{ $user->id }}" id="user{{ $user->id }}">
                                </td>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-sm btn-primary">
                <i class="bi bi-send-fill"></i> Kirim Pesan
            </button>
        </div>

    </form>
</div>

    <!-- {{-- SweetAlert2 Notifications --}} -->
    @if(session('emailSuccess'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Email Kustom Terkirim',
                text: '{{ session('emailSuccess') }}',
                confirmButtonColor: '#3085d6',
                timer: 3000
            });
        </script>
    @endif

@endsection

@push('scripts')
 <!-- Show on submit / hide on load script -->
  <script>
    const loader = document.getElementById('loader');
    const form = document.getElementById('sendCustomEmailForm');
    
    form.addEventListener('submit', () => {
        loader.style.display = 'flex';
        form.querySelector('button[type="submit"]').disabled = true;
    });

    // form.addEventListener('submit', () => {
    //   loader.style.display = 'flex';
    // });  

    window.addEventListener('load', () => {
      loader.style.display = 'none';
    });

    document.getElementById('selectAll').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('input[name="recipients[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
  </script>
<script src="https://cdn.jsdelivr.net/npm/trix@2.0.0/dist/trix.umd.min.js"></script>
@endpush

