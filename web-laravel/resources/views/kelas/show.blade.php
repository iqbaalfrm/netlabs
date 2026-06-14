@extends('layouts.admin')

@section('title', $kelas['nama_kelas'])

@section('content')

{{-- Breadcrumb --}}
<div class="d-flex align-items-center gap-2 mb-4 text-muted" style="font-size:14px">
    <a href="{{ route('kelas.index') }}" class="text-muted text-decoration-none">Manajemen Kelas</a>
    <i class="mdi mdi-chevron-right" style="font-size:14px"></i>
    <span class="text-dark fw-semibold">{{ $kelas['nama_kelas'] }}</span>
</div>

{{-- Header --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded d-flex align-items-center justify-content-center"
                     style="width:52px;height:52px;background:#E8EAFF">
                    <i class="mdi mdi-google-classroom text-primary" style="font-size:26px"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">{{ $kelas['nama_kelas'] }}</h4>
                    <p class="text-muted mb-0">{{ count($siswa) }} siswa terdaftar</p>
                </div>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                <i class="mdi mdi-plus me-1"></i>Tambah Siswa
            </button>
        </div>
    </div>
</div>

{{-- Tabel Siswa --}}
<div class="card">
    <div class="card-body">
        <h4 class="card-title"><i class="mdi mdi-account-group me-1 text-primary"></i>Daftar Siswa</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIS</th>
                        <th>Streak</th>
                        <th>Chat AI</th>
                        <th>Bergabung</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $s)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:32px;height:32px;background:#E8EAFF;color:#4B49AC;font-weight:700;font-size:12px">
                                    {{ strtoupper(substr($s['nama'], 0, 1)) }}
                                </div>
                                <span class="fw-semibold">{{ $s['nama'] }}</span>
                            </div>
                        </td>
                        <td><code>{{ $s['nis'] }}</code></td>
                        <td><span class="badge bg-warning bg-opacity-10 text-warning">{{ $s['streak_hari'] ?? 0 }} hari</span></td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $s['total_chat'] ?? 0 }}</span></td>
                        <td><small class="text-muted">{{ \Carbon\Carbon::parse($s['created_at'])->format('d M Y') }}</small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('siswa.show', $s['id']) }}" class="btn btn-sm btn-inverse-primary" title="Detail">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-inverse-warning" title="Edit"
                                    onclick="editSiswa('{{ $s['id'] }}', '{{ addslashes($s['nama']) }}', '{{ $s['kelas'] ?? '' }}', '{{ $s['sekolah'] ?? '' }}')">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-inverse-info" title="Reset Password"
                                    onclick="confirmReset(document.getElementById('resetForm{{ $s['id'] }}'), '{{ addslashes($s['nama']) }}')">
                                    <i class="mdi mdi-lock-reset"></i>
                                </button>
                                <form action="{{ route('siswa.destroy', $s['id']) }}" method="POST"
                                      onsubmit="event.preventDefault(); confirmDelete(this, '{{ addslashes($s['nama']) }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-inverse-danger" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </form>
                                <form id="resetForm{{ $s['id'] }}" action="{{ route('siswa.update', $s['id']) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="nama" value="{{ $s['nama'] }}">
                                    <input type="hidden" name="kelas" value="{{ $s['kelas'] ?? '' }}">
                                    <input type="hidden" name="password_baru" value="{{ $s['nis'] }}">
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada siswa di kelas ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah Siswa --}}
<div class="modal fade" id="modalTambahSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('siswa.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-plus-circle me-2 text-primary"></i>Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="{{ $kelas['nama_kelas'] }}" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Sekolah</label>
                            <input type="text" name="sekolah" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-check me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Siswa --}}
<div class="modal fade" id="modalEditSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditSiswa" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-pencil me-2 text-primary"></i>Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_siswa_nama" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="kelas" id="edit_siswa_kelas" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Sekolah</label>
                            <input type="text" name="sekolah" id="edit_siswa_sekolah" class="form-control">
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

{{-- Modal Reset Password --}}
<div class="modal fade" id="modalResetPassword" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formResetPassword" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-lock-reset me-2 text-warning"></i>Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Reset password untuk: <strong id="reset_nama"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password_baru" class="form-control" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="mdi mdi-lock-reset me-1"></i>Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editSiswa(id, nama, kelas, sekolah) {
    document.getElementById('formEditSiswa').action = '/guru/siswa/' + id;
    document.getElementById('edit_siswa_nama').value = nama;
    document.getElementById('edit_siswa_kelas').value = kelas;
    document.getElementById('edit_siswa_sekolah').value = sekolah;
    new bootstrap.Modal(document.getElementById('modalEditSiswa')).show();
}

function resetPassword(id, nama) {
    document.getElementById('formResetPassword').action = '/guru/siswa/' + id;
    document.getElementById('reset_nama').textContent = nama;
    new bootstrap.Modal(document.getElementById('modalResetPassword')).show();
}
</script>
@endpush
