<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sistem Parkir Digital</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0c1929; color: #e2e8f0; }
        .sidebar { background-color: #0f1e37; border-right: 1px solid rgba(56, 189, 248, 0.1); }
        .sidebar-link { color: #94a3b8; transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { color: #38bdf8; background-color: rgba(14, 165, 233, 0.1); border-left: 3px solid #0ea5e9; }
        .topbar { background-color: rgba(15, 30, 55, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(56, 189, 248, 0.1); }
        .card { background-color: #122240; border: 1px solid rgba(56, 189, 248, 0.1); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); }
        
        /* Fix Chrome Autofill Background Color to dark UI */
        input:-webkit-autofill, input:-webkit-autofill:hover, input:-webkit-autofill:focus, input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #1e293b inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">
    <div class="flex h-full overflow-hidden">
        
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="sidebar fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 md:relative md:translate-x-0 flex flex-col shadow-2xl md:shadow-none">
            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-6 border-b border-slate-800/50">
            <img src="{{ asset('images/logo_aplikasi_parkir.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl shadow-lg shadow-sky-500/10">
            <span class="text-lg font-bold text-white tracking-tight leading-tight">
                {{ $sys_settings['nama_aplikasi'] ?? 'Sistem Parkir' }}
            </span>
        </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold tracking-widest text-slate-500 uppercase overflow-hidden text-ellipsis mb-2">Utama</p>
                    <a href="{{ url('/dashboard') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg {{ request()->is('dashboard') ? 'active font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                </div>

                @if(auth()->check() && auth()->user()->role == 'admin')
                <div class="mb-6 text-slate-200">
                    <p class="px-4 text-[10px] font-bold tracking-widest text-slate-500 uppercase mb-2">Manajemen (Pengelola)</p>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.users.*') ? 'active font-semibold' : 'hover:text-white transition' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Pengguna & Petugas
                    </a>
                    <a href="{{ route('admin.areas.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.areas.*') ? 'active font-semibold' : 'hover:text-white transition' }} mt-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Area Parkir
                    </a>
                    <a href="{{ route('admin.kendaraan.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.kendaraan.*') ? 'active font-semibold' : 'hover:text-white transition' }} mt-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2.69-2.69A2 2 0 017.1 13H17a2 2 0 012 2v1"></path></svg>
                        Data Kendaraan
                    </a>
                </div>
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold tracking-widest text-slate-500 uppercase mb-2">Sistem & Log</p>
                    <a href="{{ route('admin.tarifs.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.tarifs.*') ? 'active font-semibold' : 'hover:text-white transition' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Tarif Parkir
                    </a>
                    <a href="{{ route('admin.logs.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.logs.*') ? 'active font-semibold' : 'hover:text-white transition' }} mt-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Log Aktivitas
                    </a>
                    <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.pengaturan.*') ? 'active font-semibold' : 'hover:text-white transition' }} mt-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Pengaturan Sistem
                    </a>
                </div>
                @endif

                @if(auth()->check() && auth()->user()->role == 'owner')
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold tracking-widest text-slate-500 uppercase mb-2">Pelaporan (Owner)</p>
                    <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.index') ? 'active font-semibold' : 'hover:text-white transition' }} flex items-center px-4 py-2.5 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Rekap Transaksi
                    </a>
                </div>
                @endif

                @if(auth()->check() && auth()->user()->role == 'petugas')
                <div class="mb-6">
                    <p class="px-4 text-[10px] font-bold tracking-widest text-slate-500 uppercase mb-2">Operasional Staff</p>
                    <a href="{{ route('transaksi.masuk') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('transaksi.masuk') ? 'active font-semibold' : 'hover:text-white transition' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Kendaraan Masuk
                    </a>
                    <a href="{{ route('transaksi.keluar') }}" class="sidebar-link flex items-center px-4 py-2.5 rounded-lg {{ request()->routeIs('transaksi.keluar') || request()->routeIs('transaksi.prosesKeluar') ? 'active font-semibold' : 'hover:text-white transition' }} mt-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Kendaraan Keluar
                    </a>
                </div>
                @endif

                {{-- [MENU OWNER DIHAPUS SESUAI TABEL FITUR PDF] --}}
            </nav>
        </aside>

        <!-- Main wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="topbar sticky top-0 z-40 w-full h-16 flex items-center justify-between px-4 sm:px-6">
                <button @click="sidebarOpen = true" class="md:hidden text-slate-400 hover:text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <div class="flex-1 flex justify-end">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('profile') }}" class="flex items-center gap-4 hover:bg-slate-800/50 px-3 py-1.5 rounded-2xl transition group border border-transparent hover:border-slate-700/50">
                            <div class="text-right hidden sm:block">
                                <span class="block text-sm font-bold text-white group-hover:text-sky-400 transition">{{ auth()->check() ? auth()->user()->nama_lengkap : 'Visitor' }}</span>
                                <span class="block text-xs text-sky-400 capitalize">{{ auth()->check() ? auth()->user()->role : 'guest' }}</span>
                            </div>
                            
                            @php
                                $role = auth()->check() ? auth()->user()->role : 'guest';
                                $avatarTheme = match($role) {
                                    'admin' => 'bg-sky-500/20 text-sky-400 border-sky-400/30',
                                    'petugas' => 'bg-emerald-500/20 text-emerald-400 border-emerald-400/30',
                                    'owner' => 'bg-purple-500/20 text-purple-400 border-purple-400/30',
                                    default => 'bg-slate-800 text-slate-400 border-slate-600'
                                };
                                $initial = strtoupper(substr(auth()->check() ? auth()->user()->nama_lengkap : 'V', 0, 1));
                            @endphp
                            
                            <div class="h-9 w-9 rounded-full flex items-center justify-center border {{ $avatarTheme }} text-lg font-bold shadow-inner group-hover:shadow-sky-500/20 transition">
                                {{ $initial }}
                            </div>
                        </a>
                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="ml-2">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-red-400 p-2 rounded-lg hover:bg-slate-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>
        
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-40 md:hidden" style="display: none;"></div>
    </div>
    @stack('scripts')
</body>
</html>
