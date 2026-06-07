<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Lapangan;
use App\Models\Pembayaran;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $bookingTerbaru   = Booking::with(['pelanggan', 'lapangan'])
                                ->latest()->take(8)->get();

        $bookingPerStatus = Booking::selectRaw('status, count(*) as jumlah')
                                ->groupBy('status')
                                ->pluck('jumlah', 'status');

        $bookingTotal = Booking::count();

        return view('admin.dashboard', [
            'totalPengguna'   => User::count(),
            'totalLapangan'   => Lapangan::count(),
            'bookingHariIni'  => Booking::whereDate('created_at', today())->count(),
            'pendapatanBulan' => Pembayaran::whereMonth('paid_at', now()->month)
                                    ->selectRaw('SUM(jumlah_dp + jumlah_lunas) as total')
                                    ->value('total') ?? 0,
            'bookingTerbaru'  => $bookingTerbaru,
            'bookingPerStatus'=> $bookingPerStatus,
            'bookingTotal'    => $bookingTotal ?: 1, // hindari division by zero
        ]);
    }
}
