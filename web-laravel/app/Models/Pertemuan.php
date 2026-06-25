<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model
{
    protected $table = 'pertemuan';
    public $timestamps = false;

    protected $fillable = [
        'judul',
        'deskripsi',
        'nomor_urut',
        'warna_hex',
        'dibuat_oleh',
    ];

    /**
     * Topik-topik dalam pertemuan ini
     */
    public function topik()
    {
        return $this->hasMany(Topik::class, 'pertemuan_id');
    }

    /**
     * Guru yang membuat pertemuan
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Soal kuis dalam pertemuan ini
     */
    public function soalKuis()
    {
        return $this->hasMany(SoalKuis::class, 'pertemuan_id');
    }

    /**
     * Alias soal (bisa pakai $pertemuan->soal langsung)
     */
    public function soal()
    {
        return $this->soalKuis();
    }
}
