@extends('layouts.app')
@section('title', 'Tambah Topik')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">
    <i class="fas fa-plus-circle text-primary mr-2"></i>Tambah Topik - {{ $pertemuan->judul }}
</h2>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('topik.store', $pertemuan->id) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Judul Topik</label>
            <input type="text" name="judul" value="{{ old('judul') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Isi Materi</label>
            <textarea name="isi" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">{{ old('isi') }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Urut</label>
            <input type="number" name="nomor_urut" value="{{ old('nomor_urut', 1) }}" required min="1"
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded transition">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
            <a href="{{ route('pertemuan.show', $pertemuan->id) }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded transition">Batal</a>
        </div>
    </form>
</div>
@endsection