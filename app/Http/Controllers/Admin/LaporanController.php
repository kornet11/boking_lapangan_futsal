<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dari   = $request->dari   ?? now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? now()->format('Y-m-d');

        $pembayaran = Pembayaran::whereBetween('paid_at', [$dari, $sampai . ' 23:59:59'])
            ->with(['booking.pelanggan', 'booking.lapangan'])
            ->latest('paid_at')
            ->paginate(30);

        $totalPendapatan = Pembayaran::whereBetween('paid_at', [$dari, $sampai . ' 23:59:59'])
            ->selectRaw('SUM(jumlah_dp + jumlah_lunas) as total')
            ->value('total') ?? 0;

        $totalTransaksi = Pembayaran::whereBetween('paid_at', [$dari, $sampai . ' 23:59:59'])->count();

        $totalBooking = Booking::whereBetween('created_at', [$dari, $sampai . ' 23:59:59'])->count();

        return view('admin.laporan', compact(
            'pembayaran', 'totalPendapatan', 'totalTransaksi', 'totalBooking'
        ));
    }
}
