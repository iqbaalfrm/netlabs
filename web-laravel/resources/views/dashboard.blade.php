@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- Statistik --}}
<div class="row">
    <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="card-title text-muted">Total Siswa</p>
                        <h3 class="fw-bold">{{ $stats['total_siswa'] }}</h3>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 rounded">
                        <i class="mdi mdi-account-group text-primary" style="font-size:24px;padding:10px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="card-title text-muted">Pertemuan Aktif</p>
                        <h3 class="fw-bold text-success">{{ $stats['total_pertemuan'] }}</h3>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 rounded">
                        <i class="mdi mdi-book-open-page-variant text-success" style="font-size:24px;padding:10px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="card-title text-muted">Kuis Dikerjakan</p>
                        <h3 class="fw-bold" style="color:#7B5EA7">{{ $stats['total_kuis'] }}</h3>
                    </div>
                    <div class="rounded" style="background:#F3EEFF">
                        <i class="mdi mdi-clipboard-check" style="font-size:24px;padding:10px;color:#7B5EA7"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="card-title text-muted">Rata-rata Nilai</p>
                        <h3 class="fw-bold text-warning">{{ $stats['rata_rata_nilai'] }}</h3>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10 rounded">
                        <i class="mdi mdi-star text-warning" style="font-size:24px;padding:10px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Daftar Pertemuan --}}
    <div class="col-lg-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-book-open-page-variant me-1 text-primary"></i>Pertemuan
                    </h4>
                    <a href="{{ route('pertemuan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul Pertemuan</th>
                                <th>Topik</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pertemuan as $p)
                            <tr>
                                <td>
                                    <span class="badge rounded-pill" style="background:{{ $p['warna_hex'] ?? '#4B49AC' }}; color:#fff">
                                        {{ $p['nomor_urut'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $p['judul'] }}</span>
                                    <br><small class="text-muted">{{ Str::limit($p['deskripsi'] ?? '', 50) }}</small>
                                </td>
                                <td><span class="text-muted">—</span></td>
                                <td>
                                    <a href="{{ route('pertemuan.show', $p['id']) }}" class="btn btn-sm btn-inverse-primary">
                                        <i class="mdi mdi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-book-off-outline d-block mb-2" style="font-size:32px;opacity:.4"></i>
                                    Belum ada pertemuan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar kanan --}}
    <div class="col-lg-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-account-group me-1 text-primary"></i>Siswa Terdaftar
                    </h4>
                    <a href="{{ route('siswa.index') }}" class="btn btn-sm btn-outline-primary">Semua</a>
                </div>

                @forelse($siswa5Terbaru as $s)
                <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:38px;height:38px;background:#E8EAFF;color:#4B49AC;font-weight:700;font-size:14px">
                        {{ strtoupper(substr($s['nama'], 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <p class="mb-0 fw-semibold" style="font-size:14px">{{ $s['nama'] }}</p>
                        <small class="text-muted">{{ $s['nis'] }} · {{ $s['kelas'] ?? '-' }}</small>
                    </div>
                    <a href="{{ route('siswa.show', $s['id']) }}" class="btn btn-sm btn-inverse-info">
                        <i class="mdi mdi-eye"></i>
                    </a>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="mdi mdi-account-off-outline d-block mb-2" style="font-size:32px;opacity:.4"></i>
                    Belum ada siswa
                </div>
                @endforelse

                {{-- Kuis Terbaru --}}
                <hr class="my-3">
                <h4 class="card-title">
                    <i class="mdi mdi-clipboard-check me-1" style="color:#7B5EA7"></i>Kuis Terbaru
                </h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Siswa ID</th>
                                <th>Nilai</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekap5Terbaru as $r)
                            <tr>
                                <td><small class="text-muted">{{ Str::limit($r['siswa_id'], 8) }}…</small></td>
                                <td>
                                    <span class="badge {{ $r['nilai'] >= 70 ? 'bg-success' : 'bg-warning' }}">
                                        {{ $r['nilai'] }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ \Carbon\Carbon::parse($r['waktu_kuis'])->diffForHumans() }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">Belum ada kuis</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection