<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    // ─── Relasi ─────────────────────────────────────────

    /**
     * Satu modul memiliki banyak materi.
     */
    public function materials()
    {
        return $this->hasMany(Material::class)->orderBy('urutan');
    }

    /**
     * Satu modul memiliki banyak kuis.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}