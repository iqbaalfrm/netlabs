<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilKuis extends Model
{
    protected $table = 'hasil_kuis';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'pertemuan_id',
        'skor',
        'benar',
        'salah',
        'dikerjakan_pada',
    ];

    /**
     * Relasi ke user (siswa)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke pertemuan
     */
    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id');
    }
}