@extends('layouts.app')

@section('title', $module->judul)
@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-book-open mr-2 text-primary"></i>{{ $module->judul }}
    </h2>
    <a href="{{ route('modul.index') }}" class="text-gray-600 hover:text-gray-800">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

@if ($module->deskripsi)
    <p class="text-gray-600 mb-6">{{ $module->deskripsi }}</p>
@endif

{{-- Tabs --}}
<div class="bg-white rounded-lg shadow">
    <div class="flex border-b">
        <button onclick="switchTab('materi')" id="tab-materi"
                class="tab-btn px-6 py-3 text-sm font-medium border-b-2 border-primary text-primary">
            <i class="fas fa-list mr-1"></i> Materi ({{ $module->materials->count() }})
        </button>
        <button onclick="switchTab('kuis')" id="tab-kuis"
                class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">
            <i class="fas fa-question-circle mr-1"></i> Kuis ({{ $module->quizzes->count() }})
        </button>
    </div>

    {{-- Tab Materi --}}
    <div id="content-materi" class="p-6">
        <a href="{{ route('materi.create', $module) }}" class="inline-block bg-primary text-white px-4 py-2 rounded text-sm mb-4 hover:bg-primary-dark transition">
            <i class="fas fa-plus mr-1"></i> Tambah Materi
        </a>
        @if ($module->materials->isEmpty())
            <p class="text-gray-400 text-center py-8">Belum ada materi.</p>
        @else
            <div class="space-y-2">
                @foreach ($module->materials as $materi)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded hover:bg-gray-100">
                        <div>
                            <span class="text-gray-400 mr-2">#{{ $materi->urutan }}</span>
                            <span class="font-medium">{{ $materi->judul }}</span>
                            <span class="ml-2 text-xs px-2 py-0.5 rounded {{ $materi->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                {{ $materi->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('materi.edit', $materi) }}" class="text-yellow-600 hover:text-yellow-800 mr-2"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('materi.destroy', $materi) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Tab Kuis --}}
    <div id="content-kuis" class="p-6 hidden">
        <a href="{{ route('kuis.create', $module) }}" class="inline-block bg-primary text-white px-4 py-2 rounded text-sm mb-4 hover:bg-primary-dark transition">
            <i class="fas fa-plus mr-1"></i> Tambah Kuis
        </a>
        <a href="{{ route('kuis.generate.form', $module) }}" class="inline-block bg-purple-600 text-white px-4 py-2 rounded text-sm mb-4 ml-2 hover:bg-purple-700 transition">
            <i class="fas fa-robot mr-1"></i> Generate Soal AI
        </a>
        @if ($module->quizzes->isEmpty())
            <p class="text-gray-400 text-center py-8">Belum ada kuis.</p>
        @else
            <div class="space-y-2">
                @foreach ($module->quizzes as $kuis)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded hover:bg-gray-100">
                        <div>
                            <span class="font-medium">{{ $kuis->judul }}</span>
                            <span class="ml-2 text-xs text-gray-500">{{ $kuis->durasi_menit }} menit</span>
                        </div>
                        <div>
                            <a href="{{ route('soal.create', $kuis) }}" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-list-ol"></i> Soal</a>
                            <a href="{{ route('kuis.edit', $kuis) }}" class="text-yellow-600 hover:text-yellow-800 mr-2"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('kuis.destroy', $kuis) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    function switchTab(tab) {
        // Sembunyikan semua konten
        document.getElementById('content-materi').classList.add('hidden');
        document.getElementById('content-kuis').classList.add('hidden');
        // Hapus active dari semua tab
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('border-primary', 'text-primary');
            b.classList.add('text-gray-500');
        });
        // Tampilkan konten & tab aktif
        document.getElementById('content-' + tab).classList.remove('hidden');
        let btn = document.getElementById('tab-' + tab);
        btn.classList.add('border-primary', 'text-primary');
        btn.classList.remove('text-gray-500');
    }
</script>

@endsection