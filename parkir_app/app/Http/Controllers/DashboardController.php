<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->check() ? auth()->user()->role : null;
        $data = [];

        if ($role === 'admin') {
            $data['total_area'] = DB::table('tb_area_parkir')->count();
            $data['total_pengguna'] = DB::table('tb_user')->count();
            $data['total_kendaraan'] = DB::table('tb_kendaraan')->count();
            $data['total_transaksi'] = DB::table('tb_transaksi')->count();
            
            $data['log_aktivitas'] = DB::table('tb_log_aktivitas')
                ->join('tb_user', 'tb_log_aktivitas.id_user', '=', 'tb_user.id_user')
                ->select('tb_log_aktivitas.*', 'tb_user.nama_lengkap')
                ->orderBy('waktu_aktivitas', 'desc')
                ->limit(5)
                ->get();
                
            // Generate data untuk Grafik 7 Hari Terakhir untuk Admin/Bos
            $chartData = ['labels' => [], 'data' => []];
            for ($i = 6; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
                $dailyTotal = DB::table('tb_transaksi')
                    ->where('status', 'keluar')
                    ->whereDate('waktu_keluar', $date)
                    ->sum('biaya_total');
                $chartData['labels'][] = \Carbon\Carbon::now()->subDays($i)->format('d M');
                $chartData['data'][] = (int) $dailyTotal;
            }
            $data['chart'] = $chartData;
                
        } elseif ($role === 'petugas') {
            $data['areas'] = DB::table('tb_area_parkir')->get();
            
        } elseif ($role === 'owner') {
            $data['pendapatan'] = DB::table('tb_transaksi')
                ->where('status', 'keluar')
                ->whereMonth('waktu_keluar', date('m'))
                ->sum('biaya_total');
                
            $data['total_volume'] = DB::table('tb_transaksi')
                ->whereMonth('waktu_masuk', date('m'))
                ->count();
                
            $data['kapasitas_terisi'] = (int) DB::table('tb_area_parkir')->sum('terisi');
            $data['kapasitas_total'] = (int) DB::table('tb_area_parkir')->sum('kapasitas');
            
            $data['transaksi_terbaru'] = DB::table('tb_transaksi')
                ->join('tb_kendaraan', 'tb_transaksi.id_kendaraan', '=', 'tb_kendaraan.id_kendaraan')
                ->join('tb_area_parkir', 'tb_transaksi.id_area', '=', 'tb_area_parkir.id_area')
                ->select('tb_transaksi.*', 'tb_kendaraan.plat_nomor', 'tb_kendaraan.jenis_kendaraan', 'tb_area_parkir.nama_area')
                ->orderBy('tb_transaksi.waktu_keluar', 'desc')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact('data'));
    }
}
