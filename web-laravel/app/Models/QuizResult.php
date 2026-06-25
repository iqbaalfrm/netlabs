<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    protected $table = 'quiz_results';

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = [
        'user_id',
        'quiz_id',
        'skor',
        'total_soal',
        'jawaban',      // JSON { soal_id: "a", ... }
        'selesai_at',   // timestamp
    ];

    protected $casts = [
        'jawaban'   => 'array',
        'selesai_at'=> 'datetime',
    ];

    // ─── Relasi ─────────────────────────────────────────

    /**
     * Hasil kuis dimiliki oleh satu user (siswa).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hasil kuis dimiliki oleh satu kuis.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}