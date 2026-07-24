@php
    $gradeLevelsForLogin = \App\Models\GradeLevel::orderBy('name')->get();
@endphp

<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Tabs -->
    <div class="flex mb-6 rounded-full bg-white/10 p-1">
        <button type="button" id="tab-btn-staff" onclick="switchLoginTab('staff')"
            class="flex-1 py-2 text-sm font-semibold text-center rounded-full transition">
            Admin / Guru
        </button>
        <button type="button" id="tab-btn-siswa" onclick="switchLoginTab('siswa')"
            class="flex-1 py-2 text-sm font-semibold text-center rounded-full transition">
            Siswa
        </button>
    </div>

    <!-- ================= TAB: ADMIN / GURU (tidak diubah) ================= -->
    <div id="tab-panel-staff">
        <div class="relative">
            <!-- X button to go back (top-right of screen) -->
            <!-- <a href="{{ url('/') }}"
            class="absolute top-0 right-0 text-gray-400 hover:text-gray-600 text-2xl font-bold z-50"
            aria-label="Close"
            title="Back to Home">
            &times;
            </a> -->

            <form method="POST" action="{{ route('login') }}" class="mt-5">
                @csrf

                <!-- Email Address -->
                <div class="mt-8">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email address"/>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password"/>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                </div>

               <!-- Remember Me and Forgot Password -->
                <div class="flex items-center justify-between mt-4 p-2">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-sm text-gray-300">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-300 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <!-- Actions: Back and Login Buttons -->
                <div class="flex justify-between items-center mt-4 mb-4">
                    <!-- Back Button -->
                    <a href="{{ url('/') }}" class="text-sm text-white hover:underline">
                        Back to Home
                    </a>

                    <!-- Login Button -->
                    <x-primary-button style="background-color: #2563eb; color: white;" class="px-5 py-2 rounded-full font-medium transition-all duration-200">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= TAB: SISWA (baru) ================= -->
    <div id="tab-panel-siswa">
        <form method="POST" action="{{ route('learner.login') }}" class="mt-5">
            @csrf

            <!-- Pilih Kelas -->
            <div class="mt-4">
                <x-input-label for="grade_level_select" value="Pilih Kelas" />
                <select id="grade_level_select" onchange="loadLearnersByGrade(this.value)"
                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($gradeLevelsForLogin as $gradeLevel)
                        <option value="{{ $gradeLevel->name }}" @selected(old('grade_level_select') === $gradeLevel->name)>{{ $gradeLevel->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Nama (terisi via AJAX) -->
            <div class="mt-4">
                <x-input-label for="learner_id" value="Pilih Nama" />
                <select id="learner_id" name="learner_id" required disabled
                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">-- Pilih Kelas Dahulu --</option>
                </select>
                <x-input-error :messages="$errors->get('learner_id')" class="mt-2" />
            </div>

            <!-- PIN -->
            <div class="mt-4">
                <x-input-label for="pin" value="PIN" />
                <x-text-input id="pin" class="block mt-1 w-full tracking-widest" type="password" name="pin"
                    maxlength="4" inputmode="numeric" pattern="[0-9]*" placeholder="Masukkan PIN 4 digit" required autocomplete="off" />
                <x-input-error :messages="$errors->get('pin')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center mt-4 mb-4">
                <a href="{{ url('/') }}" class="text-sm text-white hover:underline">
                    Kembali ke Beranda
                </a>

                <x-primary-button style="background-color: #2563eb; color: white;" class="px-5 py-2 rounded-full font-medium transition-all duration-200">
                    Masuk
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        function switchLoginTab(tab) {
            const staffPanel = document.getElementById('tab-panel-staff');
            const siswaPanel = document.getElementById('tab-panel-siswa');
            const staffBtn = document.getElementById('tab-btn-staff');
            const siswaBtn = document.getElementById('tab-btn-siswa');

            const activeClasses = ['bg-blue-600', 'text-white'];
            const inactiveClasses = ['text-gray-300'];

            if (tab === 'siswa') {
                staffPanel.classList.add('hidden');
                siswaPanel.classList.remove('hidden');
                siswaBtn.classList.add(...activeClasses);
                siswaBtn.classList.remove(...inactiveClasses);
                staffBtn.classList.remove(...activeClasses);
                staffBtn.classList.add(...inactiveClasses);
            } else {
                siswaPanel.classList.add('hidden');
                staffPanel.classList.remove('hidden');
                staffBtn.classList.add(...activeClasses);
                staffBtn.classList.remove(...inactiveClasses);
                siswaBtn.classList.remove(...activeClasses);
                siswaBtn.classList.add(...inactiveClasses);
            }
        }

        function loadLearnersByGrade(gradeLevel) {
            const select = document.getElementById('learner_id');

            if (!gradeLevel) {
                select.innerHTML = '<option value="">-- Pilih Kelas Dahulu --</option>';
                select.disabled = true;
                return;
            }

            select.disabled = true;
            select.innerHTML = '<option value="">Memuat data murid...</option>';

            fetch(`/api/learners-by-grade/${encodeURIComponent(gradeLevel)}`)
                .then(response => response.json())
                .then(learners => {
                    if (!learners.length) {
                        select.innerHTML = '<option value="">Tidak ada murid di kelas ini</option>';
                        select.disabled = true;
                        return;
                    }

                    select.innerHTML = '<option value="">-- Pilih Nama --</option>';
                    learners.forEach(learner => {
                        const option = document.createElement('option');
                        option.value = learner.id;
                        option.textContent = learner.name;
                        select.appendChild(option);
                    });
                    select.disabled = false;
                })
                .catch(() => {
                    select.innerHTML = '<option value="">Gagal memuat data murid</option>';
                    select.disabled = true;
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->has('learner_id') || $errors->has('pin'))
                switchLoginTab('siswa');
            @else
                switchLoginTab('staff');
            @endif
        });
    </script>
</x-guest-layout>
