<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netlabs | @yield('title', 'Admin Panel')</title>
    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2D6A4F',
                        'primary-dark': '#1B4332',
                    }
                }
            }
        }
    </script>
    {{-- Font Awesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-primary min-h-screen flex-shrink-0 flex flex-col">
        <div class="p-6 border-b border-primary-dark">
            <a href="{{ route('dashboard') }}" class="block">
                <h1 class="text-white text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-network-wired"></i> Netlabs
                </h1>
                <p class="text-green-200 text-xs mt-1">Praktikum Jaringan Komputer</p>
            </a>
        </div>

        <nav class="py-4 flex-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-6 py-3 text-white hover:bg-primary-dark transition
                      {{ request()->routeIs('dashboard') ? 'bg-primary-dark font-semibold' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i> Dashboard
            </a>
            <a href="{{ route('pertemuan.index') }}"
               class="flex items-center gap-3 px-6 py-3 text-white hover:bg-primary-dark transition
                      {{ request()->routeIs('pertemuan.*') || request()->routeIs('topik.*') || request()->routeIs('kuis.*') ? 'bg-primary-dark font-semibold' : '' }}">
                <i class="fas fa-book w-5"></i> Pertemuan
            </a>
            <a href="{{ route('siswa.index') }}"
               class="flex items-center gap-3 px-6 py-3 text-white hover:bg-primary-dark transition
                      {{ request()->routeIs('siswa.*') ? 'bg-primary-dark font-semibold' : '' }}">
                <i class="fas fa-users w-5"></i> Siswa
            </a>
        </nav>

        <div class="p-4 border-t border-primary-dark">
            <div class="text-green-200 text-sm mb-2">
                <i class="fas fa-user-circle mr-2"></i>
                {{ session('guru')['name'] ?? 'Admin' }}
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-green-300 hover:text-white text-sm flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Konten Utama --}}
    <main class="flex-1 overflow-y-auto">
        {{-- Alert --}}
        @include('components.alert')

        {{-- Page Content --}}
        <div class="p-8">
            @yield('content')
        </div>
    </main>

    {{-- Konfirmasi Hapus --}}
    <script>
        function confirmDelete(form) {
            if (confirm('Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) {
                form.submit();
            }
        }
    </script>
</body>
</html>