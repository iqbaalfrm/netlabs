<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressTopik extends Model
{
    protected $table = 'progress_topik';
    public $timestamps = false;

    protected $fillable = [
        'siswa_id',
        'topik_id',
        'is_selesai',
        'selesai_pada',
    ];

    /**
     * Relasi ke user (siswa)
     */
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /**
     * Relasi ke topik
     */
    public function topik()
    {
        return $this->belongsTo(Topik::class, 'topik_id');
    }
}