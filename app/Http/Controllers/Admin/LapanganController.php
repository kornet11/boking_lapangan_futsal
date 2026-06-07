<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LapanganController extends Controller
{
    public function index()
    {
        $lapangan = Lapangan::with('jadwalHarga')->latest()->get();
        return view('admin.lapangan.index', compact('lapangan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lapangan' => 'required|string|max:100',
            'jenis_lantai'  => 'required|string|max:50',
            'kapasitas'     => 'required|integer|min:1',
            'fasilitas'     => 'nullable|string',
            'foto'          => 'nullable|image|max:2048',
            'status'        => 'required|in:aktif,maintenance,nonaktif',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('lapangan', 'public');
        }

        Lapangan::create(array_merge(
            $request->only(['nama_lapangan', 'jenis_lantai', 'kapasitas', 'fasilitas', 'status']),
            ['foto' => $fotoPath]
        ));

        return back()->with('success', 'Lapangan berhasil ditambahkan.');
    }

    public function edit(Lapangan $lapangan)
    {
        return view('admin.lapangan.edit', compact('lapangan'));
    }

    public function update(Request $request, Lapangan $lapangan)
    {
        $request->validate([
            'nama_lapangan' => 'required|string|max:100',
            'jenis_lantai'  => 'required|string|max:50',
            'kapasitas'     => 'required|integer|min:1',
            'status'        => 'required|in:aktif,maintenance,nonaktif',
            'foto'          => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama_lapangan', 'jenis_lantai', 'kapasitas', 'fasilitas', 'status']);

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($lapangan->foto) Storage::disk('public')->delete($lapangan->foto);
            $data['foto'] = $request->file('foto')->store('lapangan', 'public');
        }

        $lapangan->update($data);

        return redirect()->route('admin.lapangan.index')->with('success', 'Lapangan berhasil diupdate.');
    }

    public function destroy(Lapangan $lapangan)
    {
        if ($lapangan->foto) Storage::disk('public')->delete($lapangan->foto);
        $lapangan->delete();
        return back()->with('success', 'Lapangan berhasil dihapus.');
    }
}
