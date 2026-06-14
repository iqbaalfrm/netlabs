@extends('layouts.admin')

@section('title', 'Soal Kuis — ' . $pertemuan['judul'])

@section('content')

<div class="d-flex align-items-center gap-2 mb-4 text-muted" style="font-size:14px">
    <a href="{{ route('kuis.index') }}" class="text-muted text-decoration-none">Kuis & Soal</a>
    <i class="mdi mdi-chevron-right" style="font-size:14px"></i>
    <span class="text-dark fw-semibold">P{{ $pertemuan['nomor_urut'] }} — {{ $pertemuan['judul'] }}</span>
</div>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Soal Kuis: {{ $pertemuan['judul'] }}</h4>
        <p class="text-muted mb-0">{{ count($soal) }} soal terdaftar</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
        <i class="mdi mdi-plus me-1"></i>Tambah Soal
    </button>
</div>

@forelse($soal as $index => $s)
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Soal {{ $index + 1 }}</span>
                    <span class="badge bg-success bg-opacity-10 text-success">Jawaban: {{ $s['jawaban_benar'] }}</span>
                </div>
                <p class="fw-semibold mb-3" style="font-size:14px">{{ $s['pertanyaan'] }}</p>
                <div class="row g-2">
                    @foreach(['a','b','c','d'] as $opt)
                    <div class="col-md-6">
                        <div class="p-2 rounded" style="font-size:13px;
                            background:{{ strtolower($s['jawaban_benar']) === $opt ? '#d4edda' : '#f8f9fa' }};
                            border:1px solid {{ strtolower($s['jawaban_benar']) === $opt ? '#28a745' : '#dee2e6' }};
                            color:{{ strtolower($s['jawaban_benar']) === $opt ? '#155724' : '#212529' }}">
                            <strong>{{ strtoupper($opt) }}.</strong> {{ $s['pilihan_' . $opt] }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($s['penjelasan'] ?? null)
                <div class="mt-2 p-2 rounded bg-light" style="font-size:13px">
                    <i class="mdi mdi-lightbulb me-1 text-warning"></i>{{ $s['penjelasan'] }}
                </div>
                @endif
            </div>
            <form action="{{ route('soal.destroy', [$pertemuan['id'], $s['id']]) }}" method="POST"
                  onsubmit="event.preventDefault(); confirmDelete(this, 'soal ini')" class="flex-shrink-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-inverse-danger"><i class="mdi mdi-delete"></i></button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center py-5">
        <i class="mdi mdi-help-circle-outline d-block mb-2" style="font-size:48px;opacity:.3"></i>
        <p class="text-muted">Belum ada soal. Tambah minimal 5 soal untuk kuis ini.</p>
    </div>
</div>
@endforelse

{{-- Modal Tambah Soal --}}
<div class="modal fade" id="modalTambahSoal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('soal.store', $pertemuan['id']) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-plus-circle me-2 text-primary"></i>Tambah Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                            <textarea name="pertanyaan" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan A <span class="text-danger">*</span></label>
                            <input type="text" name="pilihan_a" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan B <span class="text-danger">*</span></label>
                            <input type="text" name="pilihan_b" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan C <span class="text-danger">*</span></label>
                            <input type="text" name="pilihan_c" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilihan D <span class="text-danger">*</span></label>
                            <input type="text" name="pilihan_d" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jawaban Benar <span class="text-danger">*</span></label>
                            <select name="jawaban_benar" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Penjelasan (opsional)</label>
                            <input type="text" name="penjelasan" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-check me-1"></i>Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
