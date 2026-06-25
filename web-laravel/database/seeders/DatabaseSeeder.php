<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pertemuan;
use App\Models\Topik;
use App\Models\SoalKuis;
use App\Models\ModulPdf;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan data awal Netlabs.
     */
    public function run(): void
    {
        // ─── 1. Admin Guru ────────────────────────────────
        $guru = User::create([
            'name'     => 'Pak Guru Netlabs',
            'email'    => 'guru@netlabs.id',
            'password' => Hash::make('guru123'),
            'role'     => 'guru',
            'kelas'    => null,
            'no_hp'    => '081234567890',
            'aktif'    => true,
        ]);

        // ─── 2. Siswa Dummy ──────────────────────────────
        $siswaIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $siswa = User::create([
                'name'     => "Siswa $i",
                'email'    => "siswa{$i}@netlabs.test",
                'password' => Hash::make('password'),
                'role'     => 'siswa',
                'kelas'    => 'XI TKJ ' . ($i % 2 + 1),
                'no_hp'    => '08' . rand(100000000, 999999999),
                'aktif'    => true,
            ]);
            $siswaIds[] = $siswa->id;
        }

        // ─── 3. Pertemuan + Topik + Soal ─────────────────
        $p1 = Pertemuan::create([
            'judul'      => 'Pengenalan Jaringan Komputer',
            'deskripsi'  => 'Konsep dasar jaringan komputer, topologi, dan IP address.',
            'nomor_urut' => 1,
            'warna_hex'  => '#2D6A4F',
            'dibuat_oleh' => $guru->id,
        ]);

        Topik::insert([
            ['pertemuan_id' => $p1->id, 'judul' => 'Definisi Jaringan',    'isi' => 'Jaringan komputer adalah sekumpulan perangkat yang saling terhubung untuk berbagi data dan sumber daya.', 'nomor_urut' => 1],
            ['pertemuan_id' => $p1->id, 'judul' => 'Topologi Jaringan',    'isi' => 'Topologi jaringan meliputi Bus, Star, Ring, Mesh, dan Hybrid. Masing-masing memiliki kelebihan dan kekurangan.', 'nomor_urut' => 2],
            ['pertemuan_id' => $p1->id, 'judul' => 'IP Address & Subnetting', 'isi' => 'IP Address adalah alamat unik perangkat dalam jaringan. Kelas IP: A (1-126), B (128-191), C (192-223).', 'nomor_urut' => 3],
        ]);

        SoalKuis::insert([
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'Apa kepanjangan dari LAN?', 'pilihan_a' => 'Local Area Network', 'pilihan_b' => 'Large Area Network', 'pilihan_c' => 'Long Area Network', 'pilihan_d' => 'Light Area Network', 'pilihan_e' => 'Line Area Network', 'kunci' => 'A', 'penjelasan' => 'LAN singkatan dari Local Area Network.'],
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'Topologi yang semua node terhubung ke satu kabel disebut?', 'pilihan_a' => 'Star', 'pilihan_b' => 'Ring', 'pilihan_c' => 'Bus', 'pilihan_d' => 'Mesh', 'pilihan_e' => 'Tree', 'kunci' => 'C', 'penjelasan' => 'Topologi Bus menggunakan kabel backbone tunggal.'],
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'IP 192.168.1.1 termasuk kelas?', 'pilihan_a' => 'A', 'pilihan_b' => 'B', 'pilihan_c' => 'C', 'pilihan_d' => 'D', 'pilihan_e' => 'E', 'kunci' => 'C', 'penjelasan' => 'Range kelas C: 192.0.0.0 s/d 223.255.255.255.'],
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'Port default HTTP adalah?', 'pilihan_a' => '21', 'pilihan_b' => '80', 'pilihan_c' => '443', 'pilihan_d' => '22', 'pilihan_e' => '8080', 'kunci' => 'B', 'penjelasan' => 'HTTP berjalan di port 80, sedangkan HTTPS di port 443.'],
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'Subnet mask default kelas C adalah?', 'pilihan_a' => '255.0.0.0', 'pilihan_b' => '255.255.0.0', 'pilihan_c' => '255.255.255.0', 'pilihan_d' => '255.255.255.255', 'pilihan_e' => '255.255.255.128', 'kunci' => 'C', 'penjelasan' => 'Kelas C: 24 bit network, subnet mask 255.255.255.0.'],
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'Protokol yang digunakan untuk mengirim email adalah?', 'pilihan_a' => 'FTP', 'pilihan_b' => 'SMTP', 'pilihan_c' => 'HTTP', 'pilihan_d' => 'DNS', 'pilihan_e' => 'DHCP', 'kunci' => 'B', 'penjelasan' => 'SMTP (Simple Mail Transfer Protocol) untuk mengirim email.'],
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'Alat yang menghubungkan dua segmen jaringan berbeda adalah?', 'pilihan_a' => 'Hub', 'pilihan_b' => 'Switch', 'pilihan_c' => 'Router', 'pilihan_d' => 'Repeater', 'pilihan_e' => 'Bridge', 'kunci' => 'C', 'penjelasan' => 'Router menghubungkan jaringan berbeda dan melakukan routing.'],
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'Lapisan OSI yang bertanggung jawab atas routing adalah?', 'pilihan_a' => 'Physical', 'pilihan_b' => 'Data Link', 'pilihan_c' => 'Network', 'pilihan_d' => 'Transport', 'pilihan_e' => 'Session', 'kunci' => 'C', 'penjelasan' => 'Network layer (lapisan 3) menangani routing dan logical addressing.'],
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'DNS singkatan dari?', 'pilihan_a' => 'Dynamic Network System', 'pilihan_b' => 'Domain Name System', 'pilihan_c' => 'Digital Network Service', 'pilihan_d' => 'Data Network System', 'pilihan_e' => 'Domain Network Server', 'kunci' => 'B', 'penjelasan' => 'DNS menerjemahkan nama domain ke IP address.'],
            ['pertemuan_id' => $p1->id, 'pertanyaan' => 'Kabel UTP Cat 5e mendukung kecepatan maksimal?', 'pilihan_a' => '10 Mbps', 'pilihan_b' => '100 Mbps', 'pilihan_c' => '1 Gbps', 'pilihan_d' => '10 Gbps', 'pilihan_e' => '100 Gbps', 'kunci' => 'C', 'penjelasan' => 'Cat 5e mendukung Gigabit Ethernet (1000 Mbps / 1 Gbps).'],
        ]);

        // ─── 4. Pertemuan Kedua ───────────────────────────
        $p2 = Pertemuan::create([
            'judul'      => 'Perangkat Keras Jaringan',
            'deskripsi'  => 'Mengenal switch, router, access point, dan kabel jaringan.',
            'nomor_urut' => 2,
            'warna_hex'  => '#1B5E20',
            'dibuat_oleh' => $guru->id,
        ]);

        Topik::insert([
            ['pertemuan_id' => $p2->id, 'judul' => 'Switch & Hub', 'isi' => 'Switch bekerja di layer 2 OSI dan meneruskan frame berdasarkan MAC address. Hub meneruskan ke semua port.', 'nomor_urut' => 1],
            ['pertemuan_id' => $p2->id, 'judul' => 'Router & Gateway', 'isi' => 'Router bekerja di layer 3 OSI dan menghubungkan jaringan berbeda dengan routing table.', 'nomor_urut' => 2],
        ]);

        SoalKuis::insert([
            ['pertemuan_id' => $p2->id, 'pertanyaan' => 'Switch bekerja di layer OSI ke berapa?', 'pilihan_a' => '1', 'pilihan_b' => '2', 'pilihan_c' => '3', 'pilihan_d' => '4', 'pilihan_e' => '5', 'kunci' => 'B', 'penjelasan' => 'Switch bekerja di layer 2 (Data Link) OSI.'],
            ['pertemuan_id' => $p2->id, 'pertanyaan' => 'Perbedaan utama switch dan hub adalah?', 'pilihan_a' => 'Harga', 'pilihan_b' => 'Jumlah port', 'pilihan_c' => 'Switch meneruskan berdasarkan MAC', 'pilihan_d' => 'Hub lebih cepat', 'pilihan_e' => 'Switch hanya untuk WAN', 'kunci' => 'C', 'penjelasan' => 'Switch meneruskan berdasarkan MAC address, hub meneruskan ke semua port.'],
            ['pertemuan_id' => $p2->id, 'pertanyaan' => 'Access point digunakan untuk jaringan?', 'pilihan_a' => 'Kabel', 'pilihan_b' => 'Fiber optic', 'pilihan_c' => 'Nirkabel / WiFi', 'pilihan_d' => 'Satelit', 'pilihan_e' => 'Bluetooth', 'kunci' => 'C', 'penjelasan' => 'Access point memancarkan sinyal WiFi untuk jaringan nirkabel.'],
        ]);

        $this->command->info('✅ Seeder skripsi berhasil!');
        $this->command->info('   Login guru: guru@netlabs.id / guru123');
        $this->command->info('   Login siswa: siswa1@netlabs.test / password');
    }
}