@extends('layouts.admin')

@section('title', 'Nilai Jawaban Murid')

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

    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h4 class="fw-bold mb-1">{{ $learner->nama_lengkap }}</h4>
                <p class="text-muted mb-0">Tugas: <strong>{{ $assignment->title }}</strong></p>
            </div>
            <a href="{{ route('admin.assignments.show', $assignment->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Tugas
            </a>
        </div>
    </div>

    <form action="{{ route('admin.assignments.learner-answers.grade', [$assignment->id, $learner->id]) }}" method="POST">
        @csrf

        @foreach($assignment->questions as $question)
            @php $answer = $answers->get($question->id); @endphp
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0">Soal {{ $loop->iteration }}</h6>
                        <div class="d-flex gap-2">
                            <span class="badge {{ $question->type === 'pilgan' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                {{ $question->type === 'pilgan' ? 'Pilgan' : 'Essay' }}
                            </span>
                            <span class="badge bg-light text-dark border">{{ $question->points }} poin</span>
                        </div>
                    </div>
                    <p class="mb-3">{{ $question->question_text }}</p>

                    @if($question->type === 'pilgan')
                        <div class="mb-2">
                            <span class="text-muted small d-block">Jawaban Murid:</span>
                            <span class="{{ $answer && $answer->answer_text === $question->correct_answer ? 'text-success fw-semibold' : 'text-danger fw-semibold' }}">
                                {{ $answer->answer_text ?: '(tidak dijawab)' }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small d-block">Jawaban Benar:</span>
                            {{ $question->correct_answer }}
                        </div>
                        <div>
                            @if($answer && $answer->answer_text === $question->correct_answer)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle-fill me-1"></i>Benar &mdash; {{ $answer->score }} poin
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle-fill me-1"></i>Salah &mdash; 0 poin
                                </span>
                            @endif
                        </div>
                    @else
                        @if($question->answer_key)
                            <div class="mb-3">
                                <span class="text-muted small d-block mb-1">Kunci Jawaban Acuan:</span>
                                <div class="border rounded-3 p-2 bg-light-subtle border-primary-subtle">{{ $question->answer_key }}</div>
                            </div>
                        @endif
                        <div class="mb-3">
                            <span class="text-muted small d-block mb-1">Jawaban Murid:</span>
                            <div class="border rounded-3 p-2 bg-light">{{ $answer->answer_text ?: '(tidak dijawab)' }}</div>
                        </div>
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-auto">
                                <label class="form-label mb-0">Nilai (maks {{ $question->points }})</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" name="scores[{{ $question->id }}]" class="form-control"
                                    style="max-width: 120px;" value="{{ $answer->score ?? '' }}"
                                    min="0" max="{{ $question->points }}" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label mb-1">Feedback / Komentar <span class="text-muted small">(opsional)</span></label>
                            <textarea name="feedback[{{ $question->id }}]" class="form-control" rows="2">{{ $answer->feedback ?? '' }}</textarea>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="card shadow-sm mb-3 bg-primary bg-opacity-10">
            <div class="card-body text-center">
                <h5 class="mb-0">Total Nilai Saat Ini: {{ $assignmentLearner->total_score ?? 0 }} poin</h5>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-save me-1"></i> Simpan Nilai
        </button>
    </form>
</div>
@endsection
