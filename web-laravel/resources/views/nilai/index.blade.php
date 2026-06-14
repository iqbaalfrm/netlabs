@extends('layouts.admin')

@section('title', 'Nilai & Progress')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Nilai & Progress</h4>
        <p class="text-muted mb-0">Rekap nilai kuis per siswa</p>
    </div>
    <a href="{{ route('nilai.export', ['kelas' => $filterKelas, 'pertemuan_id' => $filterPertemuan]) }}" class="btn btn-outline-success">
        <i class="mdi mdi-file-export me-1"></i>Export CSV
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('nilai.index') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <select name="kelas" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k['nama_kelas'] }}" {{ $filterKelas === $k['nama_kelas'] ? 'selected' : '' }}>
                        {{ $k['nama_kelas'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="pertemuan_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Pertemuan</option>
                    @foreach($pertemuan as $p)
                    <option value="{{ $p['id'] }}" {{ $filterPertemuan === $p['id'] ? 'selected' : '' }}>
                        P{{ $p['nomor_urut'] }} — {{ $p['judul'] }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Nilai --}}
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th style="min-width:180px">Nama Siswa</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        @foreach($displayPertemuan as $p)
                        <th class="text-center" style="min-width:70px">P{{ $p['nomor_urut'] }}</th>
                        @endforeach
                        <th class="text-center" style="min-width:80px">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $s)
                    @php
                        $nilaiSiswa = [];
                        foreach ($displayPertemuan as $p) {
                            $key = $s['id'] . '_' . $p['id'];
                            $val = $nilaiMap[$key] ?? null;
                            if ($val !== null) $nilaiSiswa[] = $val;
                        }
                        $rata = count($nilaiSiswa) > 0 ? round(array_sum($nilaiSiswa) / count($nilaiSiswa), 1) : null;
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $s['nama'] }}</td>
                        <td><code>{{ $s['nis'] }}</code></td>
                        <td><span class="badge bg-light text-dark">{{ $s['kelas'] ?? '-' }}</span></td>
                        @foreach($displayPertemuan as $p)
                        @php
                            $key = $s['id'] . '_' . $p['id'];
                            $nilai = $nilaiMap[$key] ?? null;
                        @endphp
                        <td class="text-center">
                            @if($nilai !== null)
                            <span class="badge {{ $nilai >= 75 ? 'bg-success' : 'bg-danger' }}">{{ $nilai }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="text-center">
                            @if($rata !== null)
                            <strong class="{{ $rata >= 75 ? 'text-success' : 'text-danger' }}">{{ $rata }}</strong>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 3 + count($displayPertemuan) + 1 }}" class="text-center py-5 text-muted">
                            <i class="mdi mdi-chart-bar d-block mb-2" style="font-size:40px;opacity:.3"></i>
                            Belum ada data nilai
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex gap-3">
            <small><span class="badge bg-success">≥ 75</span> Di atas KKM</small>
            <small><span class="badge bg-danger">&lt; 75</span> Di bawah KKM</small>
        </div>
    </div>
</div>

@endsection
