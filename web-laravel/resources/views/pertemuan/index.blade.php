@extends('layouts.app')
@section('title', 'Daftar Pertemuan')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-book text-primary mr-2"></i>Daftar Pertemuan
    </h2>
    <a href="{{ route('pertemuan.create') }}"
       class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded transition">
        <i class="fas fa-plus mr-2"></i>Tambah Pertemuan
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">#</th>
                <th class="text-left px-4 py-3">Urutan</th>
                <th class="text-left px-4 py-3">Judul</th>
                <th class="text-left px-4 py-3">Topik</th>
                <th class="text-left px-4 py-3">Soal</th>
                <th class="text-left px-4 py-3">Tanggal</th>
                <th class="text-center px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($pertemuan as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-700">{{ $p->id }}</td>
                <td class="px-4 py-3">
                    <span class="bg-primary text-white text-xs px-2 py-1 rounded-full">{{ $p->urutan }}</span>
                </td>
                <td class="px-4 py-3 font-medium text-gray-800">
                    <a href="{{ route('pertemuan.show', $p->id) }}" class="hover:text-primary">
                        {{ $p->judul }}
                    </a>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $p->topik_count }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $p->soal_kuis_count }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $p->tanggal ? date('d/m/Y', strtotime($p->tanggal)) : '-' }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('pertemuan.show', $p->id) }}" class="text-primary hover:text-primary-dark mr-2"
                       title="Detail"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('pertemuan.edit', $p->id) }}" class="text-yellow-500 hover:text-yellow-600 mr-2"
                       title="Edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('pertemuan.destroy', $p->id) }}" method="POST" class="inline"
                          onsubmit="event.preventDefault(); confirmDelete(this);">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-600" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                    Belum ada pertemuan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection