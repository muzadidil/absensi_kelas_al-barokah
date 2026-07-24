<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'learner_id',
        'quiz_level_id',
        'passed',
        'questions_cleared',
        'total_questions',
    ];

    protected $casts = [
        'passed' => 'boolean',
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

    public function quizLevel()
    {
        return $this->belongsTo(QuizLevel::class);
    }
}
