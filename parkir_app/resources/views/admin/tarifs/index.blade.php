@extends('layouts.app')

@section('title', 'Data Konfigurasi Tarif')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Konfigurasi Tarif</h2>
        <p class="text-slate-400 text-sm mt-1">Pengaturan biaya parkir per jam berdasarkan jenis kendaraan</p>
    </div>
    <a href="{{ route('admin.tarifs.create') }}" class="bg-emerald-500 hover:bg-emerald-600 px-4 py-2 rounded-lg text-white font-semibold transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Tarif
    </a>
</div>

@if(session('success'))
<div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-3 rounded-lg mb-6 text-sm">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-500/10 border border-red-500/30 text-red-400 p-3 rounded-lg mb-6 text-sm">
    {{ session('error') }}
</div>
@endif

<div class="card p-6 rounded-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-700 text-sm text-slate-400">
                    <th class="pb-3 font-medium">Jenis Kendaraan</th>
                    <th class="pb-3 font-medium">Tarif (Per Jam)</th>
                    <th class="pb-3 font-medium">Dimulai Tanggal</th>
                    <th class="pb-3 font-medium">Berakhir Tanggal</th>
                    <th class="pb-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-slate-300">
                @forelse($tarifs as $tarif)
                <tr class="border-b border-slate-700/50 hover:bg-slate-800/50 transition">
                    <td class="py-3 font-bold text-white uppercase">{{ $tarif->jenis_kendaraan }}</td>
                    <td class="py-3 text-emerald-400 font-bold">Rp {{ number_format($tarif->tarif_per_jam, 0, ',', '.') }}</td>
                    <td class="py-3">{{ date('d M Y', strtotime($tarif->berlaku_mulai)) }}</td>
                    <td class="py-3">{{ $tarif->berlaku_hingga ? date('d M Y', strtotime($tarif->berlaku_hingga)) : 'Selamanya' }}</td>
                    <td class="py-3 text-right flex justify-end gap-3 items-center pt-4">
                        <a href="{{ route('admin.tarifs.edit', $tarif->id_tarif) }}" class="text-sky-400 hover:text-sky-300 transition">Edit</a>
                        <form action="{{ route('admin.tarifs.destroy', $tarif->id_tarif) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tarif ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 transition">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-slate-500">Belum ada data tarif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
