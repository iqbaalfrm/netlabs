<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat semua tabel skripsi: pertemuan, topik, soal_kuis,
     * hasil_kuis, chat_history, progress_topik, modul_pdf.
     */
    public function up(): void
    {
        // ─── Tabel Pertemuan ───────────────────────────────
        Schema::create('pertemuan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->integer('nomor_urut')->default(1);
            $table->string('warna_hex', 7)->default('#2D6A4F');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // ─── Tabel Topik ──────────────────────────────────
        Schema::create('topik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->cascadeOnDelete();
            $table->string('judul', 200);
            $table->text('isi')->nullable();
            $table->integer('nomor_urut')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // ─── Tabel Soal Kuis ──────────────────────────────
        Schema::create('soal_kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->cascadeOnDelete();
            $table->text('pertanyaan');
            $table->string('pilihan_a', 200);
            $table->string('pilihan_b', 200);
            $table->string('pilihan_c', 200);
            $table->string('pilihan_d', 200);
            $table->string('pilihan_e', 200);
            $table->char('kunci', 1)->comment('Jawaban benar: A/B/C/D/E');
            $table->text('penjelasan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // ─── Tabel Hasil Kuis ─────────────────────────────
        Schema::create('hasil_kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->cascadeOnDelete();
            $table->integer('skor')->default(0)->comment('Nilai 0-100');
            $table->integer('benar')->default(0);
            $table->integer('salah')->default(0);
            $table->timestamp('dikerjakan_pada')->useCurrent();
        });

        // ─── Tabel Chat History ───────────────────────────
        Schema::create('chat_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->cascadeOnDelete();
            $table->text('pesan');
            $table->text('jawaban');
            $table->timestamp('waktu')->useCurrent();
        });

        // ─── Tabel Progress Topik ─────────────────────────
        Schema::create('progress_topik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('topik_id')->constrained('topik')->cascadeOnDelete();
            $table->boolean('is_selesai')->default(false);
            $table->timestamp('selesai_pada')->nullable();
        });

        // ─── Tabel Modul PDF ──────────────────────────────
        Schema::create('modul_pdf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->cascadeOnDelete();
            $table->string('nama_file', 200);
            $table->string('path', 500);
            $table->bigInteger('ukuran_bytes')->default(0);
            $table->foreignId('diupload_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Hapus semua tabel saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('modul_pdf');
        Schema::dropIfExists('progress_topik');
        Schema::dropIfExists('chat_history');
        Schema::dropIfExists('hasil_kuis');
        Schema::dropIfExists('soal_kuis');
        Schema::dropIfExists('topik');
        Schema::dropIfExists('pertemuan');
    }
};