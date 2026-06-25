@extends('layouts.app')

@section('title', 'Review Soal AI')
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-check-double mr-2 text-purple-600"></i>Review Soal AI
    </h2>
    <div>
        <span class="text-sm text-gray-500 mr-4">Modul: <strong>{{ $kuis->module->judul }}</strong></span>
        <a href="{{ route('modul.show', $kuis->module) }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>
</div>

{{-- Info Kuis --}}
<div class="bg-white rounded-lg shadow p-4 mb-4">
    <div class="flex flex-wrap gap-4 text-sm">
        <div>
            <span class="text-gray-500">Judul Kuis:</span>
            <strong>{{ $kuis->judul }}</strong>
        </div>
        <div>
            <span class="text-gray-500">Durasi:</span>
            <strong>{{ $kuis->durasi_menit }} menit</strong>
        </div>
        <div>
            <span class="text-gray-500">Jumlah Soal:</span>
            <strong>{{ $kuis->questions->count() }}</strong>
        </div>
        <div>
            <span class="text-gray-500">Dibuat AI:</span>
            <strong>{{ $kuis->generated_at ? \Carbon\Carbon::parse($kuis->generated_at)->diffForHumans() : '-' }}</strong>
        </div>
        <div>
            <span class="text-gray-500">Status:</span>
            <span class="text-xs px-2 py-0.5 rounded {{ $kuis->aktif ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ $kuis->aktif ? 'Published' : 'Draft' }}
            </span>
        </div>
    </div>
</div>

{{-- Form Edit Soal --}}
<form action="{{ route('kuis.review.save', $kuis) }}" method="POST">
    @csrf

    @foreach ($kuis->questions as $index => $soal)
        <div class="bg-white rounded-lg shadow p-5 mb-4 border-l-4 border-purple-400">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-bold text-lg text-gray-700">Soal #{{ $index + 1 }}</h3>
                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded">AI Generated</span>
            </div>

            {{-- Pertanyaan --}}
            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-500 mb-1">Pertanyaan</label>
                <textarea name="soal[{{ $soal->id }}][pertanyaan]"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                          rows="2" required>{{ old("soal.{$soal->id}.pertanyaan", $soal->pertanyaan) }}</textarea>
            </div>

            {{-- Opsi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                @foreach (['a', 'b', 'c', 'd'] as $opsi)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Opsi {{ strtoupper($opsi) }}</label>
                        <div class="flex gap-2 items-start">
                            <input type="text" name="soal[{{ $soal->id }}][opsi_{{ $opsi }}]"
                                   value="{{ old("soal.{$soal->id}.opsi_{$opsi}", $soal->{'opsi_' . $opsi}) }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                                   required>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Jawaban Benar --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jawaban Benar</label>
                <select name="soal[{{ $soal->id }}][jawaban_benar]"
                        class="border border-gray-300 rounded px-3 py-2 text-sm" required>
                    @foreach (['a', 'b', 'c', 'd'] as $opsi)
                        <option value="{{ $opsi }}" {{ old("soal.{$soal->id}.jawaban_benar", $soal->jawaban_benar) == $opsi ? 'selected' : '' }}>
                            {{ strtoupper($opsi) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @endforeach

    {{-- Tombol Aksi --}}
    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded text-sm hover:bg-green-700 transition flex items-center">
            <i class="fas fa-save mr-2"></i> Simpan & Publish Kuis
        </button>
        <a href="{{ route('modul.show', $kuis->module) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300 transition">
            Batal (Kuis akan tetap draft)
        </a>
    </div>
</form>

@endsection