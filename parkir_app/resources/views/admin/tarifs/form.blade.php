@extends('layouts.app')

@section('title', $tarif->exists ? 'Edit Tarif Kendaraan' : 'Tambah Tarif Baru')

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.tarifs.index') }}" class="text-sky-400 hover:text-sky-300 text-sm flex items-center mb-4 transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Data Tarif
    </a>
    <h2 class="text-2xl font-bold text-white tracking-tight">{{ $tarif->exists ? 'Edit Tarif: ' . ucfirst($tarif->jenis_kendaraan) : 'Tambah Tarif Baru' }}</h2>
    <p class="text-slate-400 text-sm mt-1">Atur biaya parkir per jam sesuai jenis kendaraan.</p>
</div>

<div class="card p-6 md:p-8 rounded-2xl w-full">
    <form action="{{ $tarif->exists ? route('admin.tarifs.update', $tarif->id_tarif) : route('admin.tarifs.store') }}" method="POST">
        @csrf
        @if($tarif->exists)
            @method('PUT')
        @endif

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jenis Kendaraan -->
                <div>
                    <label for="jenis_kendaraan" class="block text-sm font-medium text-slate-300 mb-2">Jenis Kendaraan</label>
                    <select name="jenis_kendaraan" id="jenis_kendaraan" required class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                        <option value="motor" {{ old('jenis_kendaraan', $tarif->jenis_kendaraan) == 'motor' ? 'selected' : '' }}>Motor</option>
                        <option value="mobil" {{ old('jenis_kendaraan', $tarif->jenis_kendaraan) == 'mobil' ? 'selected' : '' }}>Mobil</option>
                        <option value="truk" {{ old('jenis_kendaraan', $tarif->jenis_kendaraan) == 'truk' ? 'selected' : '' }}>Truk</option>
                        <option value="lainnya" {{ old('jenis_kendaraan', $tarif->jenis_kendaraan) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis_kendaraan') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tarif Per Jam -->
                <div>
                    <label for="tarif_per_jam" class="block text-sm font-medium text-slate-300 mb-2">Tarif Per Jam (Rp)</label>
                    <input type="number" name="tarif_per_jam" id="tarif_per_jam" value="{{ old('tarif_per_jam', $tarif->tarif_per_jam) }}" required min="0" step="500" placeholder="Contoh: 2000, 5000"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                    @error('tarif_per_jam') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Berlaku Mulai -->
                <div>
                    <label for="berlaku_mulai" class="block text-sm font-medium text-slate-300 mb-2">Berlaku Mulai</label>
                    <input type="date" name="berlaku_mulai" id="berlaku_mulai" value="{{ old('berlaku_mulai', $tarif->berlaku_mulai) }}" required
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition [color-scheme:dark]">
                    @error('berlaku_mulai') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Berlaku Hingga -->
                <div>
                    <label for="berlaku_hingga" class="block text-sm font-medium text-slate-300 mb-2">Berlaku Hingga <span class="text-slate-500">(Opsional)</span></label>
                    <input type="date" name="berlaku_hingga" id="berlaku_hingga" value="{{ old('berlaku_hingga', $tarif->berlaku_hingga) }}"
                        class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition [color-scheme:dark]">
                    <p class="text-slate-500 text-xs mt-1">Kosongkan jika tarif berlaku selamanya.</p>
                    @error('berlaku_hingga') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="border-slate-700/50 pt-4">

            <div class="flex justify-end">
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 px-6 py-3 rounded-xl text-white font-bold transition">
                    {{ $tarif->exists ? 'Simpan Perubahan' : 'Buat Tarif Baru' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
