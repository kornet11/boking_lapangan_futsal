{{-- resources/views/operator/booking/masuk.blade.php --}}
@extends('layouts.app')
@section('title', 'Booking Masuk')
@section('page-title', 'Booking Masuk')

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-inbox me-2 text-warning"></i>Menunggu Konfirmasi</span>
        <span class="badge bg-warning text-dark">{{ $bookings->count() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Kode</th><th>Pelanggan</th><th>Lapangan</th><th>Tanggal Main</th><th>Jam</th><th>Total</th><th>Dibuat</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td class="fw-semibold text-primary" style="font-family:monospace">{{ $b->kode_booking }}</td>
                    <td>
                        <div class="fw-semibold">{{ $b->pelanggan->nama }}</div>
                        <small class="text-muted">{{ $b->pelanggan->no_telepon }}</small>
                    </td>
                    <td>{{ $b->lapangan->nama_lapangan }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->tanggal_main)->format('d M Y') }}</td>
                    <td>{{ substr($b->jam_mulai,0,5) }} – {{ substr($b->jam_selesai,0,5) }}</td>
                    <td class="fw-semibold">Rp {{ number_format($b->total_harga,0,',','.') }}</td>
                    <td style="font-size:12px;color:#888">{{ $b->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="{{ route('operator.booking.konfirmasi', $b) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success">
                                    <i class="bi bi-check-lg me-1"></i>Konfirmasi
                                </button>
                            </form>
                            <form method="POST" action="{{ route('operator.booking.batalkan', $b) }}"
                                  onsubmit="return confirm('Batalkan booking ini?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle" style="font-size:36px;opacity:.25"></i>
                        <p class="mt-2 mb-0">Tidak ada booking yang menunggu konfirmasi</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
