<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizLevel extends Model
{
    protected $fillable = [
        'level_number',
        'name',
        'description',
        'reset_to_first_on_fail',
    ];

    protected $casts = [
        'reset_to_first_on_fail' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
