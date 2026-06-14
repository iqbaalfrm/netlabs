@extends('layouts.admin')

@section('title', 'Pertemuan')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Daftar Pertemuan</h4>
        <p class="text-muted mb-0">{{ count($pertemuan) }} pertemuan terdaftar</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPertemuan">
        <i class="mdi mdi-plus me-1"></i>Tambah Pertemuan
    </button>
</div>

{{-- Grid kartu pertemuan --}}
<div class="row">
    @forelse($pertemuan as $p)
    <div class="col-md-6 col-xl-4 grid-margin stretch-card">
        <div class="card" style="border-top: 4px solid {{ $p['warna_hex'] ?? '#4B49AC' }}">
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge mb-2" style="background:{{ $p['warna_hex'] ?? '#4B49AC' }}20;
                           color:{{ $p['warna_hex'] ?? '#4B49AC' }}">
                        Pertemuan {{ $p['nomor_urut'] }}
                    </span>
                    <h6 class="fw-bold mb-1">{{ $p['judul'] }}</h6>
                    <p class="text-muted mb-0" style="font-size:13px">
                        {{ Str::limit($p['deskripsi'] ?? 'Tidak ada deskripsi', 80) }}
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2 pt-3 border-top">
                    <a href="{{ route('pertemuan.show', $p['id']) }}"
                       class="btn btn-sm btn-primary flex-grow-1">
                        <i class="mdi mdi-eye me-1"></i>Kelola
                    </a>
                    <button class="btn btn-sm btn-inverse-warning"
                        onclick="editPertemuan('{{ $p['id'] }}', {{ $p['nomor_urut'] }}, '{{ addslashes($p['judul']) }}', '{{ addslashes($p['deskripsi'] ?? '') }}', '{{ $p['warna_hex'] ?? '#4B49AC' }}')">
                        <i class="mdi mdi-pencil"></i>
                    </button>
                    <form action="{{ route('pertemuan.destroy', $p['id']) }}" method="POST"
                          onsubmit="event.preventDefault(); confirmDelete(this, '{{ addslashes($p['judul']) }}')">
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
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="mdi mdi-book-off-outline d-block mb-2" style="font-size:48px;opacity:.3"></i>
                <p class="text-muted">Belum ada pertemuan. Klik "Tambah Pertemuan" untuk mulai.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahPertemuan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pertemuan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="mdi mdi-plus-circle me-2 text-primary"></i>Tambah Pertemuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="form-label">Nomor Urut <span class="text-danger">*</span></label>
                            <input type="number" name="nomor_urut" class="form-control"
                                   min="1" value="{{ count($pertemuan) + 1 }}" required>
                        </div>
                        <div class="col-8">
                            <label class="form-label">Warna</label>
                            <input type="color" name="warna_hex" class="form-control form-control-color w-100"
                                   value="#4B49AC">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Judul Pertemuan <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control"
                                   placeholder="Contoh: Pengenalan Jaringan Komputer" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"
                                      placeholder="Deskripsi singkat tentang pertemuan ini..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEditPertemuan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditPertemuan" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="mdi mdi-pencil me-2 text-primary"></i>Edit Pertemuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="form-label">Nomor Urut <span class="text-danger">*</span></label>
                            <input type="number" name="nomor_urut" id="edit_nomor_urut" class="form-control"
                                   min="1" required>
                        </div>
                        <div class="col-8">
                            <label class="form-label">Warna</label>
                            <input type="color" name="warna_hex" id="edit_warna_hex"
                                   class="form-control form-control-color w-100">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Judul Pertemuan <span class="text-danger">*</span></label>
                            <input type="text" name="judul" id="edit_judul" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editPertemuan(id, nomor, judul, deskripsi, warna) {
    document.getElementById('formEditPertemuan').action = '/guru/pertemuan/' + id;
    document.getElementById('edit_nomor_urut').value = nomor;
    document.getElementById('edit_judul').value = judul;
    document.getElementById('edit_deskripsi').value = deskripsi;
    document.getElementById('edit_warna_hex').value = warna;
    new bootstrap.Modal(document.getElementById('modalEditPertemuan')).show();
}
</script>
@endpush