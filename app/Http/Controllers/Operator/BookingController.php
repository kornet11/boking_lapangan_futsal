<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Daftar booking yang menunggu konfirmasi
    public function masuk()
    {
        $bookings = Booking::where('status', 'menunggu')
            ->with(['pelanggan', 'lapangan'])
            ->latest()->get();

        return view('operator.booking.masuk', compact('bookings'));
    }

    // Semua booking dengan filter opsional
    public function index(Request $request)
    {
        $bookings = Booking::with(['pelanggan', 'lapangan', 'pembayaran'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->tanggal, fn($q) => $q->where('tanggal_main', $request->tanggal))
            ->latest()->paginate(20);

        return view('operator.booking.index', compact('bookings'));
    }

    // Konfirmasi booking
    public function konfirmasi(Booking $booking)
    {
        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Hanya booking berstatus menunggu yang bisa dikonfirmasi.');
        }

        $booking->update([
            'status'        => 'dikonfirmasi',
            'diproses_oleh' => Auth::id(),
        ]);

        return back()->with('success', "Booking {$booking->kode_booking} berhasil dikonfirmasi.");
    }

    // Batalkan booking
    public function batalkan(Booking $booking)
    {
        if (in_array($booking->status, ['selesai', 'dibatalkan'])) {
            return back()->with('error', 'Booking tidak bisa dibatalkan.');
        }

        $booking->update([
            'status'        => 'dibatalkan',
            'diproses_oleh' => Auth::id(),
        ]);

        return back()->with('success', "Booking {$booking->kode_booking} berhasil dibatalkan.");
    }

    // Tandai selesai
    public function selesai(Booking $booking)
    {
        if ($booking->status !== 'dikonfirmasi') {
            return back()->with('error', 'Hanya booking dikonfirmasi yang bisa ditandai selesai.');
        }

        $booking->update([
            'status'        => 'selesai',
            'diproses_oleh' => Auth::id(),
        ]);

        return back()->with('success', "Booking {$booking->kode_booking} ditandai selesai.");
    }

    // Jadwal hari ini
    public function jadwalHariIni()
    {
        $jadwal = Booking::where('tanggal_main', today())
            ->whereNotIn('status', ['dibatalkan'])
            ->with(['lapangan', 'pelanggan', 'pembayaran'])
            ->orderBy('jam_mulai')
            ->get();

        return view('operator.jadwal', compact('jadwal'));
    }

    // Cek ketersediaan slot
    public function ketersediaan(Request $request)
    {
        $lapangan    = Lapangan::where('status', 'aktif')->get();
        $bookingAda  = collect();
        $lapanganDipilih = null;

        if ($request->lapangan_id && $request->tanggal) {
            $lapanganDipilih = Lapangan::with('jadwalHarga')->find($request->lapangan_id);
            $bookingAda = Booking::where('lapangan_id', $request->lapangan_id)
                ->where('tanggal_main', $request->tanggal)
                ->whereNotIn('status', ['dibatalkan'])
                ->get(['jam_mulai', 'jam_selesai', 'status', 'kode_booking']);
        }

        return view('operator.ketersediaan', compact('lapangan', 'bookingAda', 'lapanganDipilih'));
    }
}
