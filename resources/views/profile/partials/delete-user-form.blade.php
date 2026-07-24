<section>
    <div class="mb-4">
        <h4 class="fw-bold">{{ __('Hapus Akun') }}</h4>
        <p class="text-muted small">
            {{ __('Setelah akun Anda dihapus, semua data dan informasi terkait akan dihapus secara permanen. Sebelum menghapus akun, silakan unduh data atau informasi yang ingin Anda simpan.') }}
        </p>
    </div>

    <!-- Delete Button to Trigger Modal -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
        {{ __('Hapus Akun') }}
    </button>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border border-danger rounded-4 shadow">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">{{ __('Konfirmasi Hapus Akun') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-3">
                            {{ __('Setelah akun Anda dihapus, semua data terkait akan dihapus permanen. Masukkan kata sandi Anda untuk konfirmasi.') }}
                        </p>

                        <div class="mb-3">
                            <label for="delete_password" class="form-label">{{ __('Kata Sandi') }}</label>
                            <input type="password"
                                   id="delete_password"
                                   name="password"
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                   placeholder="{{ __('Masukkan kata sandi Anda') }}">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            {{ __('Hapus Akun') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
