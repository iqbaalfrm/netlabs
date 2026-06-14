@extends('layouts.admin')

@section('title', $topik['judul'])

@section('content')

{{-- Breadcrumb --}}
<div class="d-flex align-items-center gap-2 mb-4 text-muted" style="font-size:14px">
    <a href="{{ route('pertemuan.index') }}" class="text-muted text-decoration-none">Pertemuan & Topik</a>
    <i class="mdi mdi-chevron-right" style="font-size:14px"></i>
    <a href="{{ route('pertemuan.show', $pertemuan['id']) }}" class="text-muted text-decoration-none">P{{ $pertemuan['nomor_urut'] }} — {{ $pertemuan['judul'] }}</a>
    <i class="mdi mdi-chevron-right" style="font-size:14px"></i>
    <span class="text-dark fw-semibold">{{ $topik['judul'] }}</span>
</div>

{{-- Header --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded d-flex align-items-center justify-content-center"
                 style="width:48px;height:48px;background:#E8EAFF">
                <span class="fw-bold text-primary">{{ $topik['nomor_urut'] }}</span>
            </div>
            <div>
                <h4 class="fw-bold mb-0">{{ $topik['judul'] }}</h4>
                <p class="text-muted mb-0">Pertemuan {{ $pertemuan['nomor_urut'] }} — {{ $pertemuan['judul'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-modul">
            <i class="mdi mdi-file-pdf-box me-1"></i>Modul PDF ({{ count($modul) }})
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-soal">
            <i class="mdi mdi-clipboard-check me-1"></i>Kuis & Soal ({{ count($soal) }})
        </a>
    </li>
</ul>

<div class="tab-content">

    {{-- TAB MODUL PDF --}}
    <div class="tab-pane fade show active" id="tab-modul">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="mdi mdi-upload me-1 text-primary"></i>Upload Modul PDF</h5>
                <form action="{{ route('modul.store') }}" method="POST" enctype="multipart/form-data"
                      onsubmit="submitWithLoading(this, 'Mengupload modul...')">
                    @csrf
                    <input type="hidden" name="pertemuan_id" value="{{ $pertemuan['id'] }}">
                    <input type="hidden" name="topik_id" value="{{ $topik['id'] }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Judul Modul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Modul OSI Layer" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">File PDF <small class="text-muted">maks. 20MB</small></label>
                            <input type="file" name="file" class="form-control" accept=".pdf" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="mdi mdi-cloud-upload me-1"></i>Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(count($modul) > 0)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Modul Tersimpan</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama File</th>
                                <th>Status RAG</th>
                                <th>Tanggal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modul as $m)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="mdi mdi-file-pdf-box text-danger" style="font-size:20px"></i>
                                        <span class="fw-semibold">{{ $m['judul'] ?? $m['nama_file'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($m['sudah_diindex'] ?? false)
                                    <span class="badge bg-success"><i class="mdi mdi-check-circle me-1"></i>Siap</span>
                                    @else
                                    <span class="badge bg-warning text-dark"><i class="mdi mdi-clock me-1"></i>Belum diproses</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ \Carbon\Carbon::parse($m['created_at'])->format('d M Y H:i') }}</small></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if(!($m['sudah_diindex'] ?? false))
                                        <form action="{{ route('modul.triggerRag', $m['id']) }}" method="POST"
                                              onsubmit="event.preventDefault(); confirmAction(this, 'Proses RAG?', 'Modul ini akan diindex untuk AI Tutor.', 'Ya, Proses!')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-inverse-success" title="Proses RAG">
                                                <i class="mdi mdi-brain"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('modul.destroy', $m['id']) }}" method="POST"
                                              onsubmit="event.preventDefault(); confirmDelete(this, 'modul ini')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-inverse-danger">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-4 text-muted">
                <i class="mdi mdi-file-pdf-box d-block mb-2" style="font-size:40px;opacity:.3"></i>
                Belum ada modul PDF untuk topik ini.
            </div>
        </div>
        @endif
    </div>

    {{-- TAB SOAL KUIS --}}
    <div class="tab-pane fade" id="tab-soal">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
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
                    <form action="{{ route('soal.destroyById', $s['id']) }}" method="POST"
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
            <div class="card-body text-center py-4 text-muted">
                <i class="mdi mdi-help-circle-outline d-block mb-2" style="font-size:40px;opacity:.3"></i>
                Belum ada soal untuk topik ini.
            </div>
        </div>
        @endforelse
    </div>

</div>

{{-- Modal Tambah Soal --}}
<div class="modal fade" id="modalTambahSoal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('soal.storeByTopik', $topik['id']) }}" method="POST">
                @csrf
                <input type="hidden" name="pertemuan_id" value="{{ $pertemuan['id'] }}">
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

@push('scripts')
<script>
const hash = window.location.hash;
if (hash) {
    const tabLink = document.querySelector(`[href="${hash}"]`);
    if (tabLink) new bootstrap.Tab(tabLink).show();
}
</script>
@endpush
