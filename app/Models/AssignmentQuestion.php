<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentQuestion extends Model
{
    protected $fillable = [
        'assignment_id',
        'type',
        'question_text',
        'options',
        'correct_answer',
        'points',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function learnerAnswers()
    {
        return $this->hasMany(LearnerAnswer::class);
    }
}
