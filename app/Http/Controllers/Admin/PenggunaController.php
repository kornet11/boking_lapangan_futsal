<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->role, fn($q) => $q->where('role', $request->role))
                     ->latest()->paginate(20);
        return view('admin.pengguna.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'no_telepon' => 'nullable|string|max:20',
            'role'       => 'required|in:admin,operator,kasir,pelanggan',
            'password'   => 'required|min:8',
        ]);

        User::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'no_telepon' => $request->no_telepon,
            'role'       => $request->role,
            'password'   => Hash::make($request->password),
        ]);

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $pengguna)
    {
        return view('admin.pengguna.edit', compact('pengguna'));
    }

    public function update(Request $request, User $pengguna)
    {
        $request->validate([
            'nama'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $pengguna->id,
            'no_telepon' => 'nullable|string|max:20',
            'role'       => 'required|in:admin,operator,kasir,pelanggan',
        ]);

        $pengguna->update($request->only(['nama', 'email', 'no_telepon', 'role']));

        // Update password kalau diisi
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $pengguna->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil diupdate.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $pengguna->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
