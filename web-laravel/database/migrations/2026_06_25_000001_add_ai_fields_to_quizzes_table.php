<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom jumlah_soal, is_generated, generated_at ke tabel quizzes.
     */
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('jumlah_soal')->default(10)->after('durasi_menit');
            $table->boolean('is_generated')->default(false)->after('jumlah_soal');
            $table->timestamp('generated_at')->nullable()->after('is_generated');
        });
    }

    /**
     * Balikkan migration.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['jumlah_soal', 'is_generated', 'generated_at']);
        });
    }
};