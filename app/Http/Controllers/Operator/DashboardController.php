<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Lapangan;

class DashboardController extends Controller
{
    public function index()
    {
        $bookingMasuk = Booking::where('status', 'menunggu')
            ->with(['pelanggan', 'lapangan'])
            ->latest()->get();

        return view('operator.dashboard', [
            'bookingMasuk'  => $bookingMasuk,
            'menunggu'      => $bookingMasuk->count(),
            'hariIniTotal'  => Booking::whereDate('tanggal_main', today())
                                ->whereNotIn('status', ['dibatalkan'])->count(),
            'lapanganAktif' => Lapangan::where('status', 'aktif')->count(),
        ]);
    }
}
