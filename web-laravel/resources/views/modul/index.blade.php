@extends('layouts.app')

@section('title', 'Daftar Modul')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-book mr-2 text-primary"></i>Daftar Modul
    </h2>
    <a href="{{ route('modul.create') }}" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark transition">
        <i class="fas fa-plus mr-1"></i> Tambah Modul
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-6 py-3 w-16">#</th>
                <th class="text-left px-6 py-3">Judul</th>
                <th class="text-left px-6 py-3">Urutan</th>
                <th class="text-left px-6 py-3">Status</th>
                <th class="text-center px-6 py-3 w-48">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse ($modules as $modul)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-500">{{ $modul->urutan }}</td>
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $modul->judul }}</td>
                    <td class="px-6 py-3">{{ $modul->urutan }}</td>
                    <td class="px-6 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $modul->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $modul->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <a href="{{ route('modul.show', $modul) }}" class="text-blue-600 hover:text-blue-800 mr-2" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('modul.edit', $modul) }}" class="text-yellow-600 hover:text-yellow-800 mr-2" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('modul.destroy', $modul) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus modul ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada modul. <a href="{{ route('modul.create') }}" class="text-primary underline">Tambah sekarang</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection