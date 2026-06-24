<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Cache;

class SeedSupabaseDummy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supabase:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed Supabase database with dummy classes (X TKJ 1-4) and 10 students per class';

    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        parent::__construct();
        $this->supabase = $supabase;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $classes = ['X TKJ 1', 'X TKJ 2', 'X TKJ 3', 'X TKJ 4'];
        
        $firstNames = [
            'Aditya', 'Bagas', 'Chandra', 'Dimas', 'Erlangga', 'Fahmi', 'Galih', 'Hendra', 'Irfan', 'Jaka',
            'Kiki', 'Lutfi', 'Maulana', 'Naufal', 'Okta', 'Pandu', 'Rian', 'Satria', 'Tegar', 'Wahyu',
            'Anisa', 'Bella', 'Citra', 'Dian', 'Elisa', 'Fitri', 'Gita', 'Hana', 'Indah', 'Jihan',
            'Kartika', 'Laras', 'Mega', 'Nadia', 'Olivia', 'Putri', 'Rara', 'Sari', 'Tiara', 'Wulan'
        ];

        $lastNames = [
            'Pratama', 'Santoso', 'Wijaya', 'Kurniawan', 'Hidayat', 'Saputra', 'Setiawan', 'Wibowo', 'Nugroho', 'Ramadhan',
            'Lestari', 'Putri', 'Sari', 'Amalia', 'Rahmawati', 'Fitriani', 'Indah', 'Utami', 'Kusuma', 'Wardani'
        ];

        $url = config('services.supabase.url');
        $key = config('services.supabase.service_key');

        $this->info('Membersihkan data dummy siswa kelas XII sebelumnya (jika ada)...');
        \Illuminate\Support\Facades\Http::withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer ' . $key,
        ])->delete($url . '/rest/v1/users?nis=gte.242510001&nis=lte.242510040');

        $this->info('Memulai seeding data dummy Kelas X ke Supabase...');

        // 1. Ambil kelas yang sudah ada di Supabase untuk menghindari duplikasi
        $existingKelas = $this->supabase->getKelas();
        $existingKelasNames = array_column($existingKelas, 'nama_kelas');

        foreach ($classes as $className) {
            if (!in_array($className, $existingKelasNames)) {
                $this->info("Membuat kelas: {$className}...");
                $res = $this->supabase->createKelas(['nama_kelas' => $className]);
                if ($res['success']) {
                    $this->info("Kelas {$className} berhasil dibuat.");
                } else {
                    $this->warn("Gagal membuat kelas {$className}: " . ($res['message'] ?? ''));
                }
            } else {
                $this->line("Kelas {$className} sudah ada, melewati pembuatan.");
            }
        }

        // 2. Ambil siswa yang sudah ada untuk menghindari duplikat NIS
        $existingSiswa = $this->supabase->getSiswa();
        $existingNis = array_column($existingSiswa, 'nis');

        $nisCounter = 242510001; // Format NIS dummy
        $studentIndex = 0;

        foreach ($classes as $className) {
            $this->info("\nMemasukkan siswa untuk kelas: {$className}");
            $createdCount = 0;

            for ($i = 1; $i <= 10; $i++) {
                // Generate NIS unik
                while (in_array((string)$nisCounter, $existingNis)) {
                    $nisCounter++;
                }
                
                $nis = (string)$nisCounter;
                $existingNis[] = $nis;

                // Generate nama acak
                $firstName = $firstNames[($studentIndex + $i) % count($firstNames)];
                $lastName = $lastNames[($studentIndex + $i * 3) % count($lastNames)];
                $nama = "{$firstName} {$lastName}";

                $password = $nis;
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $siswaData = [
                    'nama' => $nama,
                    'nis' => $nis,
                    'kelas' => $className,
                    'role' => 'siswa',
                    'password_hash' => $passwordHash,
                    'sekolah' => 'SMK Astrindo Tegal',
                ];

                $res = $this->supabase->createSiswa($siswaData);
                if ($res['success']) {
                    $createdCount++;
                    $this->line("  [{$createdCount}/10] Siswa {$nama} (NIS: {$nis}) berhasil didaftarkan.");
                } else {
                    $this->error("  Gagal mendaftarkan {$nama}: " . ($res['message'] ?? ''));
                }

                $nisCounter++;
            }
            $studentIndex += 13;
        }

        // 3. Clear cache
        Cache::forget('supabase_kelas');
        Cache::forget('supabase_siswa');

        $this->info("\nSeeding selesai! Semua kelas X TKJ 1-4 telah diisi masing-masing 10 siswa dummy.");
    }
}
