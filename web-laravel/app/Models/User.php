<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // Timestamps aktif (created_at, updated_at)
    public $timestamps = true;

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',        // enum: admin, guru, siswa
        'kelas',
        'no_hp',
        'foto',
        'aktif',       // boolean
    ];

    // Kolom yang disembunyikan dari JSON
    protected $hidden = ['password'];

    // Casting tipe data
    protected $casts = [
        'aktif' => 'boolean',
    ];

    // ─── JWT Subject ────────────────────────────────────

    /**
     * Dapatkan identifier untuk JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Dapatkan custom claims JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // ─── Relasi Eloquent ────────────────────────────────

    /**
     * Satu user memiliki banyak hasil kuis.
     */
    public function quizResults()
    {
        return $this->hasMany(QuizResult::class);
    }

    /**
     * Satu user memiliki banyak chat log.
     */
    public function chatLogs()
    {
        return $this->hasMany(ChatLog::class);
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
     * Cek apakah user adalah admin/guru.
     */
    public function isGuruOrAdmin(): bool
    {
        return in_array($this->role, ['admin', 'guru']);
    }
}