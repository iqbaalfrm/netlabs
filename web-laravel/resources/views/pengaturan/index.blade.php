@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-1">Pengaturan</h4>
    <p class="text-muted mb-0">Kelola profil dan preferensi akun guru</p>
</div>

<div class="row">
    <div class="col-lg-8">
        {{-- Edit Profil --}}
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title"><i class="mdi mdi-account-edit me-1 text-primary"></i>Edit Profil</h4>
                <form action="{{ route('pengaturan.profil') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="{{ $guru['nama'] ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIS / ID</label>
                            <input type="text" class="form-control" value="{{ $guru['nis'] ?? '' }}" disabled>
                            <small class="text-muted">Tidak dapat diubah</small>
                        </div>
                        <div class="col-12"><hr></div>
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control" minlength="6"
                                   placeholder="Kosongkan jika tidak ingin mengubah">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_baru_confirmation" class="form-control"
                                   placeholder="Ulangi password baru">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Info Akun --}}
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width:64px;height:64px;background:#4B49AC;font-size:24px;font-weight:700;color:white">
                    {{ strtoupper(substr($guru['nama'] ?? 'G', 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $guru['nama'] ?? '-' }}</h5>
                <p class="text-muted mb-0">{{ $guru['nis'] ?? '-' }}</p>
                <span class="badge bg-primary mt-2">Guru</span>
            </div>
        </div>

        {{-- Tahun Ajaran --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><i class="mdi mdi-calendar me-1 text-primary"></i>Tahun Ajaran</h4>
                <div class="p-3 rounded" style="background:#f8f9fa">
                    <h5 class="fw-bold mb-0 text-center">2025/2026</h5>
                    <p class="text-muted mb-0 text-center" style="font-size:13px">Semester Genap</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
