<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeteorTheme extends Model
{
    public const SKY_OBJECTS = [
        'bintang' => 'Bintang (malam)',
        'awan' => 'Awan (siang)',
        'tidak_ada' => 'Polos',
    ];

    protected $fillable = [
        'name',
        'sky_top', 'sky_mid', 'sky_bottom',
        'ground_top', 'ground_bottom',
        'accent', 'wall_color', 'dome_color',
        'sky_object', 'is_dark',
    ];

    protected $casts = [
        'is_dark' => 'boolean',
    ];

    public function levels()
    {
        return $this->hasMany(MeteorGameLevel::class);
    }
}
