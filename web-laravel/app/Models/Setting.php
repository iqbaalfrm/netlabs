<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = ['key', 'value'];

    // ─── Static Helper ──────────────────────────────────

    /**
     * Ambil nilai setting berdasarkan key.
     * Return default jika tidak ditemukan.
     */
    public static function get(string $key, $default = null): ?string
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Simpan atau update setting berdasarkan key.
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}