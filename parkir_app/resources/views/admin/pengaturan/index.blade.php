@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-white tracking-tight">Pengaturan Sistem</h2>
    <p class="text-slate-400 text-sm mt-1">Konfigurasi global aplikasi parkir — perubahan berlaku langsung ke seluruh sistem</p>
</div>

@if(session('success'))
<div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-3 rounded-lg mb-6 text-sm">
    {{ session('success') }}
</div>
@endif

<div class="card p-6 md:p-8 rounded-2xl w-full">
    <form action="{{ route('admin.pengaturan.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            @foreach($settings as $setting)
            <div class="bg-slate-800/30 border border-slate-700/50 rounded-xl p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <label for="setting_{{ $setting->id_pengaturan }}" class="block text-sm font-semibold text-white mb-1">
                            @switch($setting->kunci)
                                @case('nama_aplikasi')
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        Nama Aplikasi
                                    </span>
                                    @break
                                @case('waktu_tenggat')
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Waktu Tenggat Parkir (Jam)
                                    </span>
                                    @break
                                @case('denda_per_jam')
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Denda Keterlambatan (Rp/Jam)
                                    </span>
                                    @break
                                @case('struk_header')
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Header Struk Cetak
                                    </span>
                                    @break
                                @default
                                    {{ ucfirst(str_replace('_', ' ', $setting->kunci)) }}
                            @endswitch
                        </label>
                        <p class="text-xs text-slate-500">{{ $setting->keterangan }}</p>
                    </div>
                    <div class="md:w-1/2">
                        @if($setting->kunci == 'struk_header')
                            <textarea name="settings[{{ $setting->id_pengaturan }}]" id="setting_{{ $setting->id_pengaturan }}" rows="3"
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition text-sm">{{ $setting->nilai }}</textarea>
                        @elseif(in_array($setting->kunci, ['waktu_tenggat', 'denda_per_jam']))
                            <input type="number" name="settings[{{ $setting->id_pengaturan }}]" id="setting_{{ $setting->id_pengaturan }}" value="{{ $setting->nilai }}" min="0"
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition text-sm">
                        @else
                            <input type="text" name="settings[{{ $setting->id_pengaturan }}]" id="setting_{{ $setting->id_pengaturan }}" value="{{ $setting->nilai }}"
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition text-sm">
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <hr class="border-slate-700/50 pt-4">

            <div class="flex justify-end">
                <button type="submit" class="bg-sky-500 hover:bg-sky-600 px-6 py-3 rounded-xl text-white font-bold transition">
                    Simpan Semua Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
