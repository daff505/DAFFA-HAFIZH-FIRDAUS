<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:20',
            'password' => 'required|string|max:20',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->intended('/dashboard')->with('success', '✅ Login berhasil');
        }

        return back()->with('error', '❌ Login gagal')->withInput($request->except('password'));
    }

    public function register(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:20',
            'username' => 'required|string|max:20|unique:tb_user',
            'role' => 'required|in:admin,petugas,owner',
            'password' => 'required|string|min:6|max:20|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', '❌ Pendaftaran gagal');
        }

        try {
            User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'username' => $request->username,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'status_aktif' => 1,
            ]);

            return redirect('/login')->with('success', '✅ Pendaftaran berhasil');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', '❌ Pendaftaran gagal');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
