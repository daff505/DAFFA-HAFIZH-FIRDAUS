@extends('layouts.app')

@section('title', 'Data Pengguna & Petugas')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Data Pengguna & Petugas</h2>
        <p class="text-slate-400 text-sm mt-1">Manajemen hak akses aplikasi (Admin, Petugas, Owner)</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="bg-sky-500 hover:bg-sky-600 px-4 py-2 rounded-lg text-white font-semibold transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Pengguna
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
                    <th class="pb-3 font-medium">No</th>
                    <th class="pb-3 font-medium">Nama Lengkap</th>
                    <th class="pb-3 font-medium">Username</th>
                    <th class="pb-3 font-medium">Password</th>
                    <th class="pb-3 font-medium">Role</th>
                    <th class="pb-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-slate-300">
                @forelse($users as $index => $user)
                <tr class="border-b border-slate-700/50 hover:bg-slate-800/50 transition">
                    <td class="py-3">{{ $index + 1 }}</td>
                    <td class="py-3 font-bold text-white">{{ $user->nama_lengkap }}</td>
                    <td class="py-3 text-sky-400">{{ $user->username }}</td>
                    <td class="py-3 text-sky-400 font-mono">{{ $user->password_asli }}</td>
                    <td class="py-3 uppercase text-xs">
                        <span class="px-2 py-1 rounded {{ $user->role == 'admin' ? 'bg-sky-500/20 text-sky-400' : ($user->role == 'petugas' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400') }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="py-3 text-right flex justify-end gap-3 items-center h-full pt-4">
                        <a href="{{ route('admin.users.edit', $user->id_user) }}" class="text-sky-400 hover:text-sky-300 transition">Edit</a>
                        <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 transition">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-500">Belum ada data pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
