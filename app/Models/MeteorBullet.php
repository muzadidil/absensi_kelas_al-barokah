<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeteorBullet extends Model
{
    protected $fillable = [
        'name',
        'color_core',
        'color_mid',
        'color_edge',
        'meteor_effect_id',
    ];

    public function effect()
    {
        return $this->belongsTo(MeteorEffect::class, 'meteor_effect_id');
    }
}
