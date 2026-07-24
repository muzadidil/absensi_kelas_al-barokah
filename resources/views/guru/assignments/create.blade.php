@extends('layouts.guru')

@section('title', 'Buat Tugas Baru')

@section('content')
<div class="container-fluid px-2">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Buat Tugas Baru</h5>
        <a href="{{ route('guru.assignments.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('guru.assignments.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Judul Tugas</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi <span class="text-muted small">(opsional)</span></label>
                    <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Target Tugas</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="target_type" id="targetKelas" value="kelas"
                            onchange="toggleTargetFields()" {{ old('target_type', 'kelas') === 'kelas' ? 'checked' : '' }}>
                        <label class="form-check-label" for="targetKelas">Untuk Kelas</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="target_type" id="targetIndividu" value="individu"
                            onchange="toggleTargetFields()" {{ old('target_type') === 'individu' ? 'checked' : '' }}>
                        <label class="form-check-label" for="targetIndividu">Untuk Murid Tertentu</label>
                    </div>
                    @error('target_type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3" id="gradeLevelField">
                    <label for="grade_level" class="form-label">Pilih Kelas</label>
                    <select name="grade_level" id="grade_level" class="form-select" style="max-width: 300px;">
                        <option value="" selected disabled>Pilih Tingkat</option>
                        @foreach($gradeLevels as $gradeLevel)
                            <option value="{{ $gradeLevel->name }}" {{ old('grade_level') === $gradeLevel->name ? 'selected' : '' }}>{{ $gradeLevel->name }}</option>
                        @endforeach
                    </select>
                    @error('grade_level')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 d-none" id="individualInfo">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Pemilihan murid untuk tugas ini dilakukan di halaman detail tugas setelah disimpan.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deadline" class="form-label">Deadline <span class="text-muted small">(opsional)</span></label>
                    <input type="datetime-local" name="deadline" id="deadline" class="form-control" value="{{ old('deadline') }}" style="max-width: 300px;">
                    @error('deadline')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleTargetFields() {
        const isKelas = document.getElementById('targetKelas').checked;
        document.getElementById('gradeLevelField').classList.toggle('d-none', !isKelas);
        document.getElementById('individualInfo').classList.toggle('d-none', isKelas);
        document.getElementById('grade_level').required = isKelas;
    }

    document.addEventListener('DOMContentLoaded', toggleTargetFields);
</script>
@endsection
