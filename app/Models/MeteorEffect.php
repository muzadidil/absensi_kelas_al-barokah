<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeteorEffect extends Model
{
    protected $fillable = [
        'name',
        'particle_count',
        'particle_spread',
        'particle_hue',
        'shake_strength',
        'beam_color',
    ];

    public function bullets()
    {
        return $this->hasMany(MeteorBullet::class);
    }
}
