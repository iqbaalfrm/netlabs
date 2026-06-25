<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $table = 'chat_history';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'pertemuan_id',
        'pesan',
        'jawaban',
        'waktu',
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