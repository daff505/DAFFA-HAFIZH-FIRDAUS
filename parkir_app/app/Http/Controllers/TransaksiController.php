<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // --- KENDARAAN MASUK ---
    public function masuk()
    {
        // [STANDAR PERFORMA - B] Menggunakan Stored Procedure
        $areas = collect(DB::select("CALL sp_get_area_tersedia()"));
            
        // [STANDAR PERFORMA - E] Menggunakan LIMIT pada data besar
        $owners = DB::table('tb_user')->where('role', 'owner')->where('status_aktif', 1)->limit(100)->get();

        return view('transaksi.masuk', compact('areas', 'owners'));
    }

    public function storeMasuk(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required|string|max:20',
            'jenis_kendaraan' => 'required|in:motor,mobil,truk,lainnya',
            'id_area' => 'required|exists:tb_area_parkir,id_area',
            'merk' => 'nullable|string|max:20',
            'warna' => 'nullable|string|max:20',
            'pemilik' => 'nullable|string|max:20',
            'id_user' => 'nullable|exists:tb_user,id_user',
        ]);

        $platNomor = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->plat_nomor));

        DB::beginTransaction();
        try {
            // 1. Cek atau Buat Data Kendaraan
            $kendaraan = DB::table('tb_kendaraan')->where('plat_nomor', $platNomor)->first();
            
            $vehicleData = [
                'plat_nomor' => $platNomor,
                'jenis_kendaraan' => $request->jenis_kendaraan,
                'merk' => $request->merk,
                'warna' => $request->warna,
                'pemilik' => $request->pemilik,
                'id_user' => $request->id_user,
                'updated_at' => Carbon::now(),
            ];

            if (!$kendaraan) {
                $vehicleData['created_at'] = Carbon::now();
                $id_kendaraan = DB::table('tb_kendaraan')->insertGetId($vehicleData);
            } else {
                $id_kendaraan = $kendaraan->id_kendaraan;
                
                // Jika data yang dikirim kosong (id_user/pemilik), gunakan yang lama agar tidak terhapus
                if (empty($vehicleData['id_user'])) $vehicleData['id_user'] = $kendaraan->id_user;
                if (empty($vehicleData['pemilik'])) $vehicleData['pemilik'] = $kendaraan->pemilik;
                if (empty($vehicleData['merk'])) $vehicleData['merk'] = $kendaraan->merk;
                if (empty($vehicleData['warna'])) $vehicleData['warna'] = $kendaraan->warna;

                DB::table('tb_kendaraan')
                    ->where('id_kendaraan', $id_kendaraan)
                    ->update($vehicleData);
            }

            // 2. Tentukan Tarif berdasarkan jenis_kendaraan
            $tarif = DB::table('tb_tarif')
                ->where('jenis_kendaraan', $request->jenis_kendaraan)
                ->first();

            if (!$tarif) {
                return back()->with('error', 'Tarif untuk jenis kendaraan ini belum dikonfigurasi.');
            }

            // 3. Generate Nomor Tiket
            $nomorTiket = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            // 4. Catat Transaksi Masuk
            $id_transaksi = DB::table('tb_transaksi')->insertGetId([
                'nomor_tiket' => $nomorTiket,
                'id_kendaraan' => $id_kendaraan,
                'id_area' => $request->id_area,
                'waktu_masuk' => Carbon::now(),
                'id_tarif' => $tarif->id_tarif,
                'status' => 'masuk',
                'id_petugas_masuk' => auth()->id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Trigger "after_transaksi_masuk" akan meng-handle penambahan "terisi" di tabel area.

            // 5. Catat Log Aktivitas
            // [STANDAR PERFORMA - B] Menggunakan Stored Procedure
            // [STANDAR PERFORMA - C] Menggunakan parameter binding Array
            DB::statement("CALL sp_log_aktivitas(?, ?, ?, ?, ?, ?)", [
                auth()->id(),
                'Kendaraan Masuk',
                'tb_transaksi',
                $id_transaksi,
                "Plat: $platNomor, Tiket: $nomorTiket",
                $request->ip()
            ]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', "Kendaraan berhasil masuk. Nomor Tiket: $nomorTiket");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // --- KENDARAAN KELUAR ---
    public function keluar(Request $request)
    {
        $query = $request->input('q');
        $transaksi = null;
        $kendaraan = null;

        if ($query) {
            $transaksi = DB::table('tb_transaksi')
                ->join('tb_kendaraan', 'tb_transaksi.id_kendaraan', '=', 'tb_kendaraan.id_kendaraan')
                ->join('tb_area_parkir', 'tb_transaksi.id_area', '=', 'tb_area_parkir.id_area')
                ->join('tb_tarif', 'tb_transaksi.id_tarif', '=', 'tb_tarif.id_tarif')
                ->select(
                    'tb_transaksi.*', 
                    'tb_kendaraan.plat_nomor', 
                    'tb_kendaraan.jenis_kendaraan', 
                    'tb_area_parkir.nama_area', 
                    'tb_tarif.tarif_per_jam'
                )
                ->where('tb_transaksi.status', 'masuk')
                ->where(function($q) use ($query) {
                    $q->where('tb_transaksi.nomor_tiket', 'like', "%$query%")
                      ->orWhere('tb_kendaraan.plat_nomor', 'like', "%$query%");
                })
                ->first();
                
            if ($transaksi) {
                // Pre-Kalkulasi Estimasi Waktu (Hanya Tampilan, Belum Masuk DB)
                $masuk = Carbon::parse($transaksi->waktu_masuk);
                $sekarang = Carbon::now();
                $diffInMinutes = $masuk->diffInMinutes($sekarang);
                $durasi_jam = ceil($diffInMinutes / 60);
                if ($durasi_jam == 0) $durasi_jam = 1;
                
                $transaksi->estimasi_durasi = $durasi_jam;
                $transaksi->estimasi_biaya = $durasi_jam * $transaksi->tarif_per_jam;
            }
        }

        return view('transaksi.keluar', compact('transaksi', 'query'));
    }

    public function prosesKeluar(Request $request, $id_parkir)
    {
        $request->validate([
            'metode_bayar' => 'required|in:tunai,qris,debit,kredit',
        ]);

        DB::beginTransaction();
        try {
            $transaksi = DB::table('tb_transaksi')
                ->join('tb_tarif', 'tb_transaksi.id_tarif', '=', 'tb_tarif.id_tarif')
                ->join('tb_kendaraan', 'tb_transaksi.id_kendaraan', '=', 'tb_kendaraan.id_kendaraan')
                ->select('tb_transaksi.*', 'tb_tarif.tarif_per_jam', 'tb_kendaraan.plat_nomor')
                ->where('tb_transaksi.id_parkir', $id_parkir)
                ->where('tb_transaksi.status', 'masuk')
                ->first();

            if (!$transaksi) {
                return back()->with('error', 'Transaksi tidak valid atau kendaraan sudah keluar.');
            }

            // 1. Hitung Waktu dan Durasi Actual
            // --- LOGIKA HITUNG BIAYA & DENDA DINAMIS ---
            $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
            $waktuKeluar = Carbon::now();
            $durasiMenit = $waktuMasuk->diffInMinutes($waktuKeluar);
            $durasiJam = ceil($durasiMenit / 60);
            if ($durasiJam <= 0) $durasiJam = 1;

            // Ambil data Tarif per jam
            $tarifPerJam = $transaksi->tarif_per_jam;

            // Ambil Pengaturan Denda & Tenggat dari Database
            $settings = DB::table('tb_pengaturan')->pluck('nilai', 'kunci')->toArray();
            $waktuTenggat = (int)($settings['waktu_tenggat'] ?? 24);
            $dendaPerJam = (int)($settings['denda_per_jam'] ?? 0);

            // Hitung Biaya Normal
            $biayaNormal = $durasiJam * $tarifPerJam;
            $biayaDenda = 0;

            // Hitung Denda jika durasi melebihi waktu tenggat
            if ($durasiJam > $waktuTenggat) {
                $jamTerlambat = $durasiJam - $waktuTenggat;
                $biayaDenda = $jamTerlambat * $dendaPerJam;
            }

            $totalBayar = $biayaNormal + $biayaDenda;

            // 2. Update tb_transaksi
            DB::table('tb_transaksi')->where('id_parkir', $id_parkir)->update([
                'waktu_keluar' => $waktuKeluar,
                'durasi_jam' => $durasiJam,
                'biaya_total' => $totalBayar,
                'status' => 'keluar',
                'id_petugas_keluar' => auth()->id(),
                'metode_bayar' => $request->metode_bayar,
                'updated_at' => Carbon::now(),
            ]);

            // 3. Generate Struk (Mengambil Header Dinamis dari Database)
            $nomorStruk = 'STR-' . date('Ymd') . '-' . str_pad($id_parkir, 5, '0', STR_PAD_LEFT);
            $headerStruk = $settings['struk_header'] ?? "SISTEM PARKIR DIGITAL\nJl. Parkir No. 123";

            $kontenStruk = "===== " . strtoupper($settings['nama_aplikasi'] ?? 'PARKIR') . " =====\n";
            $kontenStruk .= $headerStruk . "\n";
            $kontenStruk .= "---------------------------\n";
            $kontenStruk .= "No. Struk  : $nomorStruk\n";
            $kontenStruk .= "No. Tiket  : {$transaksi->nomor_tiket}\n";
            $kontenStruk .= "Plat Nomor : {$transaksi->plat_nomor}\n";
            $kontenStruk .= "---------------------------\n";
            $kontenStruk .= "Masuk      : " . $waktuMasuk->format('d/m/Y H:i') . "\n";
            $kontenStruk .= "Keluar     : " . $waktuKeluar->format('d/m/Y H:i') . "\n";
            $kontenStruk .= "Durasi     : $durasiJam Jam\n";
            $kontenStruk .= "Biaya Normal: Rp " . number_format($biayaNormal, 0, ',', '.') . "\n";
            if ($biayaDenda > 0) {
                $kontenStruk .= "Denda ($jamTerlambat j): Rp " . number_format($biayaDenda, 0, ',', '.') . "\n";
            }
            $kontenStruk .= "---------------------------\n";
            $kontenStruk .= "TOTAL BAYAR : Rp " . number_format($totalBayar, 0, ',', '.') . "\n";
            $kontenStruk .= "Metode     : " . ucfirst($request->metode_bayar) . "\n";
            $kontenStruk .= "---------------------------\n";
            $kontenStruk .= "STATUS     : LUNAS\n";
            $kontenStruk .= "---------------------------\n";
            $kontenStruk .= "Terima kasih atas kunjungan Anda.\n";

            DB::table('tb_struk')->insert([
                'id_transaksi' => $id_parkir,
                'nomor_struk' => $nomorStruk,
                'konten_struk' => $kontenStruk,
                'dicetak_pada' => Carbon::now(),
                'dicetak_oleh' => auth()->id(),
                'created_at' => Carbon::now()
            ]);

            // 4. Catat di Log
            DB::statement("CALL sp_log_aktivitas(?, ?, ?, ?, ?, ?)", [
                auth()->id(),
                'Kendaraan Keluar (Bayar & Cetak)',
                'tb_transaksi',
                $id_parkir,
                "No. Tiket: {$transaksi->nomor_tiket}, Struk: $nomorStruk, Total: $totalBayar (Denda: $biayaDenda)",
                $request->ip()
            ]);

            DB::commit();

            return redirect()->route('transaksi.cetakStruk', $id_parkir)->with('success', "Pembayaran Berhasil. Menyiapkan struk...");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem saat pemrosesan: ' . $e->getMessage());
        }
    }

    public function cetakStruk($id_transaksi)
    {
        $struk = DB::table('tb_struk')
            ->join('tb_transaksi', 'tb_struk.id_transaksi', '=', 'tb_transaksi.id_parkir')
            ->join('tb_kendaraan', 'tb_transaksi.id_kendaraan', '=', 'tb_kendaraan.id_kendaraan')
            ->join('tb_tarif', 'tb_transaksi.id_tarif', '=', 'tb_tarif.id_tarif')
            ->join('tb_user', 'tb_struk.dicetak_oleh', '=', 'tb_user.id_user')
            ->select(
                'tb_struk.*', 
                'tb_transaksi.nomor_tiket', 
                'tb_transaksi.waktu_masuk', 
                'tb_transaksi.waktu_keluar', 
                'tb_transaksi.durasi_jam', 
                'tb_transaksi.biaya_total', 
                'tb_transaksi.metode_bayar',
                'tb_kendaraan.plat_nomor', 
                'tb_tarif.tarif_per_jam',
                'tb_user.nama_lengkap as nama_petugas'
            )
            ->where('tb_struk.id_transaksi', $id_transaksi)
            ->first();

        if (!$struk) {
            abort(404, 'Data struk tidak ditemukan.');
        }

        // Ambil pengaturan denda untuk perhitungan tampilan
        $settings = DB::table('tb_pengaturan')->pluck('nilai', 'kunci')->toArray();
        $struk->waktu_tenggat = (int)($settings['waktu_tenggat'] ?? 24);
        $struk->denda_per_jam = (int)($settings['denda_per_jam'] ?? 0);
        
        // Hitung ulang rincian biaya untuk tampilan struk
        $struk->biaya_normal = $struk->durasi_jam * $struk->tarif_per_jam;
        $struk->biaya_denda = 0;
        $struk->jam_terlambat = 0;

        if ($struk->durasi_jam > $struk->waktu_tenggat) {
            $struk->jam_terlambat = $struk->durasi_jam - $struk->waktu_tenggat;
            $struk->biaya_denda = $struk->jam_terlambat * $struk->denda_per_jam;
        }

        return view('transaksi.struk', compact('struk'));
    }
}
