<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearnerAnswer extends Model
{
    protected $fillable = [
        'learner_id',
        'assignment_question_id',
        'answer_text',
        'score',
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

    public function assignmentQuestion()
    {
        return $this->belongsTo(AssignmentQuestion::class);
    }
}
