<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypingAttempt extends Model
{
    protected $fillable = [
        'learner_id',
        'typing_level_id',
        'wpm',
        'accuracy',
        'correct_words',
        'wrong_words',
        'total_words',
        'passed',
        'duration_seconds',
    ];

    protected $casts = [
        'passed' => 'boolean',
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

    public function typingLevel()
    {
        return $this->belongsTo(TypingLevel::class);
    }
}
