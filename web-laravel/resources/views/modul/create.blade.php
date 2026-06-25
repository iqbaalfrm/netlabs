@extends('layouts.app')

@section('title', 'Tambah Modul')
@section('content')

<div class="max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-plus-circle mr-2 text-primary"></i>Tambah Modul
    </h2>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('modul.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Judul Modul <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul') }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Urutan <span class="text-red-500">*</span></label>
                <input type="number" name="urutan" value="{{ old('urutan', 1) }}" min="1" required
                       class="w-24 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-primary text-white px-6 py-2 rounded hover:bg-primary-dark transition">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <a href="{{ route('modul.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection