<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name'];

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teacher', 'subject_id', 'guru_id');
    }

    public function quizLevels()
    {
        return $this->hasMany(QuizLevel::class);
    }
}
