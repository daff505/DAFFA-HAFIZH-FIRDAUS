<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Kendaraan::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('plat_nomor', 'like', "%{$search}%")
                  ->orWhere('pemilik', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        $kendaraans = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.kendaraan.index', compact('kendaraans', 'search'));
    }

    public function create()
    {
        $kendaraan = new Kendaraan();
        $users = User::where('status_aktif', 1)->orderBy('nama_lengkap')->get();
        // [STANDAR PERFORMA - B] Menggunakan Stored Procedure untuk ambil area
        $areas = collect(DB::select("CALL sp_get_area_tersedia()"));
            
        return view('admin.kendaraan.form', compact('kendaraan', 'users', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor'      => [
                'required',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('tb_kendaraan')->whereNull('deleted_at')
            ],
            'jenis_kendaraan' => 'required|in:motor,mobil,truk,lainnya',
            'warna'           => 'nullable|string|max:20',
            'merk'            => 'nullable|string|max:20',
            'pemilik'         => 'nullable|string|max:20',
            'id_user'         => 'nullable|exists:tb_user,id_user',
            'langsung_masuk'  => 'nullable|boolean',
            'id_area'         => 'nullable|exists:tb_area_parkir,id_area',
        ], [
            'plat_nomor.unique' => 'Plat nomor ini sudah terdaftar di sistem.',
        ]);

        $platNomor = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->plat_nomor));

        DB::beginTransaction();
        try {
           $id_kendaraan = DB::table('tb_kendaraan')->insertGetId([
                 'plat_nomor'      => $platNomor,
                'jenis_kendaraan' => $request->jenis_kendaraan,
                'warna'           => $request->warna,
                'merk'            => $request->merk,
                'pemilik'         => $request->pemilik,
                'id_user'         => $request->id_user ?: null,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now(),
            ]);

            // 2. Jika opsi "Langsung Masuk" dicentang
            if ($request->langsung_masuk) {
                // Tentukan Area: Gunakan input atau cari otomatis yang pertama tersedia
                $idArea = $request->id_area;
                if (!$idArea) {
                    $idArea = DB::table('tb_area_parkir')
                        ->where('status_area', 'aktif')
                        ->whereRaw('terisi < kapasitas')
                        ->value('id_area');
                    
                    if (!$idArea) {
                        throw new \Exception('Maaf, saat ini semua area parkir sudah penuh. Kendaraan gagal dimasukkan secara otomatis.');
                    }
                }

                // Cari Tarif
                $tarif = DB::table('tb_tarif')
                    ->where('jenis_kendaraan', $request->jenis_kendaraan)
                    ->first();

                if (!$tarif) {
                    throw new \Exception('Tarif untuk jenis kendaraan ini belum dikonfigurasi.');
                }

                // Cek Kapasitas Area yang terpilih/didapat
                $area = DB::table('tb_area_parkir')->where('id_area', $idArea)->first();
                if ($area->terisi >= $area->kapasitas) {
                    throw new \Exception("Area parkir {$area->nama_area} sudah penuh.");
                }

                $nomorTiket = 'ADM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

                $id_transaksi = DB::table('tb_transaksi')->insertGetId([
                    'nomor_tiket' => $nomorTiket,
                    'id_kendaraan' => $id_kendaraan,
                    'id_area' => $idArea,
                    'waktu_masuk' => Carbon::now(),
                    'id_tarif' => $tarif->id_tarif,
                    'status' => 'masuk',
                    'id_petugas_masuk' => auth()->id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Log Aktivitas Khusus (Otomasi Admin)
                DB::statement("CALL sp_log_aktivitas(?, ?, ?, ?, ?, ?)", [
                    auth()->id(),
                    'Pendaftaran & Masuk Otomatis',
                    'tb_transaksi',
                    $id_transaksi,
                    "Plat: $platNomor (Tiket: $nomorTiket) didaftarkan & masuk otomatis oleh Admin.",
                    $request->ip()
                ]);

                $message = 'Data kendaraan berhasil ditambahkan dan tercatat masuk parkir!';
            } else {
                // 11. Catat Log Aktivitas
                // [STANDAR PERFORMA - B] Menggunakan Stored Procedure
                // [STANDAR PERFORMA - C] Menggunakan parameter binding Array
                DB::statement("CALL sp_log_aktivitas(?, ?, ?, ?, ?, ?)", [
                    auth()->id(),
                    'Tambah Data Kendaraan',
                    'tb_kendaraan',
                    $id_kendaraan,
                    "Admin menambahkan data kendaraan baru: $platNomor",
                    $request->ip()
                ]);

                $message = 'Data kendaraan berhasil ditambahkan!';
            }

            DB::commit();
            return redirect()->route('admin.kendaraan.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal memproses data: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $users = User::where('status_aktif', 1)->orderBy('nama_lengkap')->get();
        return view('admin.kendaraan.form', compact('kendaraan', 'users'));
    }

    public function update(Request $request, string $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $request->validate([
            'plat_nomor'      => [
                'required',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('tb_kendaraan')->ignore($id, 'id_kendaraan')->whereNull('deleted_at')
            ],
            'jenis_kendaraan' => 'required|in:motor,mobil,truk,lainnya',
            'warna'           => 'nullable|string|max:20',
            'merk'            => 'nullable|string|max:20',
            'pemilik'         => 'nullable|string|max:20',
            'id_user'         => 'nullable|exists:tb_user,id_user',
        ], [
            'plat_nomor.unique' => 'Plat nomor ini sudah digunakan kendaraan lain.',
        ]);

        $kendaraan->update([
            'plat_nomor'      => strtoupper($request->plat_nomor),
            'jenis_kendaraan' => $request->jenis_kendaraan,
            'warna'           => $request->warna,
            'merk'            => $request->merk,
            'pemilik'         => $request->pemilik,
            'id_user'         => $request->id_user ?: null,
        ]);

        return redirect()->route('admin.kendaraan.index')->with('success', 'Data kendaraan berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        // Cek apakah kendaraan masih dalam transaksi aktif
        $transaksiAktif = \Illuminate\Support\Facades\DB::table('tb_transaksi')
            ->where('id_kendaraan', $id)
            ->where('status', 'masuk')
            ->count();

        if ($transaksiAktif > 0) {
            return back()->with('error', 'Kendaraan ini masih dalam area parkir (status masuk). Tidak bisa dihapus.');
        }

        $kendaraan->delete();
        return redirect()->route('admin.kendaraan.index')->with('success', 'Data kendaraan berhasil dihapus!');
    }
}
