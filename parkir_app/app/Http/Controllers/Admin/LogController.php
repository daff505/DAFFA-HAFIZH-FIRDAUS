<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = DB::table('tb_log_aktivitas')
            ->join('tb_user', 'tb_log_aktivitas.id_user', '=', 'tb_user.id_user')
            ->select('tb_log_aktivitas.*', 'tb_user.nama_lengkap', 'tb_user.role');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('tb_user.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('tb_log_aktivitas.aktivitas', 'like', "%{$search}%")
                  ->orWhere('tb_log_aktivitas.detail', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('waktu_aktivitas', 'desc')->paginate(15);

        return view('admin.logs.index', compact('logs', 'search'));
    }
}
