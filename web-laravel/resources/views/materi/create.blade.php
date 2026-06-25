@extends('layouts.app')

@section('title', 'Tambah Materi')
@section('content')

<h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-plus-circle mr-2 text-primary"></i>Tambah Materi — {{ $module->judul }}</h2>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('materi.store', $module) }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Judul Materi <span class="text-red-500">*</span></label>
            <input type="text" name="judul" value="{{ old('judul') }}" required
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary">
        </div>

        {{-- Konten (editor sederhana) --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Konten</label>
            <textarea name="konten" rows="10"
                      class="w-full border border-gray-300 rounded px-3 py-2 font-mono text-sm focus:outline-none focus:border-primary"
                      placeholder="Tulis konten materi di sini...">{{ old('konten') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Urutan <span class="text-red-500">*</span></label>
            <input type="number" name="urutan" value="{{ old('urutan', 1) }}" min="1" required
                   class="w-24 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary">
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded hover:bg-primary-dark transition"><i class="fas fa-save mr-1"></i> Simpan</button>
            <a href="{{ route('modul.show', $module) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 transition">Batal</a>
        </div>
    </form>
</div>

@endsection