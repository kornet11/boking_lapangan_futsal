<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembayaran;

class DashboardController extends Controller
{
    public function index()
    {
        // Booking sudah dikonfirmasi tapi belum lunas
        $bookingBelumBayar = Booking::where('status', 'dikonfirmasi')
            ->whereDoesntHave('pembayaran', fn($q) => $q->where('status', 'lunas'))
            ->with(['pelanggan', 'lapangan', 'pembayaran'])
            ->count();

        $totalHariIni = Pembayaran::whereDate('paid_at', today())
            ->selectRaw('SUM(jumlah_dp + jumlah_lunas) as total')
            ->value('total') ?? 0;

        $transaksiHariIni = Pembayaran::whereDate('paid_at', today())->count();

        return view('kasir.dashboard', compact(
            'bookingBelumBayar', 'totalHariIni', 'transaksiHariIni'
        ));
    }

    public function laporan()
    {
        $pembayaran = Pembayaran::whereDate('paid_at', today())
            ->with(['booking.pelanggan', 'booking.lapangan', 'dikonfirmasiOleh'])
            ->latest('paid_at')
            ->get();

        $totalHariIni = $pembayaran->sum(fn($p) => $p->jumlah_dp + $p->jumlah_lunas);

        return view('kasir.laporan', compact('pembayaran', 'totalHariIni'));
    }
}
