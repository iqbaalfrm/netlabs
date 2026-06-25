@extends('layouts.app')

@section('title', 'Generate Soal AI')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-robot mr-2 text-purple-600"></i>Generate Soal AI (Gemini)
    </h2>
    <a href="{{ route('modul.show', $module) }}" class="text-gray-600 hover:text-gray-800">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Modul
    </a>
</div>

{{-- Info Modul --}}
<div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-6 rounded">
    <p class="text-sm text-purple-800">
        <strong>Modul:</strong> {{ $module->judul }}<br>
        <strong>Jumlah Materi:</strong> {{ $module->materials->count() }} materi<br>
        <small class="text-purple-600">Gemini akan membaca seluruh materi modul untuk membuat soal pilihan ganda relevan.</small>
    </p>
</div>

{{-- Form Generate --}}
<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form action="{{ route('kuis.generate', $module) }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kuis</label>
            <input type="text" name="judul" value="{{ old('judul', 'Kuis AI: ' . $module->judul) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                   required maxlength="255">
            @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Soal (5–50)</label>
            <input type="number" name="jumlah_soal" value="{{ old('jumlah_soal', 10) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                   min="5" max="50" required>
            @error('jumlah_soal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit)</label>
            <input type="number" name="durasi_menit" value="{{ old('durasi_menit', 30) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                   min="1" required>
            @error('durasi_menit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded text-sm hover:bg-purple-700 transition flex items-center">
                <i class="fas fa-magic mr-2"></i> Generate Soal
            </button>
            <a href="{{ route('modul.show', $module) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300 transition">Batal</a>
        </div>
    </form>
</div>

{{-- Tips --}}
<div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded max-w-lg">
    <p class="text-sm text-yellow-800">
        <i class="fas fa-info-circle mr-1"></i>
        <strong>Tips:</strong> Pastikan modul memiliki materi (konten) terlebih dahulu. Semakin lengkap materi, semakin baik soal yang dihasilkan Gemini. Proses generate memakan waktu 10–30 detik.
    </p>
</div>

@endsection