@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-white tracking-tight">Profil Saya</h2>
    <p class="text-slate-400 text-sm mt-1">Informasi detail akun Anda yang terdaftar di sistem.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Profile Card -->
    <div class="lg:col-span-1">
        <div class="card p-8 rounded-2xl flex flex-col items-center text-center h-full">
            @php
                $avatarTheme = match($user->role) {
                    'admin' => 'bg-sky-500/20 text-sky-400 border-sky-400/30',
                    'petugas' => 'bg-emerald-500/20 text-emerald-400 border-emerald-400/30',
                    'owner' => 'bg-purple-500/20 text-purple-400 border-purple-400/30',
                    default => 'bg-slate-800 text-slate-400 border-slate-600'
                };
                $initial = strtoupper(substr($user->nama_lengkap, 0, 1));
            @endphp
            
            <div class="h-24 w-24 rounded-full flex items-center justify-center border-4 {{ $avatarTheme }} text-4xl font-bold shadow-xl mb-6">
                {{ $initial }}
            </div>
            
            <h3 class="text-xl font-bold text-white mb-1">{{ $user->nama_lengkap }}</h3>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $avatarTheme }} border tracking-wider">
                {{ $user->role }}
            </span>
            
            <div class="mt-8 pt-8 border-t border-slate-700/50 w-full text-left space-y-4">
                <div class="flex items-center gap-3 text-slate-400 text-sm">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Terdaftar pada: {{ $user->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex items-center gap-3 text-slate-400 text-sm">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="text-emerald-400 font-medium">Status: Akun Aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="lg:col-span-2">
        <div class="card p-8 rounded-2xl h-full">
            <h3 class="text-lg font-bold text-white mb-6 border-b border-slate-700/50 pb-4">Detail Akun</h3>
            
            <div class="space-y-6">
                <!-- Username -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                    <span class="text-slate-400 font-medium">Username</span>
                    <div class="md:col-span-2">
                        <div class="bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-sky-400 font-mono">
                            {{ $user->username }}
                        </div>
                    </div>
                </div>

                <!-- Password Display with Toggle -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-center" x-data="{ show: false }">
                    <span class="text-slate-400 font-medium whitespace-nowrap">Kata Sandi Anda</span>
                    <div class="md:col-span-2 relative">
                        <div class="bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white">
                            <span x-show="!show" class="font-mono tracking-widest text-lg">••••••••</span>
                            <span x-show="show" class="font-mono text-sky-400" style="display: none;">{{ $user->password_asli }}</span>
                        </div>
                        <button @click="show = !show" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-sky-400">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Info Box -->
                @if($user->role !== 'admin')
                <div class="mt-10 p-5 bg-sky-500/10 border border-sky-500/20 rounded-2xl flex gap-4">
                    <div class="bg-sky-500/20 p-3 rounded-xl h-fit">
                        <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-white font-bold mb-1 italic">Butuh Mengubah Data?</h4>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Jika terdapat ketidaksesuaian data atau Anda ingin mengganti nama/username, silakan hubungi <span class="text-sky-400 font-bold">Administrator Utama</span> sistem parkir ini.
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
