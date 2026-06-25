@extends('layouts.app')
@section('title', 'Detail Siswa')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-user-graduate text-primary mr-2"></i>{{ $siswa->name }}
    </h2>
    <a href="{{ route('siswa.index') }}"
       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded transition text-sm">
        <i class="fas fa-arrow-left mr-1"></i>Kembali
    </a>
</div>

{{-- Profil --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-3">
        <i class="fas fa-id-card text-primary mr-2"></i>Profil
    </h3>
    <div class="grid grid-cols-2 gap-3 text-sm">
        <div><span class="text-gray-500">NIS:</span> <span class="font-semibold">{{ $siswa->nis ?? '-' }}</span></div>
        <div><span class="text-gray-500">Nama:</span> <span class="font-semibold">{{ $siswa->name }}</span></div>
        <div><span class="text-gray-500">Kelas:</span> <span class="font-semibold">{{ $siswa->kelas ?? '-' }}</span></div>
        <div><span class="text-gray-500">Email:</span> <span class="font-semibold">{{ $siswa->email ?? '-' }}</span></div>
    </div>
</div>

{{-- 2 Tab: Riwayat Kuis | Riwayat Chat --}}
<div x-data="{ tab: 'kuis' }">
    <div class="flex border-b border-gray-200 mb-4">
        <button @click="tab = 'kuis'" :class="tab === 'kuis' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-gray-500'"
                class="px-4 py-2 text-sm transition">
            <i class="fas fa-clipboard-check mr-1"></i>Riwayat Kuis
        </button>
        <button @click="tab = 'chat'" :class="tab === 'chat' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-gray-500'"
                class="px-4 py-2 text-sm transition">
            <i class="fas fa-comments mr-1"></i>Riwayat Chat
        </button>
    </div>

    {{-- Tab: Riwayat Kuis --}}
    <div x-show="tab === 'kuis'" x-cloak>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Pertemuan</th>
                        <th class="text-center px-4 py-3">Benar</th>
                        <th class="text-center px-4 py-3">Total</th>
                        <th class="text-center px-4 py-3">Nilai</th>
                        <th class="text-left px-4 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($riwayatKuis as $h)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">{{ $h->pertemuan->judul ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">{{ $h->jumlah_benar }}</td>
                        <td class="px-4 py-3 text-center">{{ $h->jumlah_soal }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-primary/10 text-primary text-xs px-2 py-1 rounded-full font-semibold">
                                {{ $h->nilai }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada riwayat kuis</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tab: Riwayat Chat --}}
    <div x-show="tab === 'chat'" x-cloak>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Pertanyaan</th>
                        <th class="text-left px-4 py-3">Jawaban</th>
                        <th class="text-left px-4 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($riwayatChat as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">{{ Str::limit($c->pertanyaan, 60) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ Str::limit($c->jawaban, 80) }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada riwayat chat</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
