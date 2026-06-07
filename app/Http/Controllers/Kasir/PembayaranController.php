<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    // Daftar booking yang perlu diproses pembayarannya
    public function index()
    {
        $bookings = Booking::where('status', 'dikonfirmasi')
            ->whereDoesntHave('pembayaran', fn($q) => $q->where('status', 'lunas'))
            ->with(['pelanggan', 'lapangan', 'pembayaran'])
            ->latest()
            ->get();

        return view('kasir.pembayaran.index', compact('bookings'));
    }

    // Proses pembayaran DP
    public function prosesDP(Request $request, Booking $booking)
    {
        $request->validate([
            'jumlah_dp'      => 'required|numeric|min:1',
            'metode'         => 'required|in:tunai,transfer,qris',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($booking->status !== 'dikonfirmasi') {
            return back()->with('error', 'Booking belum dikonfirmasi operator.');
        }

        if ($booking->pembayaran) {
            return back()->with('error', 'Pembayaran untuk booking ini sudah ada.');
        }

        $buktPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktPath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        }

        Pembayaran::create([
            'booking_id'        => $booking->id,
            'dikonfirmasi_oleh' => Auth::id(),
            'no_pembayaran'     => Pembayaran::generateNomor(),
            'jumlah_dp'         => $request->jumlah_dp,
            'jumlah_lunas'      => 0,
            'metode'            => $request->metode,
            'status'            => 'dp',
            'bukti_transfer'    => $buktPath,
            'paid_at'           => now(),
        ]);

        return back()->with('success', 'Pembayaran DP berhasil dicatat.');
    }

    // Proses pelunasan
    public function prosesLunas(Request $request, Booking $booking)
    {
        $request->validate([
            'metode'         => 'required|in:tunai,transfer,qris',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $pembayaran = $booking->pembayaran;

        if (!$pembayaran) {
            return back()->with('error', 'DP belum dilakukan untuk booking ini.');
        }

        if ($pembayaran->status === 'lunas') {
            return back()->with('error', 'Booking ini sudah lunas.');
        }

        $sisa = $booking->total_harga - $pembayaran->jumlah_dp;

        $buktPath = $pembayaran->bukti_transfer;
        if ($request->hasFile('bukti_transfer')) {
            $buktPath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        }

        $pembayaran->update([
            'jumlah_lunas'      => $sisa,
            'metode'            => $request->metode,
            'status'            => 'lunas',
            'bukti_transfer'    => $buktPath,
            'dikonfirmasi_oleh' => Auth::id(),
            'paid_at'           => now(),
        ]);

        return back()->with('success', 'Pelunasan berhasil. Booking siap digunakan!');
    }

    // Proses refund
    public function prosesRefund(Booking $booking)
    {
        $pembayaran = $booking->pembayaran;

        if (!$pembayaran) {
            return back()->with('error', 'Tidak ada pembayaran untuk booking ini.');
        }

        $pembayaran->update(['status' => 'refund']);
        $booking->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Refund berhasil diproses.');
    }

    // Tampilkan halaman cetak bukti
    public function bukti(Pembayaran $pembayaran)
    {
        $pembayaran->load([
            'booking.pelanggan',
            'booking.lapangan',
            'dikonfirmasiOleh',
        ]);

        return view('kasir.bukti', compact('pembayaran'));
    }
}
