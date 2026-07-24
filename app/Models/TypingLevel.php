<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypingLevel extends Model
{
    protected $fillable = [
        'level_number',
        'name',
        'allowed_keys',
        'description',
    ];

    public function attempts()
    {
        return $this->hasMany(TypingAttempt::class);
    }
}
