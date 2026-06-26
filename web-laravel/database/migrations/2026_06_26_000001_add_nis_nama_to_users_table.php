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
        // Step 1: Tambah kolom nis (unique, nullable untuk guru)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nis')) {
                $table->string('nis')->nullable()->unique()->after('id');
            }
        });

        // Step 2: Rename name → nama (harum dipisah agar Schema::hasColumn
        //         melihat hasil rename sebelum cek berikutnya)
        if (Schema::hasColumn('users', 'name') && !Schema::hasColumn('users', 'nama')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
            });
        }

        // Step 3: Jika nama belum ada sama sekali (fresh install tanpa name), buat kolom nama
        if (!Schema::hasColumn('users', 'nama')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nama')->nullable()->after('nis');
            });
        }
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