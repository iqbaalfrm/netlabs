<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $table = 'chat_history';
    public $timestamps = false;

    protected $fillable = [
        'siswa_id',
        'pertemuan_id',
        'pesan',
        'jawaban',
        'waktu',
    ];

    /**
     * Relasi ke user (siswa)
     */
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /**
     * Relasi ke pertemuan
     */
    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id');
    }
}