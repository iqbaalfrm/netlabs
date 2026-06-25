<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // Timestamps aktif (created_at, updated_at)
    public $timestamps = true;

    /**
     * Kolom yang bisa diisi massal (sesuai tabel users di DB).
     */
    protected $fillable = [
        'nis',
        'nama',
        'email',
        'password',
        'role',        // enum: guru, siswa
        'kelas',
    ];

    // Kolom yang disembunyikan dari JSON
    protected $hidden = ['password'];

    // ─── JWT Subject ────────────────────────────────────

    /**
     * Dapatkan identifier untuk JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Dapatkan custom claims JWT (tambahkan role dan nama di token).
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role,
            'nama' => $this->nama,
        ];
    }

    // ─── Relasi Eloquent ────────────────────────────────

    /**
     * Satu user memiliki banyak hasil kuis.
     */
    public function hasilKuis()
    {
        return $this->hasMany(HasilKuis::class, 'user_id');
    }

    /**
     * Satu user memiliki banyak chat history.
     */
    public function chatHistory()
    {
        return $this->hasMany(ChatHistory::class, 'user_id');
    }

    /**
     * Satu user memiliki banyak progress topik.
     */
    public function progressTopik()
    {
        return $this->hasMany(ProgressTopik::class, 'user_id');
    }

    // ─── Helper ─────────────────────────────────────────

    /**
     * Cek apakah user memiliki role tertentu.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Cek apakah user adalah guru.
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }
}