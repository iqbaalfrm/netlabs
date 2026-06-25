<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration — buat tabel quiz_results.
     */
    public function up(): void
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->integer('skor');                    // Skor yang diperoleh
            $table->integer('total_soal');              // Total soal saat dikerjakan
            $table->json('jawaban')->nullable();        // { soal_id: "a", ... }
            $table->timestamp('selesai_at')->nullable();// Waktu selesai
            $table->timestamps();
        });
    }

    /**
     * Balikkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
    }
};