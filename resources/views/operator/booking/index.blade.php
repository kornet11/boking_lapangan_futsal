{{-- resources/views/operator/booking/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Semua Booking')
@section('page-title', 'Semua Booking')

@section('content')

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-sm-4">
                <label class="form-label" style="font-size:13px;font-weight:500">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(['menunggu','dikonfirmasi','selesai','dibatalkan'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label class="form-label" style="font-size:13px;font-weight:500">Tanggal Main</label>
                <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Kode</th><th>Pelanggan</th><th>Lapangan</th><th>Tanggal</th><th>Jam</th><th>Total</th><th>Status</th><th>Bayar</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td style="font-family:monospace;font-size:12px">{{ $b->kode_booking }}</td>
                    <td>{{ $b->pelanggan->nama }}</td>
                    <td>{{ $b->lapangan->nama_lapangan }}</td>
                    <td style="font-size:12px">{{ \Carbon\Carbon::parse($b->tanggal_main)->format('d M Y') }}</td>
                    <td style="font-size:12px">{{ substr($b->jam_mulai,0,5) }}–{{ substr($b->jam_selesai,0,5) }}</td>
                    <td>Rp {{ number_format($b->total_harga,0,',','.') }}</td>
                    <td><span class="badge badge-{{ $b->status }} px-2 rounded-pill" style="font-size:11px">{{ ucfirst($b->status) }}</span></td>
                    <td>
                        @if($b->pembayaran)
                            <span class="badge badge-{{ $b->pembayaran->status }} px-2 rounded-pill" style="font-size:11px">{{ strtoupper($b->pembayaran->status) }}</span>
                        @else
                            <span style="font-size:11px;color:#aaa">–</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($b->status === 'menunggu')
                            <form method="POST" action="{{ route('operator.booking.konfirmasi', $b) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success" title="Konfirmasi"><i class="bi bi-check-lg"></i></button>
                            </form>
                            <form method="POST" action="{{ route('operator.booking.batalkan', $b) }}" onsubmit="return confirm('Batalkan booking ini?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-danger" title="Batalkan"><i class="bi bi-x-lg"></i></button>
                            </form>
                            @elseif($b->status === 'dikonfirmasi')
                            <form method="POST" action="{{ route('operator.booking.selesai', $b) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-primary" title="Tandai Selesai"><i class="bi bi-check2-all"></i></button>
                            </form>
                            @else
                                <span style="font-size:11px;color:#aaa">–</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada booking</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer">{{ $bookings->links() }}</div>
    @endif
</div>

@endsection
