<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $settings = Pengaturan::all();
        return view('admin.pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->settings as $id => $nilai) {
            Pengaturan::where('id_pengaturan', $id)->update([
                'nilai' => $nilai,
                'updated_at' => now()
            ]);
        }

        return redirect()->route('admin.pengaturan.index')->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }
}
