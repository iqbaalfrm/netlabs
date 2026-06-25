<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom sudah_diindex ke tabel modul_pdf
     * untuk tracking apakah PDF sudah diindex ke ChromaDB (RAG).
     */
    public function up(): void
    {
        Schema::table('modul_pdf', function (Blueprint $table) {
            $table->boolean('sudah_diindex')->default(false)->after('diupload_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('modul_pdf', function (Blueprint $table) {
            $table->dropColumn('sudah_diindex');
        });
    }
};