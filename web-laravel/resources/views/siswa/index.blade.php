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

        {{-- Pagination Controls --}}
        <div class="d-flex align-items-center justify-content-between mt-4 flex-wrap gap-2">
            <div class="text-muted" style="font-size: 14px" id="paginationInfo">
                Menampilkan 0 dari 0 siswa
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0" id="paginationControls">
                    {{-- Di-generate otomatis oleh JavaScript --}}
                </ul>
            </nav>
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
document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 10;
    let currentPage = 1;
    
    // Ambil baris tabel asli (selain baris kosong "Belum ada siswa")
    const rows = Array.from(document.querySelectorAll('#tableSiswa tbody tr')).filter(row => {
        return !(row.cells.length === 1 && row.cells[0].colSpan === 7);
    });
    
    let filteredRows = [...rows];

    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const searchInput = document.getElementById('searchInput');

    function updatePagination() {
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;
        
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Sembunyikan semua baris
        document.querySelectorAll('#tableSiswa tbody tr').forEach(row => row.style.display = 'none');

        // Tampilkan baris kosong jika tidak ada data sama sekali
        if (totalRows === 0) {
            const emptyRow = document.querySelector('#tableSiswa tbody tr');
            if (emptyRow && emptyRow.cells.length === 1) {
                emptyRow.style.display = '';
            }
            paginationInfo.textContent = 'Menampilkan 0 dari 0 siswa';
            paginationControls.innerHTML = '';
            return;
        }

        // Tampilkan baris untuk halaman aktif
        const start = (currentPage - 1) * rowsPerPage;
        const end = Math.min(start + rowsPerPage, totalRows);
        
        for (let i = start; i < end; i++) {
            if (filteredRows[i]) {
                filteredRows[i].style.display = '';
            }
        }

        // Update teks info pagination
        paginationInfo.textContent = `Menampilkan ${start + 1}-${end} dari ${totalRows} siswa`;

        // Render tombol navigasi
        paginationControls.innerHTML = '';

        // Tombol Sebelumnya
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;"><i class="mdi mdi-chevron-left"></i></a>`;
        paginationControls.appendChild(prevLi);

        // Tombol Halaman Angka
        for (let i = 1; i <= totalPages; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${currentPage === i ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>`;
            paginationControls.appendChild(pageLi);
        }

        // Tombol Selanjutnya
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;"><i class="mdi mdi-chevron-right"></i></a>`;
        paginationControls.appendChild(nextLi);
    }

    window.changePage = function(page) {
        currentPage = page;
        updatePagination();
    };

    // Filter Pencarian yang terintegrasi dengan Pagination
    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        
        if (keyword.trim() === '') {
            filteredRows = [...rows];
        } else {
            filteredRows = rows.filter(row => {
                return row.textContent.toLowerCase().includes(keyword);
            });
        }
        
        currentPage = 1;
        updatePagination();
    });

    // Jalankan inisialisasi pertama kali
    updatePagination();
});
</script>
@endpush
