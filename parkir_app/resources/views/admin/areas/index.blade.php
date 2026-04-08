@extends('layouts.app')

@section('title', 'Data Area Parkir')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Data Area Parkir</h2>
        <p class="text-slate-400 text-sm mt-1">Zonasi, blok, dan kapasitas kendaraan</p>
    </div>
    <a href="{{ route('admin.areas.create') }}" class="bg-indigo-500 hover:bg-indigo-600 px-4 py-2 rounded-lg text-white font-semibold transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Area
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
                    <th class="pb-3 font-medium">Kode Area</th>
                    <th class="pb-3 font-medium">Nama Area</th>
                    <th class="pb-3 font-medium">Lokasi</th>
                    <th class="pb-3 font-medium text-center">Kapasitas</th>
                    <th class="pb-3 font-medium text-center">Terisi</th>
                    <th class="pb-3 font-medium">Status</th>
                    <th class="pb-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-slate-300">
                @forelse($areas as $area)
                <tr class="border-b border-slate-700/50 hover:bg-slate-800/50 transition">
                    <td class="py-3 font-bold text-sky-400">{{ $area->kode_area }}</td>
                    <td class="py-3 text-white">{{ $area->nama_area }}</td>
                    <td class="py-3 text-slate-400">{{ $area->lokasi ?? '-' }}</td>
                    <td class="py-3 text-center">{{ $area->kapasitas }}</td>
                    <td class="py-3 text-center font-bold {{ $area->terisi >= $area->kapasitas ? 'text-red-400' : 'text-emerald-400' }}">{{ $area->terisi }}</td>
                    <td class="py-3 uppercase text-xs">
                        <span class="px-2 py-1 rounded {{ $area->status_area == 'aktif' ? 'bg-emerald-500/20 text-emerald-400' : ($area->status_area == 'perbaikan' ? 'bg-amber-500/20 text-amber-400' : 'bg-red-500/20 text-red-400') }}">
                            {{ $area->status_area }}
                        </span>
                    </td>
                    <td class="py-3 text-right flex justify-end gap-3 items-center pt-4">
                        <a href="{{ route('admin.areas.edit', $area->id_area) }}" class="text-sky-400 hover:text-sky-300 transition">Edit</a>
                        <form action="{{ route('admin.areas.destroy', $area->id_area) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus area parkir ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 transition">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-500">Belum ada data area parkir.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
