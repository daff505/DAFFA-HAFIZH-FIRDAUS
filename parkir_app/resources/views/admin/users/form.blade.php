@extends('layouts.app')

@section('title', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna Baru')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.users.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-4 transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Data Pengguna
    </a>
    <h2 class="text-2xl font-bold text-white tracking-tight">{{ $user->exists ? 'Edit Data: ' . $user->nama_lengkap : 'Tambah Pengguna Baru' }}</h2>
    <p class="text-slate-400 text-sm mt-1">Lengkapi formulir di bawah ini dengan informasi yang valid.</p>
</div>

<div class="card p-6 md:p-8 rounded-2xl w-full">
    <form action="{{ $user->exists ? route('admin.users.update', $user->id_user) : route('admin.users.store') }}" method="POST">
        @csrf
        @if($user->exists)
            @method('PUT')
        @endif

        <div class="space-y-6">
            <!-- Nama Lengkap -->
            <div>
                <label for="nama_lengkap" class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required maxlength="20"
                    class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
                @error('nama_lengkap') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-300 mb-2">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required maxlength="20"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
                    @error('username') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-300 mb-2">Role / Akses</label>
                    <select name="role" id="role" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                        <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>Owner</option>
                    </select>
                    @error('role') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Password -->
            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password" {{ $user->exists ? '' : 'required' }} maxlength="20"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 pr-12 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition"
                        placeholder="{{ $user->exists ? 'Ketik ulang password jika ingin mengubahnya (biarkan kosong jika tidak)' : 'Masukkan password baru (max: 20 karakter)' }}">
                    
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-sky-400 transition">
                        <!-- Eye icon (Hidden) -->
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <!-- Eye-slash icon (Shown) -->
                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
                @error('password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <hr class="border-slate-700/50 pt-4">

            <!-- Tombol Simpan -->
            <div class="flex justify-end">
                <button type="submit" class="bg-sky-500 hover:bg-sky-600 px-6 py-3 rounded-xl text-white font-bold transition">
                    {{ $user->exists ? 'Simpan Perubahan' : 'Buat Pengguna' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
