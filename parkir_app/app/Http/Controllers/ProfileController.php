<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the profile page of the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }
}
