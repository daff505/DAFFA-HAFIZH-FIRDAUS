@extends('layouts.app')

@section('title', $area->exists ? 'Edit Area Parkir' : 'Tambah Area Parkir Baru')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.areas.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-4 transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Data Area Parkir
    </a>
    <h2 class="text-2xl font-bold text-white tracking-tight">{{ $area->exists ? 'Edit Area: ' . $area->nama_area : 'Tambah Area Parkir Baru' }}</h2>
    <p class="text-slate-400 text-sm mt-1">Lengkapi formulir area parkir di bawah ini.</p>
</div>

<div class="card p-6 md:p-8 rounded-2xl w-full">
    <form action="{{ $area->exists ? route('admin.areas.update', $area->id_area) : route('admin.areas.store') }}" method="POST">
        @csrf
        @if($area->exists)
            @method('PUT')
        @endif

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kode Area -->
                <div>
                    <label for="kode_area" class="block text-sm font-medium text-slate-300 mb-2">Kode Area</label>
                    <input type="text" name="kode_area" id="kode_area" value="{{ old('kode_area', $area->kode_area) }}" required placeholder="Contoh: A-01, B-02" maxlength="20"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    @error('kode_area') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Area -->
                <div>
                    <label for="nama_area" class="block text-sm font-medium text-slate-300 mb-2">Nama Area</label>
                    <input type="text" name="nama_area" id="nama_area" value="{{ old('nama_area', $area->nama_area) }}" required placeholder="Contoh: Area Motor, Area Mobil" maxlength="50"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    @error('nama_area') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Lokasi -->
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-slate-300 mb-2">Lokasi / Nama Tempat</label>
                    <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $area->lokasi) }}" placeholder="Contoh: Basement 1, Gedung A, Lantai 2" maxlength="50"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    @error('lokasi') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kapasitas -->
                <div>
                    <label for="kapasitas" class="block text-sm font-medium text-slate-300 mb-2">Kapasitas Maksimal (Slot)</label>
                    <input type="number" name="kapasitas" id="kapasitas" value="{{ old('kapasitas', $area->kapasitas) }}" required min="1" placeholder="Jumlah maksimal kendaraan"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    @error('kapasitas') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Status Area -->
                <div>
                    <label for="status_area" class="block text-sm font-medium text-slate-300 mb-2">Status Area</label>
                    <select name="status_area" id="status_area" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                        <option value="aktif" {{ old('status_area', $area->status_area) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status_area', $area->status_area) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="perbaikan" {{ old('status_area', $area->status_area) == 'perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                    </select>
                    @error('status_area') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            @if($area->exists)
            <div class="bg-sky-500/10 border border-sky-500/20 text-sky-400 text-xs p-3 rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Saat ini area <strong>{{ $area->nama_area }}</strong> terisi <strong>{{ $area->terisi }}</strong> dari <strong>{{ $area->kapasitas }}</strong> slot. Kolom terisi tidak bisa diedit manual karena dihitung otomatis dari aktivitas loket.
            </div>
            @endif

            <hr class="border-slate-700/50 pt-4">

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 px-6 py-3 rounded-xl text-white font-bold transition">
                    {{ $area->exists ? 'Simpan Perubahan' : 'Buat Area Baru' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
