@extends('layouts.app')

@section('title', 'Kendaraan Keluar')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Proses Keluar & Pembayaran</h2>
            <p class="text-slate-400 text-sm mt-1">Validasi tiket dan hitung tagihan parkir</p>
        </div>
        <a href="{{ route('dashboard') }}"
            class="text-sm font-medium text-slate-400 hover:text-white bg-slate-800/50 hover:bg-slate-800 px-4 py-2 rounded-lg transition">
            &larr; Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Panel Kiri: Pencarian -->
        <div class="lg:col-span-4">
            <div class="card p-6 rounded-3xl border-t-4 border-t-emerald-500 relative overflow-hidden">
                <h3 class="text-lg font-bold text-white mb-4">Cari Kendaraan</h3>

                @if(session('error'))
                    <div
                        class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('transaksi.keluar') }}" method="GET">
                    <div class="space-y-4">
                        <div>
                            <label for="q" class="block text-xs font-medium text-slate-400 mb-1">No. Tiket / Plat</label>
                            <input type="text" name="q" id="q" required value="{{ request('q') }}"
                                class="w-full bg-slate-900/50 border border-slate-700/50 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition text-sm font-mono uppercase"
                                placeholder="E.g. TKT-..." autofocus>
                        </div>
                        <button type="submit"
                            class="w-full bg-slate-800 hover:bg-slate-700 text-emerald-400 text-sm font-semibold py-3 rounded-xl transition border border-slate-700 flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Panel Kanan: Hasil -->
        <div class="lg:col-span-8">
            @if($transaksi)
                <div class="card p-8 rounded-3xl relative overflow-hidden">
                    <h3 class="text-xl font-bold text-white mb-6 border-b border-slate-700/50 pb-4">Detail Validasi Kendaraan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-4">
                            <div>
                                <span class="text-xs text-slate-500 block">Nomor Tiket</span>
                                <span class="text-emerald-400 font-mono font-bold text-lg">{{ $transaksi->nomor_tiket }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><span class="text-xs text-slate-500 block">Plat</span><span
                                        class="text-white font-bold uppercase">{{ $transaksi->plat_nomor }}</span></div>
                                <div><span class="text-xs text-slate-500 block">Jenis</span><span
                                        class="text-white capitalize">{{ $transaksi->jenis_kendaraan }}</span></div>
                                <div><span class="text-xs text-slate-500 block">Masuk</span><span
                                        class="text-white text-sm">{{ date('H:i', strtotime($transaksi->waktu_masuk)) }}</span>
                                </div>
                                <div><span class="text-xs text-slate-500 block">Area</span><span
                                        class="text-white">{{ $transaksi->nama_area }}</span></div>
                            </div>
                        </div>

                        <div class="bg-slate-900/50 border border-slate-700/50 rounded-2xl p-5">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-slate-300">Durasi</span>
                                <span class="text-sm text-white">{{ $transaksi->estimasi_durasi }} Jam</span>
                            </div>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm text-slate-300">Tarif</span>
                                <span class="text-sm text-white">Rp
                                    {{ number_format($transaksi->tarif_per_jam, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-slate-700 border-dashed my-4"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-base font-bold text-white">Total</span>
                                <span class="text-2xl font-bold text-emerald-400">Rp
                                    {{ number_format($transaksi->estimasi_biaya, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('transaksi.prosesKeluar', $transaksi->id_parkir) }}" method="POST" id="form-keluar">
                        @csrf
                        <div class="bg-slate-800/30 p-5 rounded-2xl border border-slate-700/30 mb-8">
                            <label class="block text-sm font-medium text-slate-300 mb-2">Metode Pembayaran</label>
                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="metode_bayar" value="tunai" class="text-emerald-500 bg-slate-900"
                                        checked>
                                    <span class="text-sm text-slate-300 group-hover:text-white transition">Cash / Tunai</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="metode_bayar" value="qris" class="text-emerald-500 bg-slate-900">
                                    <span class="text-sm text-slate-300 group-hover:text-white transition">QRIS
                                        (Otomatis)</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" id="btn-submit-keluar"
                            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-4 rounded-xl transition shadow-lg shadow-emerald-500/20 text-lg flex justify-center items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span id="btn-text">Selesaikan Transaksi Keluar</span>
                        </button>
                    </form>

                    <!-- Snap JS SDK -->
                    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                        data-client-key="{{ config('midtrans.client_key') }}"></script>

                    <script>
                        document.getElementById('form-keluar').addEventListener('submit', function (e) {
                            const metode = document.querySelector('input[name="metode_bayar"]:checked').value;

                            if (metode === 'qris') {
                                e.preventDefault();
                                const btn = document.getElementById('btn-submit-keluar');
                                const btnText = document.getElementById('btn-text');

                                btn.disabled = true;
                                btnText.innerText = 'Menyiapkan Pembayaran...';

                                fetch("{{ route('midtrans.token', $transaksi->id_parkir) }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                    if (data.snap_token) {
                                        // Ganti teks tombol utama
                                        btnText.innerText = 'Menunggu Pembayaran...';
                                        btn.classList.add('opacity-50');

                                        // Tambahkan tombol sinkronisasi manual jika otomatis gagal (khusus localhost)
                                        if (!document.getElementById('manual-sync-btn')) {
                                            const syncBtn = document.createElement('div');
                                            syncBtn.id = 'manual-sync-btn';
                                            syncBtn.className = 'mt-4 p-4 bg-slate-800 rounded-xl border border-slate-700 text-center';
                                            syncBtn.innerHTML = `
                                                <p class="text-xs text-slate-400 mb-2 italic small">Sudah bayar di simulator tapi status belum berubah?</p>
                                                <button type="button" onclick="window.doManualSync()" class="text-emerald-400 font-bold text-sm underline hover:text-emerald-300 transition">
                                                    Klik di sini untuk Konfirmasi Manual
                                                </button>
                                            `;
                                            btn.parentNode.appendChild(syncBtn);
                                        }

                                        window.doManualSync = function() {
                                            fetch("{{ route('midtrans.finish', $transaksi->id_parkir) }}", {
                                                method: 'POST',
                                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                            }).then(() => {
                                                // [OTOMATISASI] Redirect ke halaman struk agar muncul dialog print/PDF
                                                window.location.href = "{{ route('transaksi.cetakStruk', $transaksi->id_parkir) }}";
                                            }).catch(err => alert('Gagal sinkronisasi data.'));
                                        };

                                        window.snap.pay(data.snap_token, {
                                            onSuccess: function(result) {
                                                window.doManualSync();
                                            },
                                            onPending: function(result) {
                                                alert('Pembayaran tertunda. Selesaikan di simulator!');
                                                location.reload();
                                            },
                                            onError: function(result) {
                                                alert('Terjadi kesalahan pembayaran.');
                                                location.reload();
                                            },
                                            onClose: function() {
                                                btn.disabled = false;
                                                btn.classList.remove('opacity-50');
                                                btnText.innerText = 'Selesaikan Transaksi Keluar';
                                            }
                                        });
                                    } else {
                                        alert(data.error || 'Gagal merequest token.');
                                        btn.disabled = false;
                                        btnText.innerText = 'Selesaikan Transaksi Keluar';
                                    }
                                })
                                .catch(error => {
                                    alert('Kesalahan jaringan ke Midtrans.');
                                    btn.disabled = false;
                                    btnText.innerText = 'Selesaikan Transaksi Keluar';
                                });
                            }
                        });
                    </script>
                </div>
            @else
                <div class="card p-10 rounded-3xl flex flex-col items-center justify-center text-center h-full opacity-50">
                    <h3 class="text-lg font-bold text-white mb-2">Validasi Keluar</h3>
                    <p class="text-slate-400 text-xs">Cari tiket untuk memproses tagihan.</p>
                </div>
            @endif
        </div>
    </div>
@endsection