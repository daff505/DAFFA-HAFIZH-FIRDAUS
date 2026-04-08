<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sistem Parkir Digital</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(145deg, #0c1929 0%, #122240 40%, #0f2847 70%, #0a1628 100%); }
        .auth-card { background: rgba(15, 30, 55, 0.65); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(56, 189, 248, 0.1); }
        .auth-input { background-color: rgba(8, 20, 40, 0.7); border: 1px solid rgba(56, 189, 248, 0.15); color: #e0f2fe; }
        .auth-input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15); outline: none; }
        .auth-input::placeholder { color: #4b7399; }
        
        /* Fix Autofill Chrome agar menyatu dengan desain */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px rgba(8, 20, 40, 1) inset !important;
            -webkit-text-fill-color: #e0f2fe !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .auth-btn { background: linear-gradient(135deg, #0284c7 0%, #0891b2 100%); color: #f0f9ff; font-weight: 600; letter-spacing: 0.025em; }
        .auth-btn:hover { background: linear-gradient(135deg, #0369a1 0%, #0e7490 100%); box-shadow: 0 8px 20px rgba(14, 165, 233, 0.25); }
        .auth-link { color: #38bdf8; }
        .auth-link:hover { color: #7dd3fc; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4">

    <!-- Flash Notifications Container -->
    @if(session('success') || session('error') || $errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-x-12" x-init="setTimeout(() => show = false, 5000)" class="fixed z-[9999]" style="top: 24px; right: 24px; width: 350px; max-width: 90vw;">
        <div class="rounded-2xl p-3 sm:p-4 flex items-center gap-4" style="background-color: rgba(248, 250, 252, 0.92); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(203, 213, 225, 0.5); box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
            @if(session('success'))
            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full" style="background-color: #dcfce7;">
                <svg style="color: #16a34a; width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-sm sm:text-base tracking-tight leading-tight" style="color: #111827;">{{ session('success') }}</h3>
                <p class="text-xs mt-0.5" style="color: #6b7280;">Sistem memvalidasi operasi.</p>
            </div>
            @elseif(session('error') || $errors->any())
            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full" style="background-color: #fee2e2;">
                <svg style="color: #dc2626; width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-sm sm:text-base tracking-tight leading-tight" style="color: #111827;">{{ session('error') ?? $errors->first() }}</h3>
                <p class="text-xs mt-0.5" style="color: #6b7280;">Mohon periksa kembali.</p>
            </div>
            @endif
            
            <button @click="show = false" class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full transition-colors focus:outline-none hover:bg-gray-200" style="color: #9ca3af;">
                 <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="w-full max-w-md relative z-20">
        @yield('content')
        
        <!-- Footer Informasi -->
        <footer class="mt-8 text-center">
            <p class="text-[10px] font-bold uppercase tracking-[0.3em] style" style="color: #334e6c;">
                Profesional &bull; Aman &bull; Terintegrasi
            </p>
            <p class="mt-2 text-[9px] style" style="color: #2a3f5a;">
                &copy; 2026 Pengelolaan Perparkiran Digital Terpadu. All Rights Reserved.
            </p>
        </footer>
    </main>

</body>
</html>
