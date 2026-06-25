@extends('layouts.app')
@section('title', 'Data Siswa')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">
    <i class="fas fa-users text-primary mr-2"></i>Data Siswa
</h2>

{{-- Search --}}
<div class="bg-white rounded-lg shadow p-4 mb-4">
    <form action="{{ route('siswa.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau email..."
               class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded transition">
            <i class="fas fa-search mr-1"></i>Cari
        </button>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Nama</th>
                <th class="text-left px-4 py-3">Email</th>
                <th class="text-center px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($siswa as $s)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $s->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $s->email }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('siswa.show', $s->id) }}"
                       class="bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded transition text-xs">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-gray-400">Tidak ada data siswa</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $siswa->links() }}
</div>
@endsection