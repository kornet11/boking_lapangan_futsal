<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'no_telepon'            => 'nullable|string|max:20',
            'password'              => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'no_telepon' => $request->no_telepon,
            'password'   => Hash::make($request->password),
            'role'       => 'pelanggan', // register selalu jadi pelanggan
        ]);

        Auth::login($user);

        return redirect()->route('pelanggan.dashboard')
                         ->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }
}
