@extends('layouts.admin')

@section('title', $pertemuan['judul'])

@section('content')

{{-- Breadcrumb --}}
<div class="d-flex align-items-center gap-2 mb-4 text-muted" style="font-size:14px">
    <a href="{{ route('pertemuan.index') }}" class="text-muted text-decoration-none">Pertemuan & Topik</a>
    <i class="mdi mdi-chevron-right" style="font-size:14px"></i>
    <span class="text-dark fw-semibold">P{{ $pertemuan['nomor_urut'] }} — {{ $pertemuan['judul'] }}</span>
</div>

{{-- Card header --}}
<div class="card mb-4" style="border-left: 5px solid {{ $pertemuan['warna_hex'] ?? '#4B49AC' }}">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <span class="badge mb-2" style="background:{{ $pertemuan['warna_hex'] ?? '#4B49AC' }}20;
                       color:{{ $pertemuan['warna_hex'] ?? '#4B49AC' }}">
                    Pertemuan {{ $pertemuan['nomor_urut'] }}
                </span>
                <h4 class="fw-bold mb-1">{{ $pertemuan['judul'] }}</h4>
                <p class="text-muted mb-0">{{ $pertemuan['deskripsi'] ?? 'Tidak ada deskripsi' }}</p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <span class="badge bg-light text-muted">
                    <i class="mdi mdi-book-open-variant me-1"></i>{{ count($topik) }} topik
                </span>
                <span class="badge bg-light text-muted">
                    <i class="mdi mdi-help-circle me-1"></i>{{ count($soal) }} soal
                </span>
                <span class="badge bg-light text-muted">
                    <i class="mdi mdi-file-pdf-box me-1"></i>{{ count($modul) }} PDF
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="pertemuanTab">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-topik">
            <i class="mdi mdi-book-open-variant me-1"></i>Topik ({{ count($topik) }})
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-pdf">
            <i class="mdi mdi-file-pdf-box me-1"></i>Modul PDF ({{ count($modul) }})
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-kuis">
            <i class="mdi mdi-clipboard-check me-1"></i>Soal Kuis ({{ count($soal) }})
        </a>
    </li>
</ul>

<div class="tab-content">

    {{-- TAB TOPIK --}}
    <div class="tab-pane fade show active" id="tab-topik">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTopik">
                <i class="mdi mdi-plus me-1"></i>Tambah Topik
            </button>
        </div>

        @forelse($topik as $t)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <a href="{{ route('topik.show', [$pertemuan['id'], $t['id']]) }}" class="flex-grow-1 text-decoration-none">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $t['nomor_urut'] }}</span>
                            <h6 class="fw-bold mb-0 text-dark">{{ $t['judul'] }}</h6>
                        </div>
                        <p class="text-muted mb-0" style="font-size:14px; line-height:1.6">
                            {{ Str::limit($t['isi_materi'], 200) }}
                        </p>
                        <div class="mt-2 d-flex gap-2">
                            <small class="text-muted"><i class="mdi mdi-file-pdf-box me-1"></i>Modul PDF</small>
                            <small class="text-muted"><i class="mdi mdi-clipboard-check me-1"></i>Kuis & Soal</small>
                        </div>
                    </a>
                    <div class="d-flex gap-2 flex-shrink-0">
                        <button class="btn btn-sm btn-inverse-warning"
                            onclick="editTopik('{{ $t['id'] }}', {{ $t['nomor_urut'] }}, '{{ addslashes($t['judul']) }}', {{ json_encode($t['isi_materi']) }})">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <form action="{{ route('topik.destroy', [$pertemuan['id'], $t['id']]) }}" method="POST"
                              onsubmit="event.preventDefault(); confirmDelete(this, 'topik ini')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-inverse-danger">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="mdi mdi-book-off-outline d-block mb-2" style="font-size:48px;opacity:.3"></i>
                <p class="text-muted">Belum ada topik. Tambah topik pertama untuk pertemuan ini.</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- TAB PDF --}}
    <div class="tab-pane fade" id="tab-pdf">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="mdi mdi-upload me-1 text-primary"></i>Upload Modul PDF</h5>
                <form action="{{ route('modul.upload', $pertemuan['id']) }}"
                      method="POST" enctype="multipart/form-data" id="formUploadPdf">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Pilih File PDF <span class="text-muted">— maks. 20MB</span></label>
                            <div id="dropZone" onclick="document.getElementById('inputPdf').click()"
                                 style="border:2px dashed #dee2e6; border-radius:8px; padding:24px;
                                        text-align:center; cursor:pointer; transition:all .2s; background:#f8f9fa">
                                <i class="mdi mdi-file-pdf-box" id="dropIcon"
                                   style="font-size:32px; color:#6c757d; display:block; margin-bottom:8px"></i>
                                <div id="dropLabel" style="font-size:13px; color:#6c757d">
                                    Klik atau drag PDF ke sini
                                </div>
                                <div id="fileName" class="mt-2"
                                     style="font-size:12px; color:#4B49AC; display:none; font-weight:600"></div>
                            </div>
                            <input type="file" name="file" id="inputPdf" accept=".pdf" style="display:none">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100" id="btnUpload">
                                <i class="mdi mdi-cloud-upload me-1"></i>Upload & Index
                            </button>
                            <small class="form-text d-block mt-2">
                                <i class="mdi mdi-brain me-1"></i>PDF akan diindex ke AI RAG otomatis
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(count($modul) > 0)
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="card-title mb-0"><i class="mdi mdi-file-multiple me-1"></i>Modul Tersimpan</h5>
                    <span class="badge bg-primary">{{ count($modul) }} file</span>
                </div>
                @foreach($modul as $m)
                <div class="d-flex align-items-center gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:44px;height:44px;background:#FFF1F2">
                        <i class="mdi mdi-file-pdf-box text-danger" style="font-size:22px"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <p class="fw-semibold mb-0 text-truncate" style="font-size:14px">{{ $m['nama_file'] }}</p>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($m['created_at'])->format('d M Y · H:i') }} ·
                            @if($m['sudah_diindex'])
                                <span class="text-success"><i class="mdi mdi-check-circle me-1"></i>Terindex RAG</span>
                            @else
                                <span class="text-warning"><i class="mdi mdi-clock me-1"></i>Belum diindex</span>
                            @endif
                        </small>
                    </div>
                    <div class="d-flex gap-2 flex-shrink-0">
                        @if(!empty($m['url_file']))
                        <a href="{{ $m['url_file'] }}" target="_blank" class="btn btn-sm btn-inverse-info">
                            <i class="mdi mdi-download"></i>
                        </a>
                        @endif
                        <form action="{{ route('modul.destroy', [$pertemuan['id'], $m['id']]) }}" method="POST"
                              onsubmit="event.preventDefault(); confirmDelete(this, '{{ addslashes($m['nama_file']) }}')"
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-inverse-danger">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="mdi mdi-file-pdf-box d-block mb-2" style="font-size:48px;opacity:.3"></i>
                <p class="text-muted">Belum ada modul PDF. Upload file di atas untuk mengaktifkan AI Tutor.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- TAB SOAL KUIS --}}
    <div class="tab-pane fade" id="tab-kuis">
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
                                    <strong>{{ strtoupper($opt) }}.</strong>
                                    {{ $s['pilihan_' . $opt] }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($s['penjelasan'])
                        <div class="mt-2 p-2 rounded bg-light" style="font-size:13px">
                            <i class="mdi mdi-lightbulb me-1 text-warning"></i>{{ $s['penjelasan'] }}
                        </div>
                        @endif
                    </div>
                    <form action="{{ route('soal.destroy', [$pertemuan['id'], $s['id']]) }}" method="POST"
                          onsubmit="event.preventDefault(); confirmDelete(this, 'soal ini')" class="flex-shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-inverse-danger">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="mdi mdi-help-circle-outline d-block mb-2" style="font-size:48px;opacity:.3"></i>
                <p class="text-muted">Belum ada soal kuis. Tambah minimal 5 soal.</p>
            </div>
        </div>
        @endforelse
    </div>

</div>

{{-- Modal Tambah Topik --}}
<div class="modal fade" id="modalTambahTopik" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('topik.store', $pertemuan['id']) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-plus-circle me-2 text-primary"></i>Tambah Topik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-3">
                            <label class="form-label">Nomor Urut <span class="text-danger">*</span></label>
                            <input type="number" name="nomor_urut" class="form-control" min="1" value="{{ count($topik) + 1 }}" required>
                        </div>
                        <div class="col-9">
                            <label class="form-label">Judul Topik <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Pengertian Jaringan Komputer" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Isi Materi <span class="text-danger">*</span></label>
                            <textarea name="isi_materi" class="form-control" rows="6" placeholder="Tulis isi materi topik di sini..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-check me-1"></i>Simpan Topik</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Topik --}}
<div class="modal fade" id="modalEditTopik" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditTopik" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-pencil me-2 text-primary"></i>Edit Topik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-3">
                            <label class="form-label">Nomor Urut</label>
                            <input type="number" name="nomor_urut" id="edit_topik_nomor" class="form-control" required>
                        </div>
                        <div class="col-9">
                            <label class="form-label">Judul Topik</label>
                            <input type="text" name="judul" id="edit_topik_judul" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Isi Materi</label>
                            <textarea name="isi_materi" id="edit_topik_isi" class="form-control" rows="6" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-check me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Soal --}}
<div class="modal fade" id="modalTambahSoal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('soal.store', $pertemuan['id']) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-plus-circle me-2 text-primary"></i>Tambah Soal Kuis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                            <textarea name="pertanyaan" class="form-control" rows="2" placeholder="Tuliskan pertanyaan di sini..." required></textarea>
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
                            <input type="text" name="penjelasan" class="form-control" placeholder="Penjelasan kenapa jawaban itu benar">
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

function editTopik(id, nomor, judul, isi) {
    const pertemuanId = '{{ $pertemuan['id'] }}';
    document.getElementById('formEditTopik').action = '/guru/pertemuan/' + pertemuanId + '/topik/' + id;
    document.getElementById('edit_topik_nomor').value = nomor;
    document.getElementById('edit_topik_judul').value = judul;
    document.getElementById('edit_topik_isi').value = isi;
    new bootstrap.Modal(document.getElementById('modalEditTopik')).show();
}

const inputPdf = document.getElementById('inputPdf');
const dropZone = document.getElementById('dropZone');
const fileName = document.getElementById('fileName');
const dropLabel = document.getElementById('dropLabel');
const dropIcon = document.getElementById('dropIcon');
const btnUpload = document.getElementById('btnUpload');
const formUpload = document.getElementById('formUploadPdf');

if (inputPdf) {
    inputPdf.addEventListener('change', () => {
        if (inputPdf.files.length > 0) {
            fileName.textContent = inputPdf.files[0].name;
            fileName.style.display = 'block';
            dropLabel.textContent = 'File dipilih:';
            dropIcon.style.color = '#4B49AC';
            dropZone.style.borderColor = '#4B49AC';
            dropZone.style.background = '#f0efff';
        }
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#4B49AC';
        dropZone.style.background = '#f0efff';
    });

    dropZone.addEventListener('dragleave', () => {
        if (!inputPdf.files.length) {
            dropZone.style.borderColor = '#dee2e6';
            dropZone.style.background = '#f8f9fa';
        }
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file && file.type === 'application/pdf') {
            const dt = new DataTransfer();
            dt.items.add(file);
            inputPdf.files = dt.files;
            inputPdf.dispatchEvent(new Event('change'));
        } else {
            alert('Hanya file PDF yang diizinkan');
        }
    });

    formUpload.addEventListener('submit', (e) => {
        if (!inputPdf.files.length) {
            e.preventDefault();
            alert('Pilih file PDF terlebih dahulu');
            return;
        }
        btnUpload.disabled = true;
        btnUpload.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...';
    });
}
</script>
@endpush