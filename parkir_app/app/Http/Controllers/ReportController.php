<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $status = $request->input('status');

        $query = DB::table('v_rekap_transaksi');

        if ($start_date) {
            $query->whereDate('waktu_masuk', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('waktu_masuk', '<=', $end_date);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->orderBy('waktu_masuk', 'desc')->get();

        // Calculate summary
        $total_income = $reports->sum('biaya_total');
        $total_vehicles = $reports->count();
        $completed_transactions = $reports->where('status', 'keluar')->count();

        return view('reports.index', compact(
            'reports', 
            'start_date', 
            'end_date', 
            'status', 
            'total_income', 
            'total_vehicles', 
            'completed_transactions'
        ));
    }
}
