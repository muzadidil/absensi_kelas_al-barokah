<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeteorGameLevel extends Model
{
    protected $fillable = [
        'level_number',
        'display_label',
        'theme',
        'allowed_keys',
        'lives',
        'wave_target',
        'spawn_interval_ms',
        'boss_name',
        'boss_weapon_name',
        'boss_bullets_per_shot',
        'boss_hp',
        'bullet_returns',
        'is_checkpoint',
    ];

    protected $casts = [
        'is_checkpoint' => 'boolean',
        'bullet_returns' => 'boolean',
    ];

    public function attempts()
    {
        return $this->hasMany(MeteorGameAttempt::class);
    }
}
