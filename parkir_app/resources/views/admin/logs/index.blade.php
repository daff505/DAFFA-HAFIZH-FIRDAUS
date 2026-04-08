@extends('layouts.app')

@section('title', 'Log Aktivitas Sistem')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-white tracking-tight">Log Aktivitas Sistem</h2>
    <p class="text-slate-400 text-sm mt-1">Rekam jejak seluruh aktivitas petugas dan perubahan data sistem untuk Pengelola.</p>
</div>

{{-- Search Bar --}}
<form action="{{ route('admin.logs.index') }}" method="GET" class="mb-6">
    <div class="relative">
        <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama petugas, aktivitas, atau detail tertentu..." 
            class="w-full bg-slate-800/50 border border-slate-700 rounded-xl pl-12 pr-4 py-3 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
    </div>
</form>

<div class="card p-6 rounded-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-700 text-sm text-slate-400">
                    <th class="pb-3 font-medium">Waktu</th>
                    <th class="pb-3 font-medium">Petugas / User</th>
                    <th class="pb-3 font-medium">Aktivitas</th>
                    <th class="pb-3 font-medium">Target Tabel</th>
                    <th class="pb-3 font-medium">Detail</th>
                    <th class="pb-3 font-medium text-right">Alamat IP</th>
                </tr>
            </thead>
            <tbody class="text-xs sm:text-sm text-slate-300">
                @forelse($logs as $log)
                <tr class="border-b border-slate-700/50 hover:bg-slate-800/50 transition">
                    <td class="py-3 whitespace-nowrap">{{ date('d M Y H:i:s', strtotime($log->waktu_aktivitas)) }}</td>
                    <td class="py-3">
                        <span class="block font-bold text-sky-400">{{ $log->nama_lengkap }}</span>
                        <span class="text-[10px] uppercase text-slate-500 tracking-wider">{{ $log->role }}</span>
                    </td>
                    <td class="py-3">{{ $log->aktivitas }}</td>
                    <td class="py-3">
                        @if($log->tabel_terkait)
                            <span class="px-2 py-1 rounded bg-slate-700 text-[10px] font-mono">{{ $log->tabel_terkait }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-3 text-slate-400 max-w-xs truncate" title="{{ $log->detail }}">{{ $log->detail ?? '-' }}</td>
                    <td class="py-3 text-right font-mono text-xs text-slate-500">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @if($search)
                                <p>Tidak ditemukan log aktivitas dengan kata kunci <strong class="text-white">"{{ $search }}"</strong>.</p>
                            @else
                                <p>Belum ada rekaman log aktivitas sistem.</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="mt-6">
        {{ $logs->appends(['search' => $search])->links() }}
    </div>
    @endif
</div>
@endsection
