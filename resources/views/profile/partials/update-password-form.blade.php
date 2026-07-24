<section>
    <div class="mb-4">
        <h4 class="fw-bold">{{ __('Perbarui Kata Sandi') }}</h4>
        <p class="text-muted small">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="current_password" class="form-label">{{ __('Kata Sandi Saat Ini') }}</label>
            <input type="password"
                   name="current_password"
                   id="current_password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Kata Sandi Baru') }}</label>
            <input type="password"
                   name="password"
                   id="password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Konfirmasi Kata Sandi') }}</label>
            <input type="password"
                   name="password_confirmation"
                   id="password_confirmation"
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                {{ __('Simpan') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success small">{{ __('Tersimpan.') }}</span>
            @endif
        </div>
    </form>
</section>
