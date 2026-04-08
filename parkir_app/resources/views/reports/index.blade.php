@extends('layouts.app')

@section('title', 'Laporan Transaksi Parkir')

@section('content')
<!-- Tidak tampil saat diprint -->
<div class="print:hidden">
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Laporan Transaksi Parkir</h2>
            <p class="text-slate-400 text-sm mt-1">Rekapitulasi kendaraan masuk & keluar</p>
        </div>
        <div class="flex gap-3">
            <button onclick="exportToExcel()" class="bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 rounded-xl text-white font-bold transition shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor ke Excel
            </button>
            <button onclick="window.print()" class="bg-amber-500 hover:bg-amber-600 px-5 py-2.5 rounded-xl text-white font-bold transition shadow-lg shadow-amber-500/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card p-6 rounded-2xl mb-8">
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-2 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition [color-scheme:dark]">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-2 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition [color-scheme:dark]">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Status Transaksi</label>
                <select name="status" class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-2 text-white focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
                    <option value="">Semua Status</option>
                    <option value="masuk" {{ $status == 'masuk' ? 'selected' : '' }}>Masuk (Dalam Parkir)</option>
                    <option value="keluar" {{ $status == 'keluar' ? 'selected' : '' }}>Keluar (Sudah Keluar)</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-slate-700 hover:bg-slate-600 px-4 py-2.5 rounded-xl text-white font-bold transition border border-slate-600">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-emerald-500/20 to-emerald-900/10 border border-emerald-500/20 p-5 rounded-2xl">
            <h3 class="text-slate-400 text-sm font-medium mb-1">Total Pendapatan</h3>
            <p class="text-2xl font-bold text-emerald-400">Rp {{ number_format($total_income, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-br from-sky-500/20 to-sky-900/10 border border-sky-500/20 p-5 rounded-2xl">
            <h3 class="text-slate-400 text-sm font-medium mb-1">Total Kendaraan</h3>
            <p class="text-2xl font-bold text-sky-400">{{ number_format($total_vehicles, 0, ',', '.') }} Unit</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-500/20 to-indigo-900/10 border border-indigo-500/20 p-5 rounded-2xl">
            <h3 class="text-slate-400 text-sm font-medium mb-1">Transaksi Selesai</h3>
            <p class="text-2xl font-bold text-indigo-400">{{ number_format($completed_transactions, 0, ',', '.') }} Selesai</p>
        </div>
    </div>
</div>

<!-- Mulai Area Cetak Dokumen -->
<div class="card p-6 rounded-2xl print:bg-white print:text-black print:p-0 print:border-none print:shadow-none print:m-0">
    <div class="hidden print:block mb-6 text-center">
        <h1 class="text-2xl font-bold border-b border-gray-400 pb-2 mb-2">LAPORAN TRANSAKSI PARKIR</h1>
        <p class="text-sm">Periode: {{ $start_date ? date('d-m-Y', strtotime($start_date)) : 'Awal' }} s/d {{ $end_date ? date('d-m-Y', strtotime($end_date)) : 'Akhir' }}</p>
        <p class="text-sm">Dicetak oleh: {{ auth()->user()->nama_lengkap }} ({{ date('d-m-Y H:i') }})</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse print:text-xs">
            <thead>
                <tr class="border-b border-slate-700 print:border-black text-sm text-slate-400 print:text-black">
                    <th class="pb-3 font-medium">Tiket</th>
                    <th class="pb-3 font-medium">Plat Nomor</th>
                    <th class="pb-3 font-medium">Area</th>
                    <th class="pb-3 font-medium">Masuk</th>
                    <th class="pb-3 font-medium">Keluar</th>
                    <th class="pb-3 font-medium">Durasi</th>
                    <th class="pb-3 font-medium text-right">Biaya Total</th>
                </tr>
            </thead>
            <tbody class="text-sm text-slate-300 print:text-black">
                @forelse($reports as $row)
                <tr class="border-b border-slate-700/50 print:border-gray-300 hover:bg-slate-800/50 print:hover:bg-transparent transition">
                    <td class="py-3 font-mono text-sky-400 print:text-black">{{ $row->nomor_tiket }}</td>
                    <td class="py-3 font-bold text-white print:text-black">{{ $row->plat_nomor }}</td>
                    <td class="py-3">{{ $row->nama_area }}</td>
                    <td class="py-3">{{ date('d M Y H:i', strtotime($row->waktu_masuk)) }}</td>
                    <td class="py-3">
                        @if($row->waktu_keluar)
                            {{ date('d M Y H:i', strtotime($row->waktu_keluar)) }}
                        @else
                            <span class="text-amber-400 print:text-black text-xs italic">Masih Parkir</span>
                        @endif
                    </td>
                    <td class="py-3">{{ $row->durasi_jam ? $row->durasi_jam . ' Jam' : '-' }}</td>
                    <td class="py-3 text-right font-bold text-emerald-400 print:text-black">
                        {{ $row->biaya_total > 0 ? 'Rp ' . number_format($row->biaya_total, 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-slate-500 print:text-black">Tidak ada data transaksi pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            <!-- Footer Total hanya tampil saat di print sebagai rekap -->
            <tfoot class="hidden print:table-footer-group border-t-2 border-black font-bold">
                <tr>
                    <td colspan="6" class="py-4 text-right">TOTAL PENDAPATAN:</td>
                    <td class="py-4 text-right">Rp {{ number_format($total_income, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Tanda Tangan Cetak -->
    <div class="hidden print:flex justify-end mt-12 pr-10">
        <div class="text-center">
            <p>Mengetahui,</p>
            <p class="mt-20 border-b border-black font-bold">{{ auth()->user()->nama_lengkap }}</p>
            <p>{{ ucfirst(auth()->user()->role) }}</p>
        </div>
    </div>
</div>

<style>
    @media print {
        @page { size: landscape; margin: 0.5cm; }
        
        /* Paksa semua latar belakang putih dan teks hitam */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        body { 
            background: white !important; 
            color: black !important; 
            margin: 0;
            padding: 0;
        }

        /* Hilangkan elemen navigasi */
        .sidebar, .topbar, .print\:hidden { 
            display: none !important; 
        }

        /* Reset layout utama agar memenuhi halaman */
        .flex-1, .ml-0, .md\:ml-64, main { 
            margin: 0 !important; 
            padding: 0 !important; 
            width: 100% !important;
            display: block !important;
        }

        /* Styling Kartu Laporan */
        .card { 
            background: white !important; 
            color: black !important; 
            border: none !important; 
            box-shadow: none !important; 
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Styling Tabel agar Bersih */
        table { 
            width: 100% !important; 
            border-collapse: collapse !important;
            border: 1px solid #000 !important;
        }
        
        th, td { 
            border: 1px solid #000 !important; 
            color: black !important; 
            padding: 6px !important; 
            font-size: 10pt !important;
        }

        th { 
            background-color: #f3f4f6 !important; /* Abu-abu terang untuk header */
            font-weight: bold !important;
        }

        /* Pastikan elemen teks berwarna (biru/hijau) di web menjadi hitam saat print */
        .text-sky-400, .text-emerald-400, .text-white, .text-slate-400, .text-amber-400 { 
            color: black !important; 
        }

        .bg-sky-500\/10, .bg-emerald-500\/10, .bg-indigo-500\/10 {
            background: none !important;
        }
    }
</style>
@push('scripts')
{{-- Library XLSX (SheetJS) dari CDN unpkg - Biasanya lebih stabil di berbagai jaringan --}}
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script>
    function exportToExcel() {
        // [STANDAR PERFORMA] Validasi ketersediaan library
        if (typeof XLSX === 'undefined') {
            alert('Gagal memuat sistem ekspor. Pastikan Anda terhubung ke internet untuk menggunakan fitur Ekspor Excel.');
            return;
        }

        const table = document.querySelector('table');
        const wb = XLSX.utils.book_new();
        
        // Data Header Laporan
        const wsData = [
            ["LAPORAN TRANSAKSI PARKIR"],
            ["Periode:", "{{ $start_date ?: 'Awal' }} s/d {{ $end_date ?: 'Akhir' }}"],
            ["Dicetak oleh:", "{{ auth()->user()->nama_lengkap }}"],
            ["Waktu Cetak:", new Date().toLocaleString('id-ID')],
            [], // Baris kosong
        ];

        // Ambil header tabel
        const headers = [];
        table.querySelectorAll('thead th').forEach(th => headers.push(th.innerText));
        wsData.push(headers);

        // Ambil data baris tabel
        table.querySelectorAll('tbody tr').forEach(tr => {
            const rowData = [];
            tr.querySelectorAll('td').forEach(td => rowData.push(td.innerText));
            if (rowData.length > 0 && rowData[0] !== "Tidak ada data transaksi pada periode ini.") {
                wsData.push(rowData);
            }
        });

        // Ambil total jika ada di tfoot
        const tfoot = table.querySelector('tfoot');
        if (tfoot) {
            tfoot.querySelectorAll('tr').forEach(tr => {
                const rowData = [];
                tr.querySelectorAll('td').forEach(td => rowData.push(td.innerText));
                wsData.push(rowData);
            });
        }

        const ws = XLSX.utils.aoa_to_sheet(wsData);

        // Styling Sederhana (Atur Lebar Kolom)
        ws['!cols'] = [
            { wch: 20 }, // Tiket
            { wch: 15 }, // Plat
            { wch: 15 }, // Area
            { wch: 25 }, // Masuk
            { wch: 25 }, // Keluar
            { wch: 10 }, // Durasi
            { wch: 20 }  // Biaya
        ];
        
        XLSX.utils.book_append_sheet(wb, ws, "Laporan Parkir");
        
        // Simpan File
        const dateStr = new Date().toISOString().slice(0,10);
        XLSX.writeFile(wb, "Laporan_Parkir_" + dateStr + ".xlsx");
    }
</script>
@endpush
@endsection
