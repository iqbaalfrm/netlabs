<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Netlabs Admin</title>

    {{-- Star Admin 2 CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/star-admin2.css') }}">

    {{-- MDI Icons via CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">

    @stack('styles')
</head>
<body>
    <div class="container-scroller">

        {{-- NAVBAR --}}
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <div class="me-3">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                        <span class="icon-menu mdi mdi-menu"></span>
                    </button>
                </div>
                <div>
                    <a class="navbar-brand brand-logo" href="{{ route('dashboard') }}">
                        <span class="fw-bold fs-5"><i class="mdi mdi-lan me-1"></i>Netlabs</span>
                    </a>
                    <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
                        <i class="mdi mdi-lan"></i>
                    </a>
                </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-top">
                <ul class="navbar-nav">
                    <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
                        <h1 class="welcome-text">Panel Guru</h1>
                        <h3 class="welcome-sub-text">SMK — Jaringan Komputer Dasar</h3>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="fw-semibold me-2">{{ session('guru.nama', 'Guru') }}</span>
                            <span class="badge bg-primary rounded-circle p-2">
                                {{ strtoupper(substr(session('guru.nama', 'G'), 0, 1)) }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <div class="dropdown-header text-center">
                                <p class="mb-1 mt-3 font-weight-semibold">{{ session('guru.nama') }}</p>
                                <p class="fw-light text-muted mb-0">NIS: {{ session('guru.nis') }}</p>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>

        {{-- PAGE BODY --}}
        <div class="container-fluid page-body-wrapper">

            {{-- SIDEBAR --}}
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item nav-category">Menu Utama</li>

                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="mdi mdi-view-dashboard menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('kelas.*') || request()->routeIs('siswa.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('kelas.index') }}">
                            <i class="mdi mdi-google-classroom menu-icon"></i>
                            <span class="menu-title">Manajemen Kelas</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('pertemuan.*') || request()->routeIs('topik.*') || request()->routeIs('modul.*') || request()->routeIs('kuis.*') || request()->routeIs('soal.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('pertemuan.index') }}">
                            <i class="mdi mdi-book-open-page-variant menu-icon"></i>
                            <span class="menu-title">Pertemuan & Topik</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('nilai.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('nilai.index') }}">
                            <i class="mdi mdi-chart-bar menu-icon"></i>
                            <span class="menu-title">Nilai & Progress</span>
                        </a>
                    </li>

                    <li class="nav-item nav-category">Pengaturan</li>

                    <li class="nav-item {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('pengaturan.index') }}">
                            <i class="mdi mdi-cog menu-icon"></i>
                            <span class="menu-title">Pengaturan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-start w-100">
                                <i class="mdi mdi-logout menu-icon text-danger"></i>
                                <span class="menu-title">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            {{-- MAIN PANEL --}}
            <div class="main-panel">
                <div class="content-wrapper">
                    {{-- Flash messages handled by SweetAlert2 --}}

                    @yield('content')
                </div>

                {{-- FOOTER --}}
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                            Netlabs — ITS + LMS Praktikum Jaringan Komputer
                        </span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
                            Skripsi 2026
                        </span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ asset('assets/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/sweetalert-helper.js') }}"></script>

    {{-- SweetAlert2 Flash Messages --}}
    @if(session('success'))
    <script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    </script>
    @endif

    @if(session('error'))
    <script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: @json(session('error')),
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true
    });
    </script>
    @endif

    @if(session('warning'))
    <script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'warning',
        title: @json(session('warning')),
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true
    });
    </script>
    @endif

    @if(session('info'))
    <script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'info',
        title: @json(session('info')),
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    </script>
    @endif

    @stack('scripts')
</body>
</html>
