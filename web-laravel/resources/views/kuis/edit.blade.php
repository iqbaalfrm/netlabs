@extends('layouts.app')
@section('title', 'Edit Soal Kuis')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">
    <i class="fas fa-edit text-primary mr-2"></i>Edit Soal Kuis #{{ $soal->id }}
</h2>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('kuis.update', $soal->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pertanyaan</label>
            <textarea name="pertanyaan" rows="3" required
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
        </div>

        @foreach (['a'=>'Opsi A','b'=>'Opsi B','c'=>'Opsi C','d'=>'Opsi D'] as $k => $label)
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">{{ $label }}</label>
            <input type="text" name="opsi_{{ $k }}" value="{{ old('opsi_'.$k, $soal->{'opsi_'.$k}) }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        @endforeach

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Kunci Jawaban</label>
            <select name="kunci_jawaban" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">-- Pilih --</option>
                @foreach (['A', 'B', 'C', 'D'] as $k)
                <option value="{{ $k }}" {{ old('kunci_jawaban', $soal->kunci_jawaban) == $k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded transition">
                <i class="fas fa-save mr-2"></i>Update
            </button>
            <a href="{{ route('pertemuan.show', $soal->pertemuan_id) }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded transition">Batal</a>
        </div>
    </form>
</div>
@endsection