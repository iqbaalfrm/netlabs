<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quizzes';

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = [
        'module_id',
        'judul',
        'durasi_menit',
        'jumlah_soal',
        'is_generated',
        'generated_at',
        'aktif',
    ];

    protected $casts = [
        'aktif'        => 'boolean',
        'is_generated'  => 'boolean',
        'generated_at'  => 'datetime',
    ];

    // ─── Relasi ─────────────────────────────────────────

    /**
     * Satu kuis dimiliki oleh satu modul.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Satu kuis memiliki banyak soal.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Satu kuis memiliki banyak hasil kuis.
     */
    public function results()
    {
        return $this->hasMany(QuizResult::class);
    }
}