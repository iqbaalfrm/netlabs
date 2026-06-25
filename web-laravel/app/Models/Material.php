<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = [
        'module_id',
        'judul',
        'konten',
        'file_pdf',
        'gambar',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    // ─── Relasi ─────────────────────────────────────────

    /**
     * Satu materi dimiliki oleh satu modul.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}