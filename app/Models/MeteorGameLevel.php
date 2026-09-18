<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeteorGameLevel extends Model
{
    protected $fillable = [
        'level_number',
        'display_label',
        'allowed_keys',
        'lives',
        'wave_target',
        'spawn_interval_ms',
        'boss_name',
        'boss_weapon_name',
        'boss_bullets_per_shot',
        'boss_hp',
        'is_checkpoint',
    ];

    protected $casts = [
        'is_checkpoint' => 'boolean',
    ];

    public function attempts()
    {
        return $this->hasMany(MeteorGameAttempt::class);
    }
}
