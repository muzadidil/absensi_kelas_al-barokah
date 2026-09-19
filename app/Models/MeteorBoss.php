<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeteorBoss extends Model
{
    /** Gambar boss yang tersedia di kanvas — admin memilih, bukan menggambar sendiri. */
    public const SPRITES = [
        'pocong' => 'Pocong',
        'wewe' => 'Wewe Gombel',
        'genderuwo' => 'Genderuwo',
        'kelelawar' => 'Kelelawar',
        'ufo' => 'UFO',
        'drone' => 'Drone',
        'mecha' => 'Mecha / Robot',
        'satelit' => 'Satelit',
        'kapal' => 'Kapal Siluman',
        'inti_ai' => 'Inti AI',
    ];

    protected $fillable = [
        'name',
        'sprite',
        'hue',
    ];

    public function levels()
    {
        return $this->hasMany(MeteorGameLevel::class);
    }
}
