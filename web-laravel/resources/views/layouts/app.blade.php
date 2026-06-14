<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Netlabs Admin</title>

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ===== VARIABEL WARNA NETLABS ===== */
        :root {
            --primary: #2D7DD2;
            --navy: #1A2B5F;
            --purple: #7B5EA7;
            --teal: #0F9B8E;
            --orange: #F4A261;
            --sidebar-width: 260px;
        }

        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: #F5F7FA;
            color: #1A1A2E;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #1A2B5F 0%, #0f1a3e 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s;
        }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand .brand-text {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .sidebar-brand .brand-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            margin-top: 2px;
        }

        .sidebar-nav {
            padding: 16px 0;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.3);
            text-transform: uppercase;
            padding: 8px 20px 4px;
            margin-top: 8px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: rgba(255,255,255,0.65);
            font-size: 14px;
            font-weight: 500;
            border-radius: 0;
            transition: all 0.2s;
            margin: 1px 8px;
            border-radius: 8px;
            text-decoration: none;
        }

        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }

        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(45, 125, 210, 0.4);
            border-left: 3px solid var(--primary);
        }

        .sidebar-nav .nav-link i {
            font-size: 18px;
            width: 22px;
            text-align: center;
        }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #E8ECF0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 700;
            color: #1A1A2E;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .guru-badge {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .guru-avatar {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .guru-name {
            font-size: 14px;
            font-weight: 600;
            color: #1A1A2E;
        }

        .guru-role {
            font-size: 12px;
            color: #6B7A99;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content {
            padding: 24px;
            flex: 1;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #E8ECF0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
            margin: 8px 0 4px;
        }

        .stat-label {
            font-size: 13px;
            color: #6B7A99;
        }

        /* ===== CARD UMUM ===== */
        .card-netlabs {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #E8ECF0;
            overflow: hidden;
        }

        .card-netlabs .card-header {
            background: #fff;
            border-bottom: 1px solid #F0F2F5;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 15px;
        }

        /* ===== BADGE WARNA PERTEMUAN ===== */
        .pertemuan-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        /* ===== TABLE ===== */
        .table-netlabs {
            margin: 0;
        }

        .table-netlabs thead th {
            background: #F8FAFC;
            border-bottom: 1px solid #E8ECF0;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6B7A99;
            padding: 12px 16px;
        }

        .table-netlabs tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            font-size: 14px;
            border-bottom: 1px solid #F0F2F5;
        }

        .table-netlabs tbody tr:last-child td {
            border-bottom: none;
        }

        .table-netlabs tbody tr:hover td {
            background-color: #FAFBFD;
        }

        /* ===== ALERT ===== */
        .alert { border-radius: 10px; border: none; }

        /* ===== FORM ===== */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid #E0E6EF;
            font-size: 14px;
            padding: 10px 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(45,125,210,0.1);
        }

        .btn { border-radius: 8px; font-size: 14px; font-weight: 500; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: #2468b8; border-color: #2468b8; }

        /* ===== MODAL ===== */
        .modal-content { border-radius: 16px; border: none; }
        .modal-header { border-bottom: 1px solid #F0F2F5; }
        .modal-footer { border-top: 1px solid #F0F2F5; }

        /* ===== TABS ===== */
        .nav-tabs .nav-link {
            font-size: 14px;
            font-weight: 500;
            color: #6B7A99;
            border: none;
            padding: 10px 16px;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background: none;
        }
        .nav-tabs { border-bottom: 1px solid #E8ECF0; }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: #6B7A99;
        }
        .empty-state i { font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block; }
        .empty-state p { font-size: 14px; margin: 0; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    <div class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-text">
                <i class="bi bi-cpu-fill me-2" style="color: var(--primary)"></i>Netlabs
            </div>
            <div class="brand-sub">Panel Guru — SMK Jaringan Komputer</div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                Dashboard
            </a>

            <a href="{{ route('pertemuan.index') }}"
               class="nav-link {{ request()->routeIs('pertemuan.*') ? 'active' : '' }}">
                <i class="bi bi-journal-richtext"></i>
                Pertemuan
            </a>

            <a href="{{ route('siswa.index') }}"
               class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                Data Siswa
            </a>

            <div class="nav-label">Akun</div>

            <div class="nav-link" style="cursor:default">
                <i class="bi bi-person-circle"></i>
                <div>
                    <div style="font-size:13px; color:#fff">{{ session('guru.nama') }}</div>
                    <div style="font-size:11px; color:rgba(255,255,255,0.4)">NIS: {{ session('guru.nis') }}</div>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="mx-2 mt-2">
                @csrf
                <button type="submit" class="nav-link w-100 border-0 text-start"
                    style="background:none; color:rgba(255,255,255,0.5)">
                    <i class="bi bi-box-arrow-right" style="color: #E05263"></i>
                    Logout
                </button>
            </form>
        </nav>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="main-wrapper">

        {{-- TOPBAR --}}
        <div class="topbar">
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-right">
                <div class="guru-badge">
                    <div class="guru-avatar">
                        {{ strtoupper(substr(session('guru.nama', 'G'), 0, 1)) }}
                    </div>
                    <div>
                        <div class="guru-name">{{ session('guru.nama') }}</div>
                        <div class="guru-role">Guru</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PAGE CONTENT --}}
        <div class="page-content">

            {{-- Alert dari session --}}
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-info-circle-fill"></i>
                    {{ session('info') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
