<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Parkir - #{{ $struk->nomor_struk }}</title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 75mm; 
            margin: 0 auto; 
            padding: 10px; 
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: #f4f4f4;
        }
        .paper {
            background: #fff;
            padding: 20px 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .flex { display: flex; justify-content: space-between; gap: 5px; }
        .content { margin: 10px 0; }
        .footer { margin-top: 25px; font-size: 9px; border-top: 1px solid #eee; padding-top: 10px; }
        
        .btn-group {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 100;
        }
        .btn { 
            display: inline-block; 
            padding: 10px 20px; 
            background: #0ea5e9; 
            color: #fff; 
            text-align: center; 
            text-decoration: none; 
            font-family: sans-serif;
            font-weight: bold;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }
        .btn-success { background: #10b981; }
        .btn-danger { background: #ef4444; }
        
        @media print {
            .btn-group { display: none; }
            body { background: transparent; padding: 0; margin: 0; width: 100%; }
            .paper { box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    
    <div class="btn-group no-print">
        <a href="{{ route('dashboard') }}" class="btn" style="background: #64748b;">← Dashboard</a>
        <button onclick="window.print()" class="btn btn-success">Cetak Struk (PDF)</button>
    </div>

    <div class="paper">
        <div class="header text-center">
            @php
                $appName = $struk->nama_aplikasi ?? ($sys_settings['nama_aplikasi'] ?? "PARKIR DIGITAL PRO");
                $header = $sys_settings['struk_header'] ?? "Electronic Parking System";
                $lines = explode("\n", $header);
            @endphp
            <h1 class="bold">{{ $appName }}</h1>
            @foreach($lines as $line)
                <p>{{ $line }}</p>
            @endforeach
            <p style="font-size: 8px; margin-top: 5px;">NPWP: 01.234.567.8-910.111</p>
        </div>

        <div class="content">
            <div class="flex">
                <span class="bold">NOMOR STRUK:</span>
                <span class="bold text-right">{{ $struk->nomor_struk }}</span>
            </div>
            <div class="flex">
                <span>Tanggal:</span>
                <span class="text-right">{{ date('d/m/Y H:i:s', strtotime($struk->created_at)) }}</span>
            </div>
            <div class="flex">
                <span>Plat Nomor:</span>
                <span class="bold text-right">{{ strtoupper($struk->plat_nomor) }}</span>
            </div>
            <div class="flex">
                <span>No. Tiket:</span>
                <span class="text-right">{{ $struk->nomor_tiket }}</span>
            </div>

            <div class="divider"></div>

            <div class="flex">
                <span>Waktu Masuk:</span>
                <span class="text-right">{{ date('d/m/y H:i', strtotime($struk->waktu_masuk)) }}</span>
            </div>
            <div class="flex">
                <span>Waktu Keluar:</span>
                <span class="text-right">{{ date('d/m/y H:i', strtotime($struk->waktu_keluar)) }}</span>
            </div>
            <div class="flex">
                <span>Durasi:</span>
                <span class="text-right">{{ $struk->durasi_jam }} JAM</span>
            </div>

            <div class="divider"></div>

            <!-- Rincian Biaya -->
            <div class="flex">
                <span>Biaya Parkir ({{ $struk->durasi_jam }} x {{ number_format($struk->tarif_per_jam ?? 0, 0, ',', '.') }}):</span>
                <span class="text-right">Rp {{ number_format($struk->biaya_normal ?? ($struk->biaya_total), 0, ',', '.') }}</span>
            </div>
            
            @if(isset($struk->biaya_denda) && $struk->biaya_denda > 0)
            <div class="flex">
                <span>Denda ({{ $struk->jam_terlambat }} j x {{ number_format($struk->denda_per_jam, 0, ',', '.') }}):</span>
                <span class="text-right">Rp {{ number_format($struk->biaya_denda, 0, ',', '.') }}</span>
            </div>
            <p style="font-size: 8px; color: #666; margin: 2px 0;">*Tenggat parkir max {{ $struk->waktu_tenggat }} jam</p>
            @endif

            <div class="divider"></div>

            <div class="flex" style="font-size: 14px;">
                <span class="bold">TOTAL BAYAR:</span>
                <span class="bold text-right" style="min-width: 100px;">Rp {{ number_format($struk->biaya_total, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex" style="margin-top: 5px;">
                <span>Metode Bayar:</span>
                <span class="bold text-right uppercase">{{ $struk->metode_bayar }}</span>
            </div>
            <div class="flex">
                <span>Status:</span>
                <span class="bold text-right">LUNAS</span>
            </div>

            <div class="divider"></div>
            
            <div class="text-center bold" style="font-size: 12px; margin-top: 10px;">
                *** TRANSAKSI BERHASIL ***
            </div>
        </div>

        <div class="footer text-center">
            <p class="bold">TERIMA KASIH</p>
            <p>Atas Kunjungan Anda di Terminal Parkir</p>
            <p>SMS/WA Pengaduan: 0812-3456-7890</p>
            <div class="divider"></div>
            <div class="flex" style="font-size: 8px;">
                <span>Kasir: {{ $struk->nama_petugas }}</span>
                <span>ID: {{ $struk->id_transaksi ?? $struk->nomor_struk }}</span>
            </div>
            <p style="font-size: 7px; margin-top: 10px; color: #666;">Dicetak otomatis oleh Sistem Parkir Digital v2.0</p>
        </div>
    </div>

    <script>
        // Auto print trigger if needed, or just let the button handle it
        // window.print();
    </script>
</body>
</html>
