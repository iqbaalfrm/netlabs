<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration — buat tabel modules.
     */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('judul');                // Nama modul
            $table->text('deskripsi')->nullable();  // Deskripsi modul
            $table->integer('urutan')->default(0);  // Urutan tampil
            $table->boolean('aktif')->default(true);// Status aktif
            $table->timestamps();                   // created_at, updated_at
        });
    }

    /**
     * Balikkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};