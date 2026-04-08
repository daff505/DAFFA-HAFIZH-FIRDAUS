@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<div class="auth-card p-8 rounded-3xl shadow-2xl">
    <div class="text-center mb-8">
        <!-- Logo Image -->
        <img src="{{ asset('images/logo_aplikasi_parkir.png') }}" alt="Logo Parkir" class="mx-auto mb-4 object-contain drop-shadow-md rounded-xl" style="max-width: 100px; max-height: 100px; display: block; margin: 0 auto;">
        
        <h1 class="text-2xl font-bold tracking-tight" style="color: #e0f2fe;">Autentikasi Pengelola</h1>
        <p class="mt-2 text-sm leading-relaxed" style="color: #7dd3fc;">Silakan masuk untuk akses kontrol <br> perparkiran dan laporan harian.</p>
    </div>

    <form action="{{ url('login') }}" method="POST" class="space-y-5">
        @csrf
        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-medium mb-1" style="color: #bae6fd;">Username</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5" style="color: #0ea5e9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <input type="text" name="username" id="username" value="{{ old('username') }}" maxlength="20" class="auth-input block w-full pl-10 pr-3 py-2.5 rounded-xl leading-5 sm:text-sm transition duration-150 ease-in-out" placeholder="Masukkan username" required>
            </div>
            @error('username')
                <p class="mt-1 text-sm font-medium" style="color: #f87171;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium mb-1" style="color: #bae6fd;">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5" style="color: #0ea5e9;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input :type="show ? 'text' : 'password'" name="password" id="password" maxlength="20" class="auth-input block w-full pl-10 pr-10 py-2.5 rounded-xl leading-5 sm:text-sm transition duration-150 ease-in-out" placeholder="Masukkan password" required>
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

        <!-- Submit Button -->
        <div>
            <button type="submit" class="auth-btn w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm transition duration-150 ease-in-out">
                Masuk
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm" style="color: #7dd3fc;">
            Belum punya akun? 
            <a href="{{ url('register') }}" class="auth-link font-semibold transition-colors">Daftar sekarang</a>
        </p>
    </div>
</div>
@endsection
