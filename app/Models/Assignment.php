<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'grade_level',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function questions()
    {
        return $this->hasMany(AssignmentQuestion::class);
    }

    public function assignmentLearners()
    {
        return $this->hasMany(AssignmentLearner::class);
    }
}
