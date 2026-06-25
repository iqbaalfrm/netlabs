<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration — buat tabel questions (soal kuis).
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->text('pertanyaan');                         // Teks soal
            $table->string('opsi_a');                           // Pilihan A
            $table->string('opsi_b');                           // Pilihan B
            $table->string('opsi_c');                           // Pilihan C
            $table->string('opsi_d');                           // Pilihan D
            $table->enum('jawaban_benar', ['a', 'b', 'c', 'd']);// Kunci jawaban
            $table->timestamps();
        });
    }

    /**
     * Balikkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};