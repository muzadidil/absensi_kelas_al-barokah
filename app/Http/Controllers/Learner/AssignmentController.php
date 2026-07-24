<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentLearner;
use App\Models\Learner;
use App\Models\LearnerAnswer;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Daftar tugas milik murid yang sedang login (lewat session learner_id).
     */
    public function index()
    {
        $learner = Learner::find(session('learner_id'));

        $assignmentLearners = AssignmentLearner::with('assignment')
            ->where('learner_id', $learner->id)
            ->get()
            ->sort(function ($a, $b) {
                if ($a->status !== $b->status) {
                    return $a->status === 'belum' ? -1 : 1;
                }

                $deadlineA = $a->assignment->deadline;
                $deadlineB = $b->assignment->deadline;

                if (! $deadlineA && ! $deadlineB) {
                    return 0;
                }
                if (! $deadlineA) {
                    return 1;
                }
                if (! $deadlineB) {
                    return -1;
                }

                return $deadlineA <=> $deadlineB;
            })
            ->values();

        return view('learner.assignments.index', compact('learner', 'assignmentLearners'));
    }

    /**
     * Tampilkan soal-soal tugas — hanya kalau tugas ini memang ditugaskan
     * ke murid yang sedang login.
     */
    public function show(Assignment $assignment)
    {
        $learner = Learner::find(session('learner_id'));

        $assignmentLearner = AssignmentLearner::where('assignment_id', $assignment->id)
            ->where('learner_id', $learner->id)
            ->first();

        abort_if(! $assignmentLearner, 403);

        $assignment->load(['questions' => fn ($query) => $query->orderBy('sort_order')]);

        $answers = LearnerAnswer::where('learner_id', $learner->id)
            ->whereIn('assignment_question_id', $assignment->questions->pluck('id'))
            ->get()
            ->keyBy('assignment_question_id');

        return view('learner.assignments.show', compact('learner', 'assignment', 'assignmentLearner', 'answers'));
    }

    /**
     * Simpan semua jawaban murid sekaligus, auto-koreksi soal pilgan,
     * dan tandai tugas sebagai selesai.
     */
    public function submit(Request $request, Assignment $assignment)
    {
        $learner = Learner::find(session('learner_id'));

        $assignmentLearner = AssignmentLearner::where('assignment_id', $assignment->id)
            ->where('learner_id', $learner->id)
            ->first();

        abort_if(! $assignmentLearner, 403);

        if ($assignmentLearner->status === 'selesai') {
            return redirect()->route('learner.assignments.index')
                ->with('error', 'Tugas ini sudah pernah dikirim.');
        }

        if ($assignment->deadline && $assignment->deadline->isPast()) {
            return redirect()->route('learner.assignments.index')
                ->with('error', 'Deadline tugas ini sudah lewat.');
        }

        $questions = $assignment->questions;
        $submittedAnswers = $request->input('answers', []);

        foreach ($questions as $question) {
            $answerText = $submittedAnswers[$question->id] ?? '';

            $score = null;
            if ($question->type === 'pilgan') {
                $score = $answerText === $question->correct_answer ? $question->points : 0;
            }

            LearnerAnswer::updateOrCreate(
                [
                    'learner_id' => $learner->id,
                    'assignment_question_id' => $question->id,
                ],
                [
                    'answer_text' => $answerText,
                    'score' => $score,
                ]
            );
        }

        $totalScore = LearnerAnswer::where('learner_id', $learner->id)
            ->whereIn('assignment_question_id', $questions->pluck('id'))
            ->whereNotNull('score')
            ->sum('score');

        $assignmentLearner->update([
            'status' => 'selesai',
            'submitted_at' => now(),
            'total_score' => $totalScore,
        ]);

        return redirect()->route('learner.assignments.index')
            ->with('success', 'Jawaban berhasil dikirim!');
    }

    /**
     * Raport murid yang sedang login — rekap semua tugas yang pernah
     * ditugaskan beserta nilai dan predikat keseluruhan.
     */
    public function raport()
    {
        $learner = Learner::find(session('learner_id'));

        $assignmentLearners = $learner->assignmentLearners()
            ->with('assignment.questions')
            ->get()
            ->sortByDesc(fn ($al) => $al->assignment->created_at)
            ->values();

        $selesai = $assignmentLearners->where('status', 'selesai');
        $totalTugas = $assignmentLearners->count();
        $totalSelesai = $selesai->count();
        $rataRata = $this->hitungRataRataPersen($selesai);
        $predikat = $this->hitungPredikat($rataRata);

        return view('learner.raport', compact(
            'learner', 'assignmentLearners', 'totalTugas', 'totalSelesai', 'rataRata', 'predikat'
        ));
    }

    private function hitungRataRataPersen($assignmentLearnersSelesai): float
    {
        if ($assignmentLearnersSelesai->isEmpty()) {
            return 0;
        }

        $persentasePerTugas = $assignmentLearnersSelesai->map(function ($al) {
            $maxScore = $al->assignment->questions->sum('points');

            return $maxScore > 0 ? ($al->total_score / $maxScore) * 100 : 0;
        });

        return round($persentasePerTugas->avg(), 1);
    }

    private function hitungPredikat(float $rataRata): string
    {
        return match (true) {
            $rataRata >= 90 => 'Sangat Baik',
            $rataRata >= 75 => 'Baik',
            $rataRata >= 60 => 'Cukup',
            default => 'Perlu Perbaikan',
        };
    }
}
