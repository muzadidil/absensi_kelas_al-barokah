<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeteorGameAttempt extends Model
{
    protected $fillable = [
        'learner_id',
        'meteor_game_level_id',
        'passed',
        'reached_boss',
        'meteors_destroyed',
        'wpm',
        'accuracy',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'reached_boss' => 'boolean',
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

    public function meteorGameLevel()
    {
        return $this->belongsTo(MeteorGameLevel::class);
    }
}
