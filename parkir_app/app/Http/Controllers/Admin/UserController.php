<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Parameter opsional untuk membedakan mode edit/tambah di blade
        $user = new User(); 
        return view('admin.users.form', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:20',
            'username' => 'required|string|max:20|unique:tb_user,username',
            'role' => 'required|in:admin,petugas,owner',
            'password' => 'required|string|min:6|max:20',
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'password_asli' => $request->password,
            'status_aktif' => 1
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.form', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:20',
            'username' => 'required|string|max:20|unique:tb_user,username,'.$id.',id_user',
            'role' => 'required|in:admin,petugas,owner',
            'password' => 'nullable|string|min:6|max:20',
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'role' => $request->role,
        ];

        // Jika password diisi, maka timpa/reset password lamanya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['password_asli'] = $request->password;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // 1. Proteksi: Jangan hapus diri sendiri
        if (auth()->id() == $user->id_user) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        // 2. Proteksi: Jangan hapus Super Admin (ID 1)
        if ($user->id_user == 1) {
            return back()->with('error', 'Akun Admin Utama (ID 1) tidak diperbolehkan untuk dihapus demi stabilitas sistem.');
        }

        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus!');
        } catch (\Exception $e) {
            // Jika gagal karena ada foreign key (data masih terikat di tabel lain)
            return back()->with('error', 'Gagal menghapus! Pengguna ini masih memiliki riwayat aktivitas (seperti membuat Tarif, Transaksi, atau Log) di dalam sistem.');
        }
    }
}
