@extends('layouts.admin')

@section('title', 'Manajemen Siswa')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Siswa</h4>
        <p class="text-muted mb-0">{{ count($siswa) }} siswa</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
        <i class="mdi mdi-plus me-1"></i>Tambah Siswa
    </button>
</div>

{{-- Filter & Search --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <form method="GET" action="{{ route('siswa.index') }}">
                    <select name="kelas" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k['nama_kelas'] }}" {{ $filterKelas === $k['nama_kelas'] ? 'selected' : '' }}>
                            {{ $k['nama_kelas'] }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="mdi mdi-magnify text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0"
                           placeholder="Cari nama atau NIS...">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tableSiswa">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>NIS</th>
                        <th>Kelas</th>
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
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px;height:34px;background:#E8EAFF;color:#4B49AC;font-weight:700;font-size:13px">
                                    {{ strtoupper(substr($s['nama'], 0, 1)) }}
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:14px">{{ $s['nama'] }}</p>
                                    <small class="text-muted">{{ $s['sekolah'] ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td><code>{{ $s['nis'] }}</code></td>
                        <td><span class="badge bg-light text-dark">{{ $s['kelas'] ?? '-' }}</span></td>
                        <td><span class="badge bg-warning bg-opacity-10 text-warning"><i class="mdi mdi-fire me-1"></i>{{ $s['streak_hari'] ?? 0 }}</span></td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $s['total_chat'] ?? 0 }}</span></td>
                        <td><small class="text-muted">{{ \Carbon\Carbon::parse($s['created_at'])->format('d M Y') }}</small></td>
                        <td>
                            <a href="{{ route('siswa.show', $s['id']) }}" class="btn btn-sm btn-inverse-primary">
                                <i class="mdi mdi-eye me-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="mdi mdi-account-off-outline d-block mb-2" style="font-size:40px;opacity:.3"></i>
                            Belum ada siswa
                        </td>
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
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select name="kelas" class="form-select" required>
                                <option value="">Pilih kelas</option>
                                @foreach($kelas as $k)
                                <option value="{{ $k['nama_kelas'] }}">{{ $k['nama_kelas'] }}</option>
                                @endforeach
                            </select>
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

@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', function() {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('#tableSiswa tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
    });
});
</script>
@endpush
