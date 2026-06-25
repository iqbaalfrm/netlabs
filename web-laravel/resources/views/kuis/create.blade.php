@extends('layouts.app')
@section('title', 'Tambah Soal Kuis')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">
    <i class="fas fa-plus-circle text-primary mr-2"></i>Tambah Soal Kuis - {{ $pertemuan->judul }}
</h2>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('kuis.store', $pertemuan->id) }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pertanyaan</label>
            <textarea name="pertanyaan" rows="3" required
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">{{ old('pertanyaan') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Opsi A</label>
            <input type="text" name="opsi_a" value="{{ old('opsi_a') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Opsi B</label>
            <input type="text" name="opsi_b" value="{{ old('opsi_b') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Opsi C</label>
            <input type="text" name="opsi_c" value="{{ old('opsi_c') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Opsi D</label>
            <input type="text" name="opsi_d" value="{{ old('opsi_d') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Kunci Jawaban</label>
            <select name="kunci_jawaban" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">-- Pilih --</option>
                @foreach (['A', 'B', 'C', 'D'] as $k)
                <option value="{{ $k }}" {{ old('kunci_jawaban') == $k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
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