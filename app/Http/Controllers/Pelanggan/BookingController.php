<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Lapangan;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Riwayat booking pelanggan
    public function index(Request $request)
    {
        $bookings = Booking::where('pelanggan_id', Auth::id())
            ->when(
                $request->status && $request->status !== 'semua',
                fn($q) => $q->where('status', $request->status)
            )
            ->with(['lapangan', 'pembayaran', 'ulasan', 'promos'])
            ->latest()
            ->paginate(10);

        return view('pelanggan.booking.index', compact('bookings'));
    }

    // Form buat booking baru
    public function create(Request $request)
    {
        $lapangan = Lapangan::where('status', 'aktif')
            ->with('jadwalHarga')
            ->get();

        return view('pelanggan.booking.create', compact('lapangan'));
    }

    // Simpan booking baru
    public function store(Request $request)
    {
        $request->validate([
            'lapangan_id'  => 'required|exists:lapangan,id',
            'tanggal_main' => 'required|date|after_or_equal:today',
            'jam_mulai'    => 'required|date_format:H:i',
            'jam_selesai'  => 'required|date_format:H:i|after:jam_mulai',
            'kode_promo'   => 'nullable|string',
        ]);

        // Cek konflik jadwal
        if (Booking::cekKonflik(
            $request->lapangan_id,
            $request->tanggal_main,
            $request->jam_mulai,
            $request->jam_selesai
        )) {
            return back()->withInput()
                         ->with('error', 'Maaf, slot jam tersebut sudah dipesan. Pilih jam lain.');
        }

        // Hitung harga
        $lapangan    = Lapangan::findOrFail($request->lapangan_id);
        $jadwalHarga = $lapangan->getHarga($request->tanggal_main, $request->jam_mulai);

        if (!$jadwalHarga) {
            return back()->withInput()
                         ->with('error', 'Tidak ada tarif untuk slot jam yang dipilih.');
        }

        $durasi     = (strtotime($request->jam_selesai) - strtotime($request->jam_mulai)) / 3600;
        $totalHarga = $jadwalHarga->harga_per_jam * $durasi;

        // Buat booking
        $booking = Booking::create([
            'pelanggan_id' => Auth::id(),
            'lapangan_id'  => $request->lapangan_id,
            'kode_booking' => Booking::generateKode(),
            'tanggal_main' => $request->tanggal_main,
            'jam_mulai'    => $request->jam_mulai,
            'jam_selesai'  => $request->jam_selesai,
            'total_harga'  => $totalHarga,
            'status'       => 'menunggu',
        ]);

        // Terapkan promo jika ada
        if ($request->filled('kode_promo')) {
            $promo = Promo::where('kode_promo', strtoupper($request->kode_promo))->first();

            if ($promo && $promo->isValid()) {
                $nilaiDiskon = $promo->hitungDiskon($totalHarga);
                $booking->update(['total_harga' => $totalHarga - $nilaiDiskon]);
                $booking->promos()->attach($promo->id, ['nilai_diskon' => $nilaiDiskon]);
                $promo->increment('total_digunakan');
            }
        }

        return redirect()->route('pelanggan.booking.index')
                         ->with('success', "Booking berhasil! Kode: {$booking->kode_booking}. Tunggu konfirmasi dari operator.");
    }

    // Detail booking
    public function show(Booking $booking)
    {
        // Pastikan hanya bisa lihat booking milik sendiri
        abort_if($booking->pelanggan_id !== Auth::id(), 403);

        $booking->load(['lapangan', 'pembayaran', 'ulasan', 'promos']);
        return view('pelanggan.booking.show', compact('booking'));
    }

    // Batalkan booking
    public function batalkan(Booking $booking)
    {
        abort_if($booking->pelanggan_id !== Auth::id(), 403);

        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Booking hanya bisa dibatalkan saat masih menunggu konfirmasi.');
        }

        $booking->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    // Tulis ulasan setelah selesai
    public function ulasan(Request $request, Booking $booking)
    {
        abort_if($booking->pelanggan_id !== Auth::id(), 403);

        if ($booking->status !== 'selesai') {
            return back()->with('error', 'Ulasan hanya bisa ditulis setelah booking selesai.');
        }

        if ($booking->ulasan) {
            return back()->with('error', 'Kamu sudah menulis ulasan untuk booking ini.');
        }

        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        $booking->ulasan()->create([
            'pelanggan_id' => Auth::id(),
            'lapangan_id'  => $booking->lapangan_id,
            'rating'       => $request->rating,
            'komentar'     => $request->komentar,
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }

    // Cek validitas promo (AJAX dari form booking)
    public function cekPromo(Request $request)
    {
        $promo = Promo::where('kode_promo', strtoupper($request->kode))->first();

        if (!$promo || !$promo->isValid()) {
            return response()->json(['valid' => false]);
        }

        return response()->json([
            'valid'          => true,
            'diskon_persen'  => $promo->diskon_persen,
            'maks_diskon'    => $promo->maks_diskon,
            'nama_promo'     => $promo->nama_promo,
        ]);
    }
}
