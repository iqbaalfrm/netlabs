@extends('layouts.app')

@section('title', 'Tambah Soal')
@section('content')

<h2 class="text-2xl font-bold text-gray-800 mb-6">
    <i class="fas fa-list-ol mr-2 text-primary"></i>Tambah Soal — {{ $quiz->judul }}
</h2>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('soal.store', $quiz) }}" method="POST">
        @csrf

        {{-- Pertanyaan --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Pertanyaan <span class="text-red-500">*</span></label>
            <textarea name="pertanyaan" rows="3" required
                      class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary">{{ old('pertanyaan') }}</textarea>
        </div>

        {{-- 4 Pilihan --}}
        @foreach (['a', 'b', 'c', 'd'] as $opsi)
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-medium mb-1">Pilihan {{ strtoupper($opsi) }} <span class="text-red-500">*</span></label>
                <input type="text" name="pilihan_{{ $opsi }}" value="{{ old('pilihan_' . $opsi) }}" required
                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary">
            </div>
        @endforeach

        {{-- Kunci Jawaban --}}
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-1">Kunci Jawaban <span class="text-red-500">*</span></label>
            <select name="kunci_jawaban" required
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary">
                <option value="">— Pilih —</option>
                @foreach (['a', 'b', 'c', 'd'] as $opsi)
                    <option value="{{ $opsi }}" {{ old('kunci_jawaban') === $opsi ? 'selected' : '' }}>{{ strtoupper($opsi) }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded hover:bg-primary-dark transition">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="{{ route('kuis.edit', $quiz) }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400 transition">Batal</a>
        </div>
    </form>
</div>

@endsection