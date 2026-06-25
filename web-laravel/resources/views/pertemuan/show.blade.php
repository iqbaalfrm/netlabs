@extends('layouts.app')
@section('title', $pertemuan->judul)

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-book-open text-primary mr-2"></i>{{ $pertemuan->judul }}
    </h2>
    <div class="flex gap-2">
        <a href="{{ route('pertemuan.edit', $pertemuan->id) }}"
           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded transition text-sm">
            <i class="fas fa-edit mr-1"></i>Edit
        </a>
        <a href="{{ route('pertemuan.index') }}"
           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded transition text-sm">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
    </div>
</div>

{{-- Info Pertemuan --}}
<div class="bg-white rounded-lg shadow p-5 mb-6">
    <div class="grid grid-cols-4 gap-4 text-sm">
        <div>
            <span class="text-gray-500">Urutan:</span>
            <span class="font-semibold">{{ $pertemuan->nomor_urut }}</span>
        </div>
        <div>
            <span class="text-gray-500">Status:</span>
            <span class="font-semibold">{{ $pertemuan->status ?? '-' }}</span>
        </div>
    </div>
    @if ($pertemuan->deskripsi)
    <p class="text-gray-600 mt-3">{{ $pertemuan->deskripsi }}</p>
    @endif
</div>

{{-- 3 Tab: Topik | Soal | Upload PDF --}}
<div x-data="{ tab: 'topik' }">
    {{-- Tab Headers --}}
    <div class="flex border-b border-gray-200 mb-4">
        <button @click="tab = 'topik'" :class="tab === 'topik' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-gray-500'"
                class="px-4 py-2 text-sm transition">
            <i class="fas fa-list-ul mr-1"></i>Topik ({{ $pertemuan->topik->count() }})
        </button>
        <button @click="tab = 'soal'" :class="tab === 'soal' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-gray-500'"
                class="px-4 py-2 text-sm transition">
            <i class="fas fa-question-circle mr-1"></i>Soal Kuis ({{ $pertemuan->soalKuis->count() }})
        </button>
        <button @click="tab = 'pdf'" :class="tab === 'pdf' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-gray-500'"
                class="px-4 py-2 text-sm transition">
            <i class="fas fa-file-pdf mr-1"></i>Modul PDF ({{ $pertemuan->modulPdf->count() }})
        </button>
    </div>

    {{-- Tab: Topik --}}
    <div x-show="tab === 'topik'" x-cloak>
        <div class="mb-3">
            <a href="{{ route('topik.create', $pertemuan->id) }}"
               class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded transition text-sm">
                <i class="fas fa-plus mr-1"></i>Tambah Topik
            </a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">#</th>
                        <th class="text-left px-4 py-3">Judul</th>
                        <th class="text-left px-4 py-3">Estimasi</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pertemuan->topik as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $t->nomor_urut }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $t->judul }}</td>
                        <td class="px-4 py-3 text-gray-500">-</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('topik.edit', $t->id) }}" class="text-yellow-500 hover:text-yellow-600 mr-2"
                               title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('topik.destroy', $t->id) }}" method="POST" class="inline"
                                  onsubmit="event.preventDefault(); confirmDelete(this);">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-600" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada topik</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tab: Soal --}}
    <div x-show="tab === 'soal'" x-cloak>
        <div class="mb-3">
            <a href="{{ route('kuis.create', $pertemuan->id) }}"
               class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded transition text-sm">
                <i class="fas fa-plus mr-1"></i>Tambah Soal
            </a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">#</th>
                        <th class="text-left px-4 py-3">Pertanyaan</th>
                        <th class="text-center px-4 py-3">Kunci</th>
                        <th class="text-center px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pertemuan->soalKuis as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">{{ $s->id }}</td>
                        <td class="px-4 py-3 text-gray-800">{{ \Illuminate\Support\Str::limit($s->pertanyaan, 80) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">{{ strtoupper($s->kunci) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('kuis.edit', $s->id) }}" class="text-yellow-500 hover:text-yellow-600 mr-2"
                               title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('kuis.destroy', $s->id) }}" method="POST" class="inline"
                                  onsubmit="event.preventDefault(); confirmDelete(this);">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-600" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada soal</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tab: Upload PDF --}}
    <div x-show="tab === 'pdf'" x-cloak>
        <div class="bg-white rounded-lg shadow p-5 mb-4 max-w-xl">
            <form action="{{ route('modul.upload', $pertemuan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Judul Modul</label>
                    <input type="text" name="judul" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="mb-3">
                    <label class="block text-gray-700 text-sm font-bold mb-2">File PDF (max 10MB)</label>
                    <input type="file" name="file" required accept=".pdf"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none">
                </div>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded transition">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Judul</th>
                        <th class="text-left px-4 py-3">File</th>
                        <th class="text-left px-4 py-3">Upload</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pertemuan->modulPdf as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $m->judul }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ asset('storage/'.$m->file_path) }}" target="_blank"
                               class="text-primary hover:text-primary-dark">
                                <i class="fas fa-download mr-1"></i>Buka PDF
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada modul PDF</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection