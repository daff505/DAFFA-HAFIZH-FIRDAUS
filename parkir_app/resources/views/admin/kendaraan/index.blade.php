@extends('layouts.app')

@section('title', 'Data Kendaraan')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Data Kendaraan</h2>
        <p class="text-slate-400 text-sm mt-1">Manajemen kendaraan terdaftar di sistem parkir</p>
    </div>
    <a href="{{ route('admin.kendaraan.create') }}" class="bg-indigo-500 hover:bg-indigo-600 px-4 py-2 rounded-lg text-white font-semibold transition flex items-center gap-2 self-start sm:self-auto">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Kendaraan
    </a>
</div>

@if(session('success'))
<div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-3 rounded-lg mb-6 text-sm flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-500/10 border border-red-500/30 text-red-400 p-3 rounded-lg mb-6 text-sm flex items-center gap-2">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    {{ session('error') }}
</div>
@endif

{{-- Search Bar --}}
<form action="{{ route('admin.kendaraan.index') }}" method="GET" class="mb-6">
    <div class="relative">
        <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari plat nomor, pemilik, atau merek kendaraan..."
            class="w-full bg-slate-800/50 border border-slate-700 rounded-xl pl-12 pr-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
    </div>
</form>

<div class="card p-6 rounded-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-700 text-sm text-slate-400">
                    <th class="pb-3 font-medium">Plat Nomor</th>
                    <th class="pb-3 font-medium">Jenis</th>
                    <th class="pb-3 font-medium">Merek / Warna</th>
                    <th class="pb-3 font-medium">Pemilik</th>
                    <th class="pb-3 font-medium">User Terkait</th>
                    <th class="pb-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-slate-300">
                @forelse($kendaraans as $kendaraan)
                <tr class="border-b border-slate-700/50 hover:bg-slate-800/50 transition">
                    <td class="py-3 font-bold text-sky-400 tracking-wider font-mono">{{ $kendaraan->plat_nomor }}</td>
                    <td class="py-3">
                        @php
                            $jenisConfig = [
                                'motor'   => ['color' => 'bg-amber-500/20 text-amber-400', 'icon' => '🏍️'],
                                'mobil'   => ['color' => 'bg-sky-500/20 text-sky-400', 'icon' => '🚗'],
                                'truk'    => ['color' => 'bg-red-500/20 text-red-400', 'icon' => '🚛'],
                                'lainnya' => ['color' => 'bg-slate-500/20 text-slate-400', 'icon' => '🚌'],
                            ];
                            $cfg = $jenisConfig[$kendaraan->jenis_kendaraan] ?? $jenisConfig['lainnya'];
                        @endphp
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $cfg['color'] }}">
                            {{ $cfg['icon'] }} {{ ucfirst($kendaraan->jenis_kendaraan) }}
                        </span>
                    </td>
                    <td class="py-3">
                        <span class="text-white font-medium">{{ $kendaraan->merk ?? '-' }}</span>
                        @if($kendaraan->warna)
                            <span class="text-slate-400"> / {{ $kendaraan->warna }}</span>
                        @endif
                    </td>
                    <td class="py-3 text-slate-300">{{ $kendaraan->pemilik ?? '-' }}</td>
                    <td class="py-3">
                        @if($kendaraan->user)
                            <span class="text-emerald-400 text-xs">{{ $kendaraan->user->nama_lengkap }}</span>
                        @else
                            <span class="text-slate-600 text-xs italic">Tamu / Umum</span>
                        @endif
                    </td>
                    <td class="py-3 text-right">
                        <div class="flex justify-end gap-3 items-center">
                            <a href="{{ route('admin.kendaraan.edit', $kendaraan->id_kendaraan) }}" class="text-sky-400 hover:text-sky-300 transition font-medium">Edit</a>
                            <form action="{{ route('admin.kendaraan.destroy', $kendaraan->id_kendaraan) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data kendaraan {{ $kendaraan->plat_nomor }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 transition font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2.69-2.69A2 2 0 017.1 13H17a2 2 0 012 2v1"></path></svg>
                            @if($search)
                                <p>Tidak ada kendaraan dengan kata kunci <strong class="text-white">"{{ $search }}"</strong>.</p>
                            @else
                                <p>Belum ada data kendaraan yang terdaftar.</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- [STANDAR PERFORMA - A & E] Paginasi untuk efisiensi pemuatan data besar --}}
    @if($kendaraans->hasPages())
    <div class="mt-6">
        {{ $kendaraans->appends(['search' => $search])->links() }}
    </div>
    @endif
</div>
@endsection
