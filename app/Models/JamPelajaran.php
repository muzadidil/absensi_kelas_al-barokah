<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    protected $fillable = [
        'grade_level',
        'hari',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'subject_id',
        'guru_id',
    ];

    public const HARI_LIST = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
