<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom nis dan rename name → nama di tabel users.
     * 
     * Kolom nis: unik untuk siswa (login via NIS), nullable untuk guru (login via email).
     * Kolom nama: rename dari 'name' (Laravel default) agar konsisten dengan model & API.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom nis (unique, nullable untuk guru)
            if (!Schema::hasColumn('users', 'nis')) {
                $table->string('nis')->nullable()->unique()->after('id');
            }

            // Rename name → nama (jika name ada dan nama belum ada)
            if (Schema::hasColumn('users', 'name') && !Schema::hasColumn('users', 'nama')) {
                $table->renameColumn('name', 'nama');
            }

            // Jika nama belum ada sama sekali (fresh install tanpa name), buat kolom nama
            if (!Schema::hasColumn('users', 'nama')) {
                $table->string('nama')->nullable()->after('nis');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename kembali nama → name
            if (Schema::hasColumn('users', 'nama') && !Schema::hasColumn('users', 'name')) {
                $table->renameColumn('nama', 'name');
            }

            // Hapus kolom nis
            if (Schema::hasColumn('users', 'nis')) {
                $table->dropUnique(['nis']);
                $table->dropColumn('nis');
            }
        });
    }
};