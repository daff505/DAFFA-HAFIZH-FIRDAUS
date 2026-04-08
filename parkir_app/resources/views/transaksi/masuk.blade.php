@extends('layouts.app')

@section('title', 'Kendaraan Masuk')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Form Kendaraan Masuk</h2>
        <p class="text-slate-400 text-sm mt-1">Sistem pencatatan terintegrasi</p>
    </div>
    <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-400 hover:text-white bg-slate-800/50 hover:bg-slate-800 px-4 py-2 rounded-lg transition">
        &larr; Kembali
    </a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="card p-8 rounded-3xl border-t-4 border-t-sky-500 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-sky-500/10 rounded-full blur-2xl"></div>
        
        @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('transaksi.storeMasuk') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Plat Nomor -->
                <div>
                    <label for="plat_nomor" class="block text-sm font-medium text-slate-300 mb-2">Nomor Polisi / Plat Kendaraan <span class="text-sky-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-bold">PLAT</span>
                        </div>
                        <input type="text" name="plat_nomor" id="plat_nomor" required
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl pl-16 pr-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition font-mono uppercase text-lg"
                            placeholder="B 1234 ABC" maxlength="20"
                            value="{{ old('plat_nomor') }}" autofocus>
                    </div>
                    @error('plat_nomor') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis Kendaraan -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Jenis Kendaraan <span class="text-sky-500">*</span></label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach(['motor' => 'Motor', 'mobil' => 'Mobil', 'truk' => 'Truk', 'lainnya' => 'Lainnya'] as $val => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis_kendaraan" value="{{ $val }}" class="peer sr-only" {{ old('jenis_kendaraan') == $val ? 'checked' : ($loop->first ? 'checked' : '') }}>
                            <div class="text-center p-3 rounded-xl border border-slate-700/50 bg-slate-900/30 text-slate-400 peer-checked:bg-sky-500/10 peer-checked:border-sky-500 peer-checked:text-sky-400 transition hover:bg-slate-800">
                                <span class="font-semibold">{{ $label }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Merk/Model -->
                    <div>
                        <label for="merk" class="block text-sm font-medium text-slate-300 mb-2">Merk / Model Kendaraan</label>
                        <input type="text" name="merk" id="merk"
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition text-sm"
                            placeholder="Contoh: Honda Vario / Toyota Avanza" maxlength="20"
                            value="{{ old('merk') }}">
                    </div>
                    <!-- Warna -->
                    <div>
                        <label for="warna" class="block text-sm font-medium text-slate-300 mb-2">Warna Kendaraan</label>
                        <input type="text" name="warna" id="warna"
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition text-sm"
                            placeholder="Contoh: Hitam / Putih" maxlength="20"
                            value="{{ old('warna') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Pemilik (Manual) -->
                    <div>
                        <label for="pemilik" class="block text-sm font-medium text-slate-300 mb-2">Nama Pemilik (Luar Member)</label>
                        <input type="text" name="pemilik" id="pemilik"
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition text-sm"
                            placeholder="Ketik nama pemilik jika ada" maxlength="20"
                            value="{{ old('pemilik') }}">
                    </div>
                    <!-- Kaitkan Akun User -->
                    <div>
                        <label for="id_user" class="block text-sm font-medium text-slate-300 mb-2">Kaitkan Akun Member</label>
                        <select name="id_user" id="id_user"
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl px-4 py-3 text-slate-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition appearance-none text-sm">
                            <option value="">-- Pilih Jika Kendaraan Member --</option>
                            @foreach($owners as $u)
                                <option value="{{ $u->id_user }}" 
                                    data-nama="{{ $u->nama_lengkap }}"
                                    {{ old('id_user') == $u->id_user ? 'selected' : '' }}>
                                    {{ $u->nama_lengkap }} ({{ $u->username }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Area Parkir -->
                <div>
                    <label for="id_area" class="block text-sm font-medium text-slate-300 mb-2">Pilih Area Parkir <span class="text-sky-500">*</span></label>
                    <div class="relative">
                        <select name="id_area" id="id_area" required
                            class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl px-4 py-3 text-slate-300 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition appearance-none">
                            <option value="" disabled selected>-- Pilih Area yang Tersedia --</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id_area }}" {{ old('id_area') == $area->id_area ? 'selected' : '' }}>
                                    {{ $area->nama_area }} - {{ $area->lokasi }} (Sisa: {{ $area->kapasitas - $area->terisi }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    @error('id_area') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-semibold py-4 rounded-xl transition shadow-lg shadow-sky-500/20 text-lg flex justify-center items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Proses Tiket Masuk
                </button>
            </div>
            
            <p class="text-xs text-center text-slate-500 mt-4">Pastikan menginput nomor polisi dengan benar sesuai STNK / fisik kendaraan.</p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectUser = document.getElementById('id_user');
        const inputPemilik = document.getElementById('pemilik');

        if (selectUser && inputPemilik) {
            selectUser.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const nama = selectedOption.getAttribute('data-nama');
                
                if (nama) {
                    inputPemilik.value = nama;
                }
            });
        }
    });
</script>
@endpush
