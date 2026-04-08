<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MidtransController extends Controller
{
    /**
     * Get Snap Token using Midtrans Snap API
     */
    public function getToken(Request $request, $id_parkir)
    {
        $transaksi = DB::table('tb_transaksi')
            ->join('tb_tarif', 'tb_transaksi.id_tarif', '=', 'tb_tarif.id_tarif')
            ->join('tb_kendaraan', 'tb_transaksi.id_kendaraan', '=', 'tb_kendaraan.id_kendaraan')
            ->select('tb_transaksi.*', 'tb_tarif.tarif_per_jam', 'tb_kendaraan.plat_nomor', 'tb_kendaraan.jenis_kendaraan')
            ->where('tb_transaksi.id_parkir', $id_parkir)
            ->where('tb_transaksi.status', 'masuk')
            ->first();

        if (!$transaksi) {
            return response()->json(['error' => 'Transaksi tidak ditemukan atau sudah selesai.'], 404);
        }

        // Hitung durasi dan biaya aktual
        $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
        $waktuKeluar = Carbon::now();
        $durasiMenit = $waktuMasuk->diffInMinutes($waktuKeluar);
        $durasiJam = ceil($durasiMenit / 60);
        if ($durasiJam <= 0) $durasiJam = 1;

        $settings = DB::table('tb_pengaturan')->pluck('nilai', 'kunci')->toArray();
        $waktuTenggat = (int)($settings['waktu_tenggat'] ?? 24);
        $dendaPerJam = (int)($settings['denda_per_jam'] ?? 0);

        $biayaNormal = $durasiJam * $transaksi->tarif_per_jam;
        $biayaDenda = ($durasiJam > $waktuTenggat) ? ($durasiJam - $waktuTenggat) * $dendaPerJam : 0;
        $totalBayar = $biayaNormal + $biayaDenda;

        // Snap API Transaction Payload
        $payload = [
            'transaction_details' => [
                'order_id' => 'PARK-' . $id_parkir . '-' . time(),
                'gross_amount' => (int)$totalBayar,
            ],
            'customer_details' => [
                'first_name' => 'Pelanggan Parkir',
                'last_name' => $transaksi->plat_nomor,
                'email' => 'diandianugrahani@gmail.com',
                'phone' => '+6285716499546',
            ],
            'item_details' => [
                [
                    'id' => 'PARK-' . $id_parkir,
                    'price' => (int)$totalBayar,
                    'quantity' => 1,
                    'name' => 'Bayar Parkir ' . $transaksi->plat_nomor,
                ]
            ],
        ];

        try {
            $response = $this->requestSnapToken($payload);
            
            return response()->json([
                'snap_token' => $response['token'],
                'redirect_url' => $response['redirect_url'],
                'order_id' => $payload['transaction_details']['order_id']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper to call Midtrans Snap API using CURL
     */
    private function requestSnapToken($payload)
    {
        $url = config('midtrans.is_production') 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $serverKey = config('midtrans.server_key');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($serverKey . ':')
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $data = json_decode($response, true);
        
        if ($httpCode !== 201 && $httpCode !== 200) {
            throw new \Exception($data['error_messages'][0] ?? 'Gagal menghubungi API Snap Midtrans.');
        }

        return $data;
    }

    public function finishTransaction(Request $request, $id_parkir)
    {
        Log::info("Midtrans Sync: Memproses pelunasan manual untuk ID Parkir $id_parkir");
        // Di lingkungan lokal, kita panggil finalize secara manual dari frontend success handler
        $this->finalizeTransaction($id_parkir, 'qris');
        
        return response()->json(['message' => 'Transaksi berhasil diperbarui.']);
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $parts = explode('-', $orderId);
        $id_parkir = $parts[1] ?? null;

        if ($id_parkir && ($transactionStatus == 'capture' || $transactionStatus == 'settlement')) {
            $this->finalizeTransaction($id_parkir, 'qris');
        }

        return response()->json(['message' => 'OK']);
    }

    private function finalizeTransaction($id_parkir, $metode)
    {
        DB::beginTransaction();
        try {
            $transaksi = DB::table('tb_transaksi')
                ->join('tb_tarif', 'tb_transaksi.id_tarif', '=', 'tb_tarif.id_tarif')
                ->join('tb_kendaraan', 'tb_transaksi.id_kendaraan', '=', 'tb_kendaraan.id_kendaraan')
                ->select('tb_transaksi.*', 'tb_tarif.tarif_per_jam', 'tb_kendaraan.plat_nomor')
                ->where('tb_transaksi.id_parkir', $id_parkir)
                ->where('tb_transaksi.status', 'masuk')
                ->first();

            if (!$transaksi) return;

            $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
            $waktuKeluar = Carbon::now();
            $durasiMenit = $waktuMasuk->diffInMinutes($waktuKeluar);
            $durasiJam = ceil($durasiMenit / 60);
            if ($durasiJam <= 0) $durasiJam = 1;

            $settings = DB::table('tb_pengaturan')->pluck('nilai', 'kunci')->toArray();
            $biayaNormal = $durasiJam * $transaksi->tarif_per_jam;
            $waktuTenggat = (int)($settings['waktu_tenggat'] ?? 24);
            $dendaPerJam = (int)($settings['denda_per_jam'] ?? 0);
            $biayaDenda = ($durasiJam > $waktuTenggat) ? ($durasiJam - $waktuTenggat) * $dendaPerJam : 0;
            $totalBayar = $biayaNormal + $biayaDenda;

            // [PERBAIKAN 404] Tambahkan petugas agar join di cetakStruk tidak gagal
            DB::table('tb_transaksi')->where('id_parkir', $id_parkir)->update([
                'waktu_keluar' => $waktuKeluar,
                'durasi_jam' => $durasiJam,
                'biaya_total' => $totalBayar,
                'status' => 'keluar',
                'metode_bayar' => $metode,
                'id_petugas_keluar' => auth()->id(), // Set petugas keluar
                'updated_at' => Carbon::now(),
            ]);

            $nomorStruk = 'STR-' . date('Ymd') . '-' . str_pad($id_parkir, 5, '0', STR_PAD_LEFT);
            $headerStruk = $settings['struk_header'] ?? "SISTEM PARKIR DIGITAL\nJl. Parkir No. 123";
            $kontenStruk = "===== " . strtoupper($settings['nama_aplikasi'] ?? 'PARKIR') . " =====\n";
            $kontenStruk .= $headerStruk . "\n" . str_repeat("-", 27) . "\n";
            $kontenStruk .= "No. Struk  : $nomorStruk\nNo. Tiket  : {$transaksi->nomor_tiket}\n";
            $kontenStruk .= "Plat Nomor : {$transaksi->plat_nomor}\n" . str_repeat("-", 27) . "\n";
            $kontenStruk .= "Masuk      : " . $waktuMasuk->format('d/m/Y H:i') . "\n";
            $kontenStruk .= "Keluar     : " . $waktuKeluar->format('d/m/Y H:i') . "\n";
            $kontenStruk .= "Durasi     : $durasiJam Jam\nTOTAL BAYAR : Rp " . number_format($totalBayar, 0, ',', '.') . "\n";
            $kontenStruk .= "Metode     : QRIS (Midtrans)\n" . str_repeat("-", 27) . "\nSTATUS     : LUNAS\n";
            $kontenStruk .= str_repeat("-", 27) . "\nTerima kasih atas kunjungan Anda.\n";

            // Cek apakah struk sudah ada untuk menghindari duplikasi
            $exists = DB::table('tb_struk')->where('id_transaksi', $id_parkir)->exists();
            if (!$exists) {
                DB::table('tb_struk')->insert([
                    'id_transaksi' => $id_parkir,
                    'nomor_struk' => $nomorStruk,
                    'konten_struk' => $kontenStruk,
                    'dicetak_pada' => Carbon::now(),
                    'dicetak_oleh' => auth()->id(), // Set petugas pencetak
                    'created_at' => Carbon::now()
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Midtrans Callback Error: ' . $e->getMessage());
        }
    }
}
