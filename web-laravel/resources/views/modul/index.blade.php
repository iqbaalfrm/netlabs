@extends('layouts.admin')

@section('title', 'Modul PDF')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Modul PDF</h4>
        <p class="text-muted mb-0">{{ count($modul) }} modul tersimpan</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUploadModul">
        <i class="mdi mdi-plus me-1"></i>Upload Modul
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Pertemuan</th>
                        <th>Status RAG</th>
                        <th>Tanggal Upload</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modul as $m)
                    @php
                        $prt = collect($pertemuan)->firstWhere('id', $m['pertemuan_id']);
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:36px;height:36px;background:#FFF1F2">
                                    <i class="mdi mdi-file-pdf-box text-danger" style="font-size:18px"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:14px">{{ $m['judul'] ?? $m['nama_file'] }}</p>
                                    <small class="text-muted">{{ $m['nama_file'] }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($prt)
                            <span class="badge bg-primary bg-opacity-10 text-primary">P{{ $prt['nomor_urut'] }} — {{ Str::limit($prt['judul'], 25) }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
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
                                      onsubmit="event.preventDefault(); confirmAction(this, 'Proses RAG?', 'Modul ini akan diindex untuk AI Tutor. Embedding lama akan diganti.', 'Ya, Proses!')">
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
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="mdi mdi-file-pdf-box d-block mb-2" style="font-size:40px;opacity:.3"></i>
                            Belum ada modul PDF
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Upload --}}
<div class="modal fade" id="modalUploadModul" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('modul.store') }}" method="POST" enctype="multipart/form-data"
                  onsubmit="submitWithLoading(this, 'Mengupload modul...')">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-upload me-2 text-primary"></i>Upload Modul PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Judul Modul <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Modul Subnetting Dasar" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Pertemuan <span class="text-danger">*</span></label>
                            <select name="pertemuan_id" class="form-select" required>
                                <option value="">Pilih pertemuan</option>
                                @foreach($pertemuan as $p)
                                <option value="{{ $p['id'] }}">P{{ $p['nomor_urut'] }} — {{ $p['judul'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">File PDF <span class="text-danger">*</span> <small class="text-muted">maks. 20MB</small></label>
                            <input type="file" name="file" class="form-control" accept=".pdf" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-cloud-upload me-1"></i>Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
