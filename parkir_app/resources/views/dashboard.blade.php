@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white tracking-tight">Dashboard Overview</h2>
        <p class="text-slate-400 text-sm mt-1">Status Real-time dari Basis Data Parkir</p>
    </div>

    @if(auth()->check() && auth()->user()->role == 'admin')
        <!-- ADMIN DASHBOARD WIDGETS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="card p-5 rounded-2xl border-t-4 border-t-sky-500">
                <p class="text-slate-400 text-sm font-medium">Zonasi Area (Blok)</p>
                <h3 class="text-3xl font-bold text-white mt-2">{{ $data['total_area'] ?? 0 }}</h3>
            </div>
            <div class="card p-5 rounded-2xl border-t-4 border-t-indigo-500">
                <p class="text-slate-400 text-sm font-medium">Banyak Hak Akses User</p>
                <h3 class="text-3xl font-bold text-white mt-2">{{ $data['total_pengguna'] ?? 0 }}</h3>
            </div>
            <div class="card p-5 rounded-2xl border-t-4 border-t-emerald-500">
                <p class="text-slate-400 text-sm font-medium">Total Volume Kendaraan</p>
                <h3 class="text-3xl font-bold text-white mt-2">{{ $data['total_kendaraan'] ?? 0 }}</h3>
            </div>
            <div class="card p-5 rounded-2xl border-t-4 border-t-amber-500">
                <p class="text-slate-400 text-sm font-medium">Total Akumulasi Transaksi</p>
                <h3 class="text-3xl font-bold text-white mt-2">{{ $data['total_transaksi'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- GRAFIK PENDAPATAN (KHUSUS PENGELOLA/BOS) -->
        <div class="card p-6 rounded-2xl mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-semibold text-white">Grafik Pendapatan 7 Hari Terakhir</h3>
                    @php
                        $chartData = $data['chart']['data'] ?? [];
                        $isDropping = false;
                        if(count($chartData) >= 2) {
                            $today = end($chartData);
                            $yesterday = prev($chartData);
                            $isDropping = ($today < $yesterday) || ($today == 0);
                        }
                    @endphp
                    @if($isDropping)
                        <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-500/10 text-red-500 text-[10px] font-bold border border-red-500/20 animate-pulse uppercase">
                            🔴 Status: Lesu/Sepi
                        </span>
                    @else
                        <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-500 text-[10px] font-bold border border-emerald-500/20 uppercase">
                            🟢 Status: Lancar
                        </span>
                    @endif
                </div>
                <span class="text-[10px] text-slate-500 bg-slate-800 px-3 py-1 rounded-full border border-slate-700 font-mono">DATA TERVERIFIKASI</span>
            </div>
            
            <!-- Legenda Penjelasan Warna -->
            <div class="flex items-center gap-4 mb-6 text-[10px]">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 rounded-full bg-emerald-500 h-2"></span>
                    <span class="text-slate-400">HIJAU: Pendapatan Bagus/Stabil</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 rounded-full bg-red-500 h-2"></span>
                    <span class="text-slate-400">MERAH: Pendapatan Turun/Aplikasi Sepi</span>
                </div>
            </div>

            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Log Aktivitas Admin -->
        <div class="card p-6 rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white">Log Aktivitas Terbaru</h3>
                <a href="{{ route('admin.logs.index') }}" class="text-xs text-sky-400 hover:underline">Lihat Semua →</a>
            </div>

            @if(isset($data['log_aktivitas']) && count($data['log_aktivitas']) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-700 text-sm text-slate-400">
                                <th class="pb-3 font-medium">Waktu</th>
                                <th class="pb-3 font-medium">Petugas</th>
                                <th class="pb-3 font-medium">Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-slate-300">
                            @foreach($data['log_aktivitas']->take(5) as $log)
                                <tr class="border-b border-slate-700/50 hover:bg-slate-800/50 transition">
                                    <td class="py-3">{{ date('H:i', strtotime($log->waktu_aktivitas)) }}</td>
                                    <td class="py-3 font-bold text-sky-400">{{ $log->nama_lengkap }}</td>
                                    <td class="py-3 text-xs text-slate-400">{{ $log->aktivitas }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-slate-500 text-sm text-center py-4 italic">Belum ada aktivitas tercatat.</div>
            @endif
        </div>

    @elseif(auth()->check() && auth()->user()->role == 'petugas')
        <!-- PETUGAS DASHBOARD WIDGETS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="card p-6 rounded-3xl relative overflow-hidden group hover:border-sky-500/50 transition">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-sky-500/10 rounded-full blur-2xl group-hover:bg-sky-500/20 transition"></div>
                <h3 class="text-xl font-bold text-white">Entry Masuk</h3>
                <p class="text-slate-400 mt-2 text-sm">Proses kendaraan masuk ke area parkir.</p>
                <a href="{{ route('transaksi.masuk') }}" class="mt-6 w-full inline-block text-center bg-sky-500 hover:bg-sky-600 text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-sky-500/20 uppercase tracking-wider text-sm">
                    KENDARAAN MASUK
                </a>
            </div>
            <div class="card p-6 rounded-3xl relative overflow-hidden group hover:border-emerald-500/50 transition">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition"></div>
                <h3 class="text-xl font-bold text-white">Entry Keluar</h3>
                <p class="text-slate-400 mt-2 text-sm">Hitung tarif dan proses pembayaran keluar.</p>
                <a href="{{ route('transaksi.keluar') }}" class="mt-6 w-full inline-block text-center bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-emerald-500/20 uppercase tracking-wider text-sm">
                    KENDARAAN KELUAR
                </a>
            </div>
        </div>

        <div class="card p-6 rounded-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-white">Status Slot Parkir Aktif</h3>
                <span class="text-[10px] text-sky-400 bg-sky-500/10 px-3 py-1 rounded-full border border-sky-500/20">REAL-TIME DB</span>
            </div>

            <div class="space-y-6">
                @if(isset($data['areas']) && count($data['areas']) > 0)
                    @foreach($data['areas'] as $area)
                        @php
                            $percentage = ($area->kapasitas > 0) ? round(($area->terisi / $area->kapasitas) * 100) : 0;
                            $colorClass = $percentage > 85 ? 'bg-red-500' : ($percentage > 60 ? 'bg-amber-500' : 'bg-sky-500');
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-2">
                                <span class="text-slate-300 font-medium">{{ $area->nama_area }}</span>
                                <span class="text-slate-500">{{ $area->terisi }} / {{ $area->kapasitas }} Slot</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-3 border border-slate-700/30 overflow-hidden">
                                <div class="{{ $colorClass }} h-full transition-all duration-1000 ease-in-out" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-slate-500 text-sm italic">Data area parkir belum tersedia.</div>
                @endif
            </div>
        </div>

    @elseif(auth()->check() && auth()->user()->role == 'owner')
        <!-- OWNER (BOS PARKIR) DASHBOARD -->
        <div class="mb-8 p-8 rounded-2xl border border-sky-500/20 bg-gradient-to-br from-sky-500/10 to-indigo-500/5 relative overflow-hidden group">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl group-hover:bg-sky-500/20 transition duration-700"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                        <span class="text-3xl">🏢</span> Halo, {{ auth()->user()->nama_lengkap }}!
                    </h3>
                    <p class="text-slate-400 mt-2 text-sm leading-relaxed max-w-xl">
                        Selamat datang di panel kendali utama. Pantau performa finansial dan operasional parkir Anda secara real-time dari sini.
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('reports.index') }}" class="px-6 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-bold transition shadow-lg shadow-sky-500/20 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Buka Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- STATS WIDGETS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Pendapatan Card -->
            <div class="card p-6 rounded-2xl border-l-4 border-l-emerald-500">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-slate-400 text-sm font-medium uppercase tracking-wider">Pendapatan Bulan Ini</span>
                </div>
                <h3 class="text-2xl font-bold text-white font-mono">Rp {{ number_format($data['pendapatan'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-[10px] text-emerald-500 mt-2 font-bold uppercase">Terakumulasi Otomatis</p>
            </div>

            <!-- Volume Card -->
            <div class="card p-6 rounded-2xl border-l-4 border-l-sky-500">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-sky-500/10 rounded-xl text-sky-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-slate-400 text-sm font-medium uppercase tracking-wider">Volume Transaksi</span>
                </div>
                <h3 class="text-2xl font-bold text-white font-mono">{{ number_format($data['total_volume'] ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">Kendaraan</span></h3>
                <p class="text-[10px] text-sky-500 mt-2 font-bold uppercase">Bulan Berjalan</p>
            </div>

            <!-- Occupancy Card -->
            <div class="card p-6 rounded-2xl border-l-4 border-l-amber-500">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-amber-500/10 rounded-xl text-amber-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    @php
                        $occupancy = ($data['kapasitas_total'] ?? 0) > 0 ? round(($data['kapasitas_terisi'] / $data['kapasitas_total']) * 100) : 0;
                    @endphp
                    <span class="text-slate-400 text-sm font-medium uppercase tracking-wider">Okupansi Real-time</span>
                </div>
                <div class="flex items-end justify-between">
                    <h3 class="text-2xl font-bold text-white font-mono">{{ $occupancy }}%</h3>
                    <span class="text-xs text-slate-500">{{ $data['kapasitas_terisi'] ?? 0 }} / {{ $data['kapasitas_total'] ?? 0 }} Terisi</span>
                </div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden border border-slate-700">
                    <div class="bg-amber-500 h-full rounded-full transition-all duration-1000" style="width: {{ $occupancy }}%"></div>
                </div>
            </div>
        </div>

        <!-- RECENT TRANSACTIONS FOR OWNER -->
        <div class="card p-8 rounded-2xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Aktivitas Transaksi Terbaru
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-700 text-xs text-slate-500 uppercase tracking-widest">
                            <th class="pb-4 font-bold">Plat Nomor</th>
                            <th class="pb-4 font-bold">Tipe</th>
                            <th class="pb-4 font-bold">Area</th>
                            <th class="pb-4 font-bold text-right">Total Bayar</th>
                            <th class="pb-4 font-bold text-center">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($data['transaksi_terbaru'] ?? [] as $trx)
                        <tr class="border-b border-slate-700/50 hover:bg-slate-800/20 transition">
                            <td class="py-4 font-bold text-white uppercase tracking-tighter">{{ $trx->plat_nomor }}</td>
                            <td class="py-4">
                                <span class="px-2 py-0.5 rounded-md bg-slate-800 text-[10px] uppercase font-bold text-slate-400 border border-slate-700">{{ $trx->jenis_kendaraan }}</span>
                            </td>
                            <td class="py-4 text-slate-400 text-xs">{{ $trx->nama_area }}</td>
                            <td class="py-4 text-right font-bold text-emerald-400">Rp {{ number_format($trx->biaya_total, 0, ',', '.') }}</td>
                            <td class="py-4 text-center text-slate-500 text-[10px] font-mono">
                                {{ $trx->waktu_keluar ? date('d/m H:i', strtotime($trx->waktu_keluar)) : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-600 italic">Belum ada transaksi keluar yang tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    @if(auth()->check() && auth()->user()->role == 'admin')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('revenueChart');
                if (ctx) {
                    // Ambil Data dari Backend
                    const labels = {!! json_encode($data['chart']['labels'] ?? []) !!};
                    const rawData = {!! json_encode($data['chart']['data'] ?? []) !!};
                    
                    // Klasifikasi Data (Memecah Jadi 2 Dataset: Lancar vs Lesu)
                    const lancarData = [];
                    const lesuData = [];

                    rawData.forEach((val, i) => {
                        let prevVal = i > 0 ? rawData[i-1] : val;
                        let isDropping = (val < prevVal) || (val === 0);
                        
                        if (isDropping) {
                            lesuData.push(val);
                            lancarData.push(null); // Kosongkan di dataset lancar
                        } else {
                            lancarData.push(val);
                            lesuData.push(null); // Kosongkan di dataset lesu
                        }
                    });

                    // Tambahkan Titik Sambung agar tidak putus (Opsional: Ambil titik sebelumnya)
                    // Untuk efek visual yang lebih smooth

                    // Dataset Utama (Satu Data yang Sama Tapi Beda Gaya)
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Performa Lancar (Hijau)',
                                    data: rawData,
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    borderWidth: 4, // Makin tebal makin pro
                                    fill: true,
                                    tension: 0.4,
                                    segment: {
                                        borderColor: ctx => {
                                            const i = ctx.p0DataIndex;
                                            const v = rawData[i+1];
                                            const prevV = rawData[i];
                                            return (v < prevV || v === 0) ? 'transparent' : '#10b981';
                                        }
                                    }
                                },
                                {
                                    label: 'Performa Lesu (Merah)',
                                    data: rawData,
                                    borderColor: '#ef4444',
                                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                                    borderWidth: 4,
                                    fill: false,
                                    tension: 0.4,
                                    segment: {
                                        borderColor: ctx => {
                                            const i = ctx.p0DataIndex;
                                            const v = rawData[i+1];
                                            const prevV = rawData[i];
                                            return (v < prevV || v === 0) ? '#ef4444' : 'transparent';
                                        }
                                    }
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            //--- ANIMASI DASHBOARD PROFESIONAL ---
                            animation: {
                                duration: 2000,
                                easing: 'easeOutQuart',
                            },
                            animations: {
                                y: {
                                    from: 500, // Efek muncul dari bawah
                                },
                            },
                            plugins: {
                                legend: { 
                                    display: true, 
                                    position: 'top',
                                    labels: { color: '#94a3b8', usePointStyle: true, padding: 20 }
                                },
                                tooltip: { mode: 'index', intersect: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                    ticks: {
                                        color: '#94a3b8',
                                        callback: function (value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value); }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#94a3b8' }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
@endpush