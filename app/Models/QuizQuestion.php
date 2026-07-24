<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_level_id',
        'question_text',
        'explanation',
        'sort_order',
    ];

    public function level()
    {
        return $this->belongsTo(QuizLevel::class, 'quiz_level_id');
    }

    public function options()
    {
        return $this->hasMany(QuizOption::class)->orderBy('sort_order');
    }

    public function correctOption()
    {
        return $this->hasOne(QuizOption::class)->where('is_correct', true);
    }
}
