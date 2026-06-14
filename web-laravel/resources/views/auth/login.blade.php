<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login — Netlabs Admin</title>

    <link rel="stylesheet" href="{{ asset('assets/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/star-admin2.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">

    <style>
        .auth .brand-logo img { width: 100%; }
        .auth .login-half-bg { background: linear-gradient(135deg, #4B49AC 0%, #7978E9 100%); }
    </style>
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">

                            {{-- Brand --}}
                            <div class="brand-logo d-flex align-items-center gap-2 mb-3">
                                <div class="rounded d-flex align-items-center justify-content-center"
                                     style="width:48px;height:48px;background:#4B49AC">
                                    <i class="mdi mdi-lan text-white" style="font-size:24px"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">Netlabs</h4>
                                    <small class="text-muted">Panel Guru</small>
                                </div>
                            </div>

                            <h4 class="fw-bold">Selamat Datang</h4>
                            <h6 class="fw-light text-muted mb-4">Login untuk melanjutkan ke panel admin</h6>

                            {{-- Errors --}}
                            @if($errors->has('login'))
                                <div class="alert alert-danger d-flex align-items-center gap-2">
                                    <i class="mdi mdi-alert-circle"></i>
                                    {{ $errors->first('login') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger d-flex align-items-center gap-2">
                                    <i class="mdi mdi-alert-circle"></i>
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form action="{{ route('login.post') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label class="form-label">NIS / ID Guru</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">
                                            <i class="mdi mdi-account-outline text-primary"></i>
                                        </span>
                                        <input type="text" name="nis"
                                               class="form-control form-control-lg @error('nis') is-invalid @enderror"
                                               placeholder="Contoh: GURU001"
                                               value="{{ old('nis') }}" autofocus>
                                    </div>
                                    @error('nis')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group mt-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">
                                            <i class="mdi mdi-lock-outline text-primary"></i>
                                        </span>
                                        <input type="password" name="password"
                                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                                               placeholder="Masukkan password">
                                    </div>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn w-100">
                                        <i class="mdi mdi-login me-2"></i>Masuk
                                    </button>
                                </div>
                            </form>

                            <div class="mt-4 p-3 rounded" style="background:#f0efff">
                                <small class="text-muted">
                                    <strong class="text-primary">Akun demo:</strong>
                                    NIS <code>GURU001</code> · Password <code>guru123</code>
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/vendor.bundle.base.js') }}"></script>
</body>
</html>