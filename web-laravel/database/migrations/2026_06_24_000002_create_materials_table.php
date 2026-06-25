<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration — buat tabel materials.
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('judul');                        // Judul materi
            $table->text('konten')->nullable();             // Isi materi (HTML/Markdown)
            $table->string('file_pdf')->nullable();         // Path file PDF di storage
            $table->string('gambar')->nullable();           // Path gambar di storage
            $table->integer('urutan')->default(0);          // Urutan dalam modul
            $table->boolean('aktif')->default(true);        // Status aktif
            $table->timestamps();
        });
    }

    /**
     * Balikkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};