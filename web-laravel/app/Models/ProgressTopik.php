<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressTopik extends Model
{
    protected $table = 'progress_topik';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'topik_id',
        'is_selesai',
        'selesai_pada',
    ];

    /**
     * Relasi ke user (siswa)
     */
    public function siswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke topik
     */
    public function topik()
    {
        return $this->belongsTo(Topik::class, 'topik_id');
    }
}