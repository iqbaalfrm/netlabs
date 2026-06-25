<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulPdf extends Model
{
    protected $table = 'modul_pdf';
    public $timestamps = false;

    protected $fillable = [
        'pertemuan_id',
        'nama_file',
        'path',
        'ukuran_bytes',
        'diupload_oleh',
    ];

    /**
     * Relasi ke pertemuan
     */
    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id');
    }

    /**
     * Guru yang mengupload
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'diupload_oleh');
    }
}