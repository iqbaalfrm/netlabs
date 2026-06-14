@extends('layouts.admin')

@section('title', 'Kuis & Soal')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kuis & Soal</h4>
        <p class="text-muted mb-0">Kelola soal kuis per pertemuan</p>
    </div>
</div>

<div class="row">
    @forelse($kuisData as $item)
    @php $p = $item['pertemuan']; @endphp
    <div class="col-md-6 col-xl-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:{{ $p['warna_hex'] ?? '#4B49AC' }}20">
                        <span class="fw-bold" style="color:{{ $p['warna_hex'] ?? '#4B49AC' }}">P{{ $p['nomor_urut'] }}</span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">{{ $p['judul'] }}</h6>
                        <small class="text-muted">{{ $item['jumlah_soal'] }} soal</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="flex-grow-1">
                        <div class="progress" style="height:6px">
                            <div class="progress-bar" style="width:{{ min($item['jumlah_soal'] * 20, 100) }}%;background:{{ $p['warna_hex'] ?? '#4B49AC' }}"></div>
                        </div>
                        <small class="text-muted">{{ $item['jumlah_soal'] }}/5 soal minimum</small>
                    </div>
                    <a href="{{ route('kuis.show', $p['id']) }}" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-pencil me-1"></i>Kelola
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="mdi mdi-clipboard-check d-block mb-2" style="font-size:48px;opacity:.3"></i>
                <p class="text-muted">Belum ada pertemuan. Buat pertemuan terlebih dahulu di menu Pertemuan & Topik.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

@endsection
