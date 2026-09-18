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
        'quiz_reset_at',
        'meteor_reset_at',
        'meteor_checkpoint_level',
    ];

    protected $casts = [
        'quiz_reset_at' => 'datetime',
        'meteor_reset_at' => 'datetime',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function meteorGameAttempts()
    {
        return $this->hasMany(MeteorGameAttempt::class);
    }
}
