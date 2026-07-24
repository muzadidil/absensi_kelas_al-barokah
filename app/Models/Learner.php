<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
     use HasFactory;

    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'email',
        'pin',
        'grade_level',
        'section',
    ];

    public function attendance()
    {
        return $this->hasMany(LearnerAttendance::class);
    }
}
