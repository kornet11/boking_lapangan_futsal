<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalHarga;
use App\Models\Lapangan;
use Illuminate\Http\Request;

class JadwalHargaController extends Controller
{
    public function index()
    {
        $lapangan     = Lapangan::with('jadwalHarga')->get();
        $jadwalHarga  = JadwalHarga::with('lapangan')->orderBy('lapangan_id')->get();
        return view('admin.jadwal.index', compact('lapangan', 'jadwalHarga'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lapangan_id'   => 'required|exists:lapangan,id',
            'tipe_hari'     => 'required|in:weekday,weekend',
            'jam_mulai'     => 'required|date_format:H:i',
            'jam_selesai'   => 'required|date_format:H:i|after:jam_mulai',
            'harga_per_jam' => 'required|numeric|min:0',
        ]);

        JadwalHarga::create($request->only([
            'lapangan_id', 'tipe_hari', 'jam_mulai', 'jam_selesai', 'harga_per_jam',
        ]));

        return back()->with('success', 'Tarif berhasil ditambahkan.');
    }

    public function update(Request $request, JadwalHarga $jadwal)
    {
        $request->validate([
            'harga_per_jam' => 'required|numeric|min:0',
        ]);

        $jadwal->update($request->only(['tipe_hari', 'jam_mulai', 'jam_selesai', 'harga_per_jam']));
        return back()->with('success', 'Tarif berhasil diupdate.');
    }

    public function destroy(JadwalHarga $jadwal)
    {
        $jadwal->delete();
        return back()->with('success', 'Tarif berhasil dihapus.');
    }
}
