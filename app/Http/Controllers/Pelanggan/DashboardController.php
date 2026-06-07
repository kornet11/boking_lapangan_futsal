<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Lapangan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $bookingAktif = Booking::where('pelanggan_id', $userId)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->with(['lapangan', 'pembayaran'])
            ->latest()
            ->take(5)
            ->get();

        $lapangan = Lapangan::where('status', 'aktif')
            ->with('jadwalHarga')
            ->get();

        return view('pelanggan.dashboard', [
            'bookingAktif' => $bookingAktif,
            'lapangan'     => $lapangan,
            'menunggu'     => Booking::where('pelanggan_id', $userId)->where('status', 'menunggu')->count(),
            'dikonfirmasi' => Booking::where('pelanggan_id', $userId)->where('status', 'dikonfirmasi')->count(),
            'selesai'      => Booking::where('pelanggan_id', $userId)->where('status', 'selesai')->count(),
        ]);
    }
}
