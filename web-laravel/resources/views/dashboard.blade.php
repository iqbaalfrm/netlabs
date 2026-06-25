@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">
    <i class="fas fa-tachometer-alt text-primary mr-2"></i>Dashboard
</h2>

{{-- 4 Stat Card --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Siswa</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalSiswa }}</p>
            </div>
            <i class="fas fa-users text-blue-500 text-3xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Pertemuan</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalPertemuan }}</p>
            </div>
            <i class="fas fa-book text-green-500 text-3xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Soal Kuis</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalSoal }}</p>
            </div>
            <i class="fas fa-question-circle text-yellow-500 text-3xl"></i>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Chat</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalChat }}</p>
            </div>
            <i class="fas fa-comments text-purple-500 text-3xl"></i>
        </div>
    </div>
</div>

{{-- 10 Chat Terbaru --}}
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-comment-dots text-primary mr-2"></i>10 Chat Terbaru
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="text-left px-4 py-3">Siswa</th>
                    <th class="text-left px-4 py-3">Pesan</th>
                    <th class="text-left px-4 py-3">Balasan AI</th>
                    <th class="text-left px-4 py-3">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($chatTerbaru as $chat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700">
                        {{ $chat->siswa->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate">
                        {{ \Illuminate\Support\Str::limit($chat->pesan, 50) }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate">
                        {{ \Illuminate\Support\Str::limit($chat->balasan, 50) }}
                    </td>
                    <td class="px-4 py-3 text-gray-500">
                        {{ $chat->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                        <i class="fas fa-inbox text-3xl mb-2 block"></i>
                        Belum ada chat
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection