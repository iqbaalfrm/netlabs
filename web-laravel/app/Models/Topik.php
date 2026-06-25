<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topik extends Model
{
    protected $table = 'topik';
    public $timestamps = false;

    protected $fillable = [
        'pertemuan_id',
        'judul',
        'isi',
        'nomor_urut',
    ];

    /**
     * Relasi ke pertemuan
     */
    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id');
    }

    /**
     * Progress siswa pada topik ini
     */
    public function progress()
    {
        return $this->hasMany(ProgressTopik::class, 'topik_id');
    }
}