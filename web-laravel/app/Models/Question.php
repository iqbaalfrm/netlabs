<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = [
        'quiz_id',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'jawaban_benar',   // enum: a, b, c, d
    ];

    // ─── Relasi ─────────────────────────────────────────

    /**
     * Satu soal dimiliki oleh satu kuis.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}