<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypingLevel extends Model
{
    protected $fillable = [
        'level_number',
        'name',
        'allowed_keys',
        'word_bank',
        'description',
        'allow_backspace',
        'allow_space',
        'min_wpm',
        'min_accuracy',
        'max_error_percent',
        'time_limit_seconds',
    ];

    protected $casts = [
        'allow_backspace' => 'boolean',
        'allow_space' => 'boolean',
        'min_wpm' => 'integer',
        'min_accuracy' => 'integer',
        'max_error_percent' => 'integer',
        'time_limit_seconds' => 'integer',
    ];

    public function attempts()
    {
        return $this->hasMany(TypingAttempt::class);
    }

    /**
     * Apakah hasil (kecepatan, akurasi kata, & % salah) memenuhi ambang lulus
     * tahap ini. Ketiganya harus terpenuhi: kecepatan, kebenaran, kesalahan.
     */
    public function isPassing(int $wpm, int $accuracyPercent, int $errorPercent): bool
    {
        return $wpm >= $this->min_wpm
            && $accuracyPercent >= $this->min_accuracy
            && $errorPercent <= $this->max_error_percent;
    }

    /**
     * Apakah tahap ini punya syarat kelulusan yang aktif (bukan default longgar).
     */
    public function hasPassCriteria(): bool
    {
        return $this->min_wpm > 0 || $this->min_accuracy > 0 || $this->max_error_percent < 100;
    }

    /**
     * Apakah tahap ini berbatas waktu (mode typing test berdurasi).
     */
    public function hasTimeLimit(): bool
    {
        return $this->time_limit_seconds > 0;
    }
}
