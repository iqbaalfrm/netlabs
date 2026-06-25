<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    protected $table = 'chat_logs';

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = [
        'user_id',
        'pertanyaan',
        'jawaban',
    ];

    // ─── Relasi ─────────────────────────────────────────

    /**
     * Satu chat log dimiliki oleh satu user (siswa).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}