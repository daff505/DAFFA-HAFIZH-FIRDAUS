@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
<div class="auth-card p-8 rounded-3xl shadow-2xl mt-8 mb-8">
    <div class="text-center mb-8">
        <!-- Logo Image -->
        <img src="{{ asset('images/logo_aplikasi_parkir.png') }}" alt="Logo Parkir" class="mx-auto mb-4 object-contain drop-shadow-md rounded-xl" style="max-width: 100px; max-height: 100px; display: block; margin: 0 auto;">

        <h1 class="text-2xl font-bold tracking-tight" style="color: #e0f2fe;">Registrasi Petugas Baru</h1>
        <p class="mt-2 text-sm leading-relaxed" style="color: #7dd3fc;">Daftarkan akun operasional Anda <br> untuk mulai mengelola area parkir.</p>
    </div>

    <form action="{{ url('register') }}" method="POST" class="space-y-4">
        @csrf
        
        <!-- Nama Lengkap -->
        <div>
            <label for="nama_lengkap" class="block text-sm font-medium mb-1" style="color: #bae6fd;">Nama Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5" style="color: #0ea5e9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" maxlength="20" class="auth-input block w-full pl-10 pr-3 py-2.5 rounded-xl leading-5 sm:text-sm transition duration-150 ease-in-out" placeholder="Masukkan nama lengkap" required>
            </div>
            @error('nama_lengkap')
                <p class="mt-1 text-sm" style="color: #f87171;">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium mb-1" style="color: #bae6fd;">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" style="color: #0ea5e9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" maxlength="20" class="auth-input block w-full pl-10 pr-3 py-2.5 rounded-xl leading-5 sm:text-sm transition duration-150 ease-in-out" placeholder="Username" required>
                </div>
                @error('username')
                    <p class="mt-1 text-sm" style="color: #f87171;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium mb-1" style="color: #bae6fd;">Peran Akses</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                        <svg class="h-5 w-5" style="color: #0ea5e9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <select name="role" id="role" class="auth-input block w-full pl-10 pr-8 py-2.5 rounded-xl leading-5 sm:text-sm transition duration-150 ease-in-out appearance-none" required>
                        <option value="" disabled selected style="background-color: #0f1e37;">Pilih Peran</option>
                        <option value="petugas" style="background-color: #0f1e37;">Petugas Parkir</option>
                        <option value="owner" style="background-color: #0f1e37;">Pemilik (Owner)</option>
                        <option value="admin" style="background-color: #0f1e37;">Administrator</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 items-center flex pointer-events-none">
                        <svg class="w-5 h-5 z-10" style="color: #38bdf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                @error('role')
                    <p class="mt-1 text-sm" style="color: #f87171;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Password Baru -->
            <div x-data="{ show: false }">
                <label for="password" class="block text-sm font-medium mb-1" style="color: #bae6fd;">Password Baru</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" style="color: #0ea5e9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input :type="show ? 'text' : 'password'" name="password" id="password" maxlength="20" class="auth-input block w-full pl-10 pr-10 py-2.5 rounded-xl leading-5 sm:text-sm transition duration-150 ease-in-out" placeholder="Buat password" required>
                    <!-- Toggle Button -->
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center focus:outline-none transition-colors" style="color: #38bdf8;" onmouseover="this.style.color='#e0f2fe'" onmouseout="this.style.color='#38bdf8'">
                        <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div x-data="{ show: false }">
                <label for="password_confirmation" class="block text-sm font-medium mb-1" style="color: #bae6fd;">Konfirmasi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5" style="color: #0ea5e9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <input :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" maxlength="20" class="auth-input block w-full pl-10 pr-10 py-2.5 rounded-xl leading-5 sm:text-sm transition duration-150 ease-in-out" placeholder="Ulangi password" required>
                    <!-- Toggle Button -->
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center focus:outline-none transition-colors" style="color: #38bdf8;" onmouseover="this.style.color='#e0f2fe'" onmouseout="this.style.color='#38bdf8'">
                        <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="auth-btn w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm transition duration-150 ease-in-out">
                Daftar Akun
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm" style="color: #7dd3fc;">
            Sudah memiliki akun? 
            <a href="{{ url('login') }}" class="auth-link font-semibold transition-colors">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
