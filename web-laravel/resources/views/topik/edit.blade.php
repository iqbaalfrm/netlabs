@extends('layouts.app')
@section('title', 'Edit Topik')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">
    <i class="fas fa-edit text-primary mr-2"></i>Edit Topik - {{ $topik->judul }}
</h2>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('topik.update', $topik->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Judul Topik</label>
            <input type="text" name="judul" value="{{ old('judul', $topik->judul) }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Isi Materi</label>
            <textarea name="isi" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">{{ old('isi', $topik->isi) }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Urut</label>
            <input type="number" name="nomor_urut" value="{{ old('nomor_urut', $topik->nomor_urut) }}" required min="1"
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded transition">
                <i class="fas fa-save mr-2"></i>Update
            </button>
            <a href="{{ route('pertemuan.show', $topik->pertemuan_id) }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded transition">Batal</a>
        </div>
    </form>
</div>
@endsection