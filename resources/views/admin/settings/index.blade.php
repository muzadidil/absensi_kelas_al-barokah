@extends('layouts.admin')

@section('title', 'Pengaturan Situs')

@push('styles')
<style>
    .settings-card {
        border: none;
        border-radius: var(--lems-radius);
        box-shadow: var(--lems-shadow-sm);
    }
    .settings-card .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(16, 24, 40, 0.06);
        border-radius: var(--lems-radius) var(--lems-radius) 0 0 !important;
        padding: 1rem 1.25rem;
    }
    .settings-card .card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--lems-ink);
        margin: 0;
    }
    .settings-card .card-header .subtitle {
        font-size: 0.8rem;
        color: var(--lems-ink-muted);
    }
    .settings-card .card-body { padding: 1.25rem; }

    .form-label { font-weight: 600; font-size: 0.85rem; color: var(--lems-ink); }
    .form-text { font-size: 0.78rem; }

    .brand-preview {
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--lems-canvas);
        border: 1px dashed rgba(16, 24, 40, 0.18);
        border-radius: 0.75rem;
        padding: 0.75rem;
        min-height: 96px;
    }
    .brand-preview img { max-height: 72px; max-width: 100%; object-fit: contain; }
    .brand-preview.favicon img { max-height: 48px; max-width: 48px; }
    .brand-preview .placeholder-text { color: var(--lems-ink-muted); font-size: 0.8rem; }

    .save-bar {
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 1px solid rgba(16, 24, 40, 0.06);
        box-shadow: 0 -4px 12px rgba(16, 24, 40, 0.05);
        padding: 0.85rem 1.25rem;
        border-radius: var(--lems-radius);
        margin-top: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-3">
        <h4 class="fw-bold mb-1"><i class="bi bi-gear-fill me-1"></i> Pengaturan Situs</h4>
        <p class="text-muted mb-0">Atur identitas & tampilan situs: nama, alamat, favicon, dan logo halaman login.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <!-- Identitas Situs -->
            <div class="col-lg-6">
                <div class="card settings-card h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-buildings me-1"></i> Identitas Situs</h5>
                        <div class="subtitle">Nama & alamat yang tampil di halaman login.</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Nama Situs</label>
                            <input type="text" name="site_name" id="site_name"
                                   class="form-control rounded-3"
                                   value="{{ old('site_name', $siteName) }}"
                                   placeholder="Sistem Absensi Kelas Al-Barokah">
                            <div class="form-text text-muted">Kosongkan untuk memakai nama bawaan.</div>
                        </div>

                        <div class="mb-0">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea name="address" id="address" rows="4"
                                      class="form-control rounded-3"
                                      placeholder="Contoh: Jl. Pesantren No. 1, Desa Al-Barokah, Kec. ..., Kab. ...">{{ old('address', $address) }}</textarea>
                            <div class="form-text text-muted">Ditampilkan di halaman login sebagai info lokasi.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo & Favicon -->
            <div class="col-lg-6">
                <div class="card settings-card h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-image me-1"></i> Logo & Favicon</h5>
                        <div class="subtitle">Gambar branding situs.</div>
                    </div>
                    <div class="card-body">
                        <!-- Logo Login -->
                        <div class="mb-4">
                            <label for="login_logo" class="form-label">Logo Halaman Login</label>
                            <div class="row g-2 align-items-center">
                                <div class="col-4">
                                    <div class="brand-preview">
                                        @if($loginLogoPath)
                                            <img id="loginLogoPreview" src="{{ asset($loginLogoPath) }}" alt="Logo login">
                                        @else
                                            <img id="loginLogoPreview" src="{{ asset('images/developer.png') }}" alt="Logo login (bawaan)">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-8">
                                    <input type="file" name="login_logo" id="login_logo"
                                           class="form-control rounded-3" accept="image/*"
                                           onchange="previewImage(this, 'loginLogoPreview')">
                                    <div class="form-text text-muted">PNG/JPG/SVG/WebP, maks 2 MB. Kosongkan bila tidak diubah.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Favicon -->
                        <div class="mb-0">
                            <label for="favicon" class="form-label">Favicon (ikon tab browser)</label>
                            <div class="row g-2 align-items-center">
                                <div class="col-4">
                                    <div class="brand-preview favicon">
                                        @if($faviconPath)
                                            <img id="faviconPreview" src="{{ asset($faviconPath) }}" alt="Favicon">
                                        @else
                                            <span id="faviconPlaceholder" class="placeholder-text">Belum ada</span>
                                            <img id="faviconPreview" src="" alt="Favicon" style="display:none;">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-8">
                                    <input type="file" name="favicon" id="favicon"
                                           class="form-control rounded-3" accept="image/*"
                                           onchange="previewImage(this, 'faviconPreview', 'faviconPlaceholder')">
                                    <div class="form-text text-muted">PNG/ICO/SVG/WebP, maks 1 MB. Sebaiknya persegi (mis. 64×64).</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="save-bar d-flex justify-content-end align-items-center gap-2">
            <span class="text-muted small me-auto">Perubahan berlaku setelah disimpan.</span>
            <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-save me-1"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true,
        });
    });
</script>
@endif
<script>
    function previewImage(input, previewId, placeholderId) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById(previewId);
            img.src = e.target.result;
            img.style.display = 'block';
            if (placeholderId) {
                const ph = document.getElementById(placeholderId);
                if (ph) ph.style.display = 'none';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>
@endpush
