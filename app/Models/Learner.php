<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
     use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'pin',
        'grade_level',
        'section',
    ];

    public function attendance()
    {
        return $this->hasMany(LearnerAttendance::class);
    }

    public function assignmentLearners()
    {
        return $this->hasMany(AssignmentLearner::class);
    }

    public function learnerAnswers()
    {
        return $this->hasMany(LearnerAnswer::class);
    }
}
