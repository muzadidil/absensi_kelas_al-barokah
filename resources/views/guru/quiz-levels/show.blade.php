@extends('layouts.guru')

@section('title', 'Kelola Soal — ' . $quizLevel->name)

@push('styles')
<style>
    [id^="addQuestionModal"] .modal-body,
    [id^="editQuestionModal"] .modal-body {
        max-height: calc(100vh - 190px);
        overflow-y: auto;
    }
    .opt-row { display: flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.6rem; border-radius: 0.5rem; }
    .opt-row.correct { background: rgba(25,135,84,.10); }
    .opt-row .marker { width: 22px; text-align: center; color: #9aa1ad; }
    .opt-row.correct .marker { color: #157347; }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex justify-content-between align-items-start gap-2 mb-3">
    <div>
        <span class="badge bg-primary mb-1">Tahap {{ $quizLevel->level_number }}</span>
        <h5 class="fw-bold mb-0">{{ $quizLevel->name }}</h5>
        <p class="text-muted small mb-0">{{ $quizLevel->description }}</p>
    </div>
    <div class="d-flex gap-2 flex-shrink-0">
        <a href="{{ route('guru.quiz-levels.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Soal
        </button>
    </div>
</div>

@if($quizLevel->questions->isEmpty())
    <div class="card"><div class="card-body text-center text-muted py-5">
        <i class="bi bi-patch-question display-5 d-block mb-2 opacity-50"></i>
        Belum ada soal di tahap ini. Tambahkan minimal 1 soal agar bisa dimainkan murid.
    </div></div>
@else
    @foreach($quizLevel->questions as $question)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div class="fw-semibold">
                        <span class="badge bg-secondary me-1">Soal {{ $loop->iteration }}</span>
                        {{ $question->question_text }}
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $question->id }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="{{ route('guru.quiz-questions.destroy', [$quizLevel->id, $question->id]) }}" method="POST"
                            onsubmit="return confirm('Hapus soal ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3-fill"></i></button>
                        </form>
                    </div>
                </div>

                <div class="mb-2">
                    @foreach($question->options as $opt)
                        <div class="opt-row {{ $opt->is_correct ? 'correct' : '' }}">
                            <span class="marker">
                                <i class="bi {{ $opt->is_correct ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                            </span>
                            <span>{{ $opt->option_text }}</span>
                            @if($opt->is_correct)<span class="badge bg-success ms-1">Benar</span>@endif
                        </div>
                    @endforeach
                </div>

                @if($question->explanation)
                    <div class="small text-muted"><i class="bi bi-lightbulb me-1"></i> <span class="fw-semibold">Penjelasan:</span> {{ $question->explanation }}</div>
                @endif
            </div>
        </div>

        <!-- Edit soal -->
        <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form action="{{ route('guru.quiz-questions.update', [$quizLevel->id, $question->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Soal {{ $loop->iteration }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @include('guru.quiz-levels._question-fields', ['question' => $question])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Tambah soal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('guru.quiz-questions.store', $quizLevel->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('guru.quiz-levels._question-fields', ['question' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
