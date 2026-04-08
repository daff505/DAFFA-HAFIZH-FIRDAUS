<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaParkir;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = AreaParkir::all();
        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        $area = new AreaParkir();
        return view('admin.areas.form', compact('area'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_area' => 'required|string|max:20|unique:tb_area_parkir,kode_area',
            'nama_area' => 'required|string|max:20',
            'lokasi' => 'nullable|string|max:20',
            'kapasitas' => 'required|integer|min:1',
            'status_area' => 'required|in:aktif,nonaktif,perbaikan',
        ]);

        AreaParkir::create([
            'kode_area' => $request->kode_area,
            'nama_area' => $request->nama_area,
            'lokasi' => $request->lokasi,
            'kapasitas' => $request->kapasitas,
            'terisi' => 0,
            'status_area' => $request->status_area,
        ]);

        return redirect()->route('admin.areas.index')->with('success', 'Area parkir berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $area = AreaParkir::findOrFail($id);
        return view('admin.areas.form', compact('area'));
    }

    public function update(Request $request, string $id)
    {
        $area = AreaParkir::findOrFail($id);

        $request->validate([
            'kode_area' => 'required|string|max:20|unique:tb_area_parkir,kode_area,'.$id.',id_area',
            'nama_area' => 'required|string|max:20',
            'lokasi' => 'nullable|string|max:20',
            'kapasitas' => 'required|integer|min:1',
            'status_area' => 'required|in:aktif,nonaktif,perbaikan',
        ]);

        $area->update([
            'kode_area' => $request->kode_area,
            'nama_area' => $request->nama_area,
            'lokasi' => $request->lokasi,
            'kapasitas' => $request->kapasitas,
            'status_area' => $request->status_area,
        ]);

        return redirect()->route('admin.areas.index')->with('success', 'Data area parkir berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $area = AreaParkir::findOrFail($id);

        // Cek apakah area masih ada kendaraan parkir
        if ($area->terisi > 0) {
            return back()->with('error', 'Area ini masih memiliki ' . $area->terisi . ' kendaraan parkir. Tidak bisa dihapus.');
        }

        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Area parkir berhasil dihapus!');
    }
}
