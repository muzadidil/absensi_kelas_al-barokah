<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentLearner extends Model
{
    protected $fillable = [
        'assignment_id',
        'learner_id',
        'status',
        'submitted_at',
        'total_score',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }
}
