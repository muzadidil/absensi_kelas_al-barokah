@extends('layouts.admin')

@section('title', 'Detail Tugas')

@section('content')
<div class="container-fluid px-2">

    <!-- Success Message -->
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
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    <!-- ================= BAGIAN A: INFO TUGAS ================= -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div>
                    <h4 class="fw-bold mb-1">{{ $assignment->title }}</h4>
                    <p class="text-muted mb-2">{{ $assignment->description ?: 'Tidak ada deskripsi.' }}</p>
                    <div class="d-flex flex-wrap gap-3 small">
                        <span>
                            <i class="bi bi-people-fill me-1"></i>
                            Target: <strong>{{ $assignment->grade_level ? 'Kelas ' . $assignment->grade_level : 'Individual' }}</strong>
                        </span>
                        <span>
                            <i class="bi bi-calendar-event me-1"></i>
                            Deadline: <strong>{{ $assignment->deadline ? $assignment->deadline->format('d/m/Y H:i') : 'Tidak ada' }}</strong>
                        </span>
                    </div>
                </div>
                <a href="{{ route('admin.assignments.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    <!-- ================= BAGIAN B: DAFTAR SOAL (read-only) ================= -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h5 class="mb-3">Daftar Soal</h5>

            @forelse($assignment->questions as $question)
                <div class="border rounded-3 p-3 mb-2">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold">Soal {{ $loop->iteration }}</span>
                        <span class="badge {{ $question->type === 'pilgan' ? 'bg-primary' : 'bg-warning text-dark' }}">
                            {{ $question->type === 'pilgan' ? 'Pilgan' : 'Essay' }}
                        </span>
                        <span class="badge bg-light text-dark border">{{ $question->points }} poin</span>
                    </div>
                    <p class="mb-2">{{ $question->question_text }}</p>

                    @if($question->type === 'pilgan' && $question->options)
                        <ul class="list-unstyled mb-0 small">
                            @foreach($question->options as $i => $option)
                                <li class="{{ $option === $question->correct_answer ? 'text-success fw-semibold' : '' }}">
                                    <i class="bi {{ $option === $question->correct_answer ? 'bi-check-circle-fill' : 'bi-circle' }} me-1"></i>
                                    {{ chr(97 + $i) }}. {{ $option }}
                                </li>
                            @endforeach
                        </ul>
                    @elseif($question->type === 'essay' && $question->answer_key)
                        <div class="small text-muted">
                            <span class="fw-semibold">Kunci jawaban acuan:</span> {{ $question->answer_key }}
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted text-center mb-0">Belum ada soal untuk tugas ini.</p>
            @endforelse
        </div>
    </div>

    <!-- ================= BAGIAN C: MURID YANG DITUGASKAN (read + evaluasi) ================= -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Murid yang Ditugaskan</h5>

            <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm">
                <table class="table table-sm table-compact table-bordered table-hover bg-white mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 1%;">No.</th>
                            <th class="px-3 py-2 text-left">Nama Murid</th>
                            <th class="px-3 py-2 text-left">Kelas</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">Nilai</th>
                            <th class="px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignment->assignmentLearners as $al)
                            <tr>
                                <td class="px-3 py-1">{{ $loop->iteration }}</td>
                                <td class="px-3 py-1">{{ $al->learner->nama_lengkap }}</td>
                                <td class="px-3 py-1">{{ $al->learner->grade_level }}</td>
                                <td class="px-3 py-1">
                                    <span class="badge {{ $al->status === 'selesai' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $al->status === 'selesai' ? 'Selesai' : 'Belum' }}
                                    </span>
                                </td>
                                <td class="px-3 py-1">
                                    @if($al->status === 'selesai')
                                        {{ $al->total_score ?? 0 }}
                                        @if(isset($ungradedByLearner[$al->learner_id]))
                                            <span class="text-warning small d-block">* (essay belum dinilai)</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-1 text-center">
                                    @if($al->status === 'selesai')
                                        <a href="{{ route('admin.assignments.learner-answers', [$assignment->id, $al->learner_id]) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bi bi-clipboard-check"></i> Nilai
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada murid yang ditugaskan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
