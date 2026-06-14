@extends('layouts.admin')

@section('title', 'Manajemen Kelas')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Kelas</h4>
        <p class="text-muted mb-0">{{ count($kelas) }} kelas terdaftar</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
        <i class="mdi mdi-plus me-1"></i>Tambah Kelas
    </button>
</div>

<div class="row">
    @forelse($kelas as $k)
    <div class="col-md-6 col-xl-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded d-flex align-items-center justify-content-center"
                             style="width:44px;height:44px;background:#E8EAFF">
                            <i class="mdi mdi-google-classroom text-primary" style="font-size:22px"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $k['nama_kelas'] }}</h5>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 pt-3 border-top">
                    <a href="{{ route('kelas.show', $k['id']) }}" class="btn btn-sm btn-primary flex-grow-1">
                        <i class="mdi mdi-eye me-1"></i>Detail
                    </a>
                    <button class="btn btn-sm btn-inverse-warning"
                        onclick="editKelas('{{ $k['id'] }}', '{{ addslashes($k['nama_kelas']) }}')">
                        <i class="mdi mdi-pencil"></i>
                    </button>
                    <form action="{{ route('kelas.destroy', $k['id']) }}" method="POST"
                          onsubmit="event.preventDefault(); confirmDelete(this, '{{ addslashes($k['nama_kelas']) }}')">
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
                <i class="mdi mdi-google-classroom d-block mb-2" style="font-size:48px;opacity:.3"></i>
                <p class="text-muted">Belum ada kelas. Klik "Tambah Kelas" untuk mulai.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahKelas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('kelas.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-plus-circle me-2 text-primary"></i>Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: XI TKJ 1" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-check me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEditKelas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditKelas" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-pencil me-2 text-primary"></i>Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kelas" id="edit_nama_kelas" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-check me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editKelas(id, nama) {
    document.getElementById('formEditKelas').action = '/guru/kelas/' + id;
    document.getElementById('edit_nama_kelas').value = nama;
    new bootstrap.Modal(document.getElementById('modalEditKelas')).show();
}
</script>
@endpush
