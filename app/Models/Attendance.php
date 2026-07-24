<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'learner_id',
        'jam_pelajaran_id',
        'tanggal',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const STATUS_LIST = ['hadir', 'sakit', 'izin', 'alpa'];

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

    public function jamPelajaran()
    {
        return $this->belongsTo(JamPelajaran::class);
    }
}
