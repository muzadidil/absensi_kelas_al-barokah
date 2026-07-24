@extends('layouts.learner')

@section('title', $assignment->title)

@section('content')

    <div class="d-flex justify-content-between align-items-start gap-2 mb-4">
        <div>
            @if($assignment->description)
                <p class="text-muted mb-1">{{ $assignment->description }}</p>
            @endif
            <p class="text-muted small mb-0">
                <i class="bi bi-calendar-event me-1"></i>
                @if($assignment->deadline)
                    Deadline: {{ $assignment->deadline->format('d/m/Y H:i') }}
                @else
                    Tidak ada batas waktu
                @endif
            </p>
        </div>
        <a href="{{ route('learner.assignments.index') }}" class="btn btn-outline-secondary btn-sm flex-shrink-0">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php $isSelesai = $assignmentLearner->status === 'selesai'; @endphp

    <form method="POST" action="{{ route('learner.assignments.submit', $assignment->id) }}" onsubmit="return confirmSubmit()">
        @csrf

        @forelse($assignment->questions as $question)
            @php $existingAnswer = $answers->get($question->id); @endphp
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0">Soal {{ $loop->iteration }}</h6>
                        <span class="badge bg-light text-dark border">{{ $question->points }} poin</span>
                    </div>
                    <p class="mb-3">{{ $question->question_text }}</p>

                    @if($question->type === 'pilgan')
                        @foreach($question->options as $i => $option)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio"
                                    name="answers[{{ $question->id }}]"
                                    id="q{{ $question->id }}_{{ $i }}"
                                    value="{{ $option }}"
                                    {{ $isSelesai ? 'disabled' : '' }}
                                    @checked($existingAnswer && $existingAnswer->answer_text === $option)>
                                <label class="form-check-label" for="q{{ $question->id }}_{{ $i }}">
                                    {{ chr(97 + $i) }}. {{ $option }}
                                </label>
                            </div>
                        @endforeach
                    @else
                        <textarea name="answers[{{ $question->id }}]" class="form-control" rows="4"
                            {{ $isSelesai ? 'readonly' : '' }}>{{ $existingAnswer->answer_text ?? '' }}</textarea>
                    @endif

                    @if($isSelesai)
                        <div class="mt-3 pt-2 border-top">
                            @if($question->type === 'pilgan')
                                @if($existingAnswer && $existingAnswer->answer_text === $question->correct_answer)
                                    <span class="text-success fw-semibold">
                                        <i class="bi bi-check-circle-fill me-1"></i>Benar &mdash; {{ $existingAnswer->score }} poin
                                    </span>
                                @else
                                    <span class="text-danger fw-semibold">
                                        <i class="bi bi-x-circle-fill me-1"></i>Salah &mdash; 0 poin
                                    </span>
                                    <div class="small text-muted">Jawaban benar: {{ $question->correct_answer }}</div>
                                @endif
                            @else
                                @if($existingAnswer && $existingAnswer->score !== null)
                                    <span class="text-success fw-semibold">
                                        <i class="bi bi-check-circle-fill me-1"></i>Nilai: {{ $existingAnswer->score }} poin
                                    </span>
                                    @if($existingAnswer->feedback)
                                        <div class="small text-muted mt-1">
                                            <span class="fw-semibold">Catatan guru:</span> {{ $existingAnswer->feedback }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">
                                        <i class="bi bi-hourglass-split me-1"></i>Menunggu penilaian
                                    </span>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">Belum ada soal untuk tugas ini.</div>
        @endforelse

        @if($isSelesai)
            <div class="card border-0 shadow-sm mb-3 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h5 class="mb-0">Total Nilai: {{ $assignmentLearner->total_score ?? 0 }} poin</h5>
                    @if($assignment->questions->contains('type', 'essay'))
                        <small class="text-muted">Nilai soal essay akan ditambahkan setelah dinilai admin.</small>
                    @endif
                </div>
            </div>
        @elseif($assignment->questions->isNotEmpty())
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="bi bi-send-fill me-1"></i> Kirim Jawaban
            </button>
        @endif
    </form>

@endsection

@push('scripts')
    <script>
        function confirmSubmit() {
            return confirm('Yakin ingin mengirim? Jawaban tidak bisa diubah setelah dikirim.');
        }
    </script>
@endpush
