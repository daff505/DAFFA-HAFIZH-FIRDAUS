<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::all();
        return view('admin.tarifs.index', compact('tarifs'));
    }

    public function create()
    {
        $tarif = new Tarif();
        return view('admin.tarifs.form', compact('tarif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_kendaraan' => 'required|in:motor,mobil,truk,lainnya',
            'tarif_per_jam' => 'required|numeric|min:0',
            'berlaku_mulai' => 'required|date',
            'berlaku_hingga' => 'nullable|date|after_or_equal:berlaku_mulai',
        ]);

        Tarif::create([
            'jenis_kendaraan' => $request->jenis_kendaraan,
            'tarif_per_jam' => $request->tarif_per_jam,
            'berlaku_mulai' => $request->berlaku_mulai,
            'berlaku_hingga' => $request->berlaku_hingga,
            'dibuat_oleh' => auth()->user()->id_user,
        ]);

        return redirect()->route('admin.tarifs.index')->with('success', 'Tarif baru berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $tarif = Tarif::findOrFail($id);
        return view('admin.tarifs.form', compact('tarif'));
    }

    public function update(Request $request, string $id)
    {
        $tarif = Tarif::findOrFail($id);

        $request->validate([
            'jenis_kendaraan' => 'required|in:motor,mobil,truk,lainnya',
            'tarif_per_jam' => 'required|numeric|min:0',
            'berlaku_mulai' => 'required|date',
            'berlaku_hingga' => 'nullable|date|after_or_equal:berlaku_mulai',
        ]);

        $tarif->update([
            'jenis_kendaraan' => $request->jenis_kendaraan,
            'tarif_per_jam' => $request->tarif_per_jam,
            'berlaku_mulai' => $request->berlaku_mulai,
            'berlaku_hingga' => $request->berlaku_hingga,
        ]);

        return redirect()->route('admin.tarifs.index')->with('success', 'Data tarif berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $tarif = Tarif::findOrFail($id);
        $tarif->delete();
        return redirect()->route('admin.tarifs.index')->with('success', 'Tarif berhasil dihapus!');
    }
}
