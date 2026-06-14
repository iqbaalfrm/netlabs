@extends('layouts.admin')

@section('title', $siswa['nama'])

@section('content')

{{-- Breadcrumb --}}
<div class="d-flex align-items-center gap-2 mb-4 text-muted" style="font-size:14px">
    <a href="{{ route('siswa.index') }}" class="text-muted text-decoration-none">Siswa</a>
    <i class="mdi mdi-chevron-right" style="font-size:14px"></i>
    <span class="text-dark fw-semibold">{{ $siswa['nama'] }}</span>
</div>

<div class="row">

    {{-- Profil --}}
    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center border-bottom pb-4">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width:72px;height:72px;background:linear-gradient(135deg,#4B49AC,#7978E9);
                            font-size:28px;font-weight:700;color:white">
                    {{ strtoupper(substr($siswa['nama'], 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $siswa['nama'] }}</h5>
                <p class="text-muted mb-3">{{ $siswa['kelas'] ?? '-' }}</p>
                <div class="d-flex justify-content-center gap-4">
                    <div class="text-center">
                        <h5 class="fw-bold text-warning mb-0">{{ $siswa['streak_hari'] ?? 0 }}</h5>
                        <small class="text-muted">Streak</small>
                    </div>
                    <div style="width:1px;background:#dee2e6"></div>
                    <div class="text-center">
                        <h5 class="fw-bold text-primary mb-0">{{ $siswa['total_chat'] ?? 0 }}</h5>
                        <small class="text-muted">Chat</small>
                    </div>
                    <div style="width:1px;background:#dee2e6"></div>
                    <div class="text-center">
                        <h5 class="fw-bold text-success mb-0">{{ $rataRata }}</h5>
                        <small class="text-muted">Rata-rata</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold">NIS</small>
                    <p class="mb-0 mt-1"><code>{{ $siswa['nis'] }}</code></p>
                </div>
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold">Sekolah</small>
                    <p class="mb-0 mt-1">{{ $siswa['sekolah'] ?? 'Tidak diisi' }}</p>
                </div>
                <div>
                    <small class="text-muted text-uppercase fw-semibold">Bergabung</small>
                    <p class="mb-0 mt-1">{{ \Carbon\Carbon::parse($siswa['created_at'])->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Kuis --}}
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-clipboard-check me-1" style="color:#7B5EA7"></i>
                    Riwayat Kuis ({{ count($hasilKuis) }})
                </h4>

                @if(count($hasilKuis) > 0)
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <small class="text-muted">Rata-rata nilai</small>
                        <strong class="text-success">{{ $rataRata }}/100</strong>
                    </div>
                    <div class="progress" style="height:8px;border-radius:10px">
                        <div class="progress-bar" style="width:{{ $rataRata }}%;
                             background:{{ $rataRata >= 70 ? '#28a745' : '#ffc107' }};
                             border-radius:10px"></div>
                    </div>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pertemuan</th>
                                <th>Benar</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hasilKuis as $h)
                            <tr>
                                <td>
                                    @if(isset($pertemuanMap[$h['pertemuan_id']]))
                                    <span class="fw-semibold" style="font-size:13px">
                                        {{ $pertemuanMap[$h['pertemuan_id']]['judul'] }}
                                    </span>
                                    @else
                                    <span class="text-muted" style="font-size:13px">Pertemuan dihapus</span>
                                    @endif
                                </td>
                                <td style="font-size:13px">{{ $h['jumlah_benar'] }}/{{ $h['total_soal'] }}</td>
                                <td>
                                    <span class="fw-bold" style="font-size:16px;
                                        color:{{ $h['nilai'] >= 70 ? '#28a745' : '#dc3545' }}">
                                        {{ $h['nilai'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($h['nilai'] >= 70)
                                    <span class="badge bg-success">Lulus</span>
                                    @else
                                    <span class="badge bg-danger">Belum lulus</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ \Carbon\Carbon::parse($h['waktu_kuis'])->format('d M Y H:i') }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="mdi mdi-clipboard-off-outline d-block mb-2" style="font-size:48px;opacity:.3"></i>
                                    <p class="text-muted">Siswa belum mengerjakan kuis apapun</p>
                                </td>
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