@extends('layouts.app')
@section('title', 'Edit Pertemuan')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">
    <i class="fas fa-edit text-primary mr-2"></i>Edit Pertemuan
</h2>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('pertemuan.update', $pertemuan->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Judul</label>
            <input type="text" name="judul" value="{{ old('judul', $pertemuan->judul) }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
            <textarea name="deskripsi" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">{{ old('deskripsi', $pertemuan->deskripsi) }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $pertemuan->urutan) }}" required min="1"
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $pertemuan->tanggal) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Kode Ruangan</label>
                <input type="text" name="kode_ruangan" value="{{ old('kode_ruangan', $pertemuan->kode_ruangan) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded transition">
                <i class="fas fa-save mr-2"></i>Update
            </button>
            <a href="{{ route('pertemuan.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded transition">Batal</a>
        </div>
    </form>
</div>
@endsection