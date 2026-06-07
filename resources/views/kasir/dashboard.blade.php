@extends('layouts.app')
@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard Kasir')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#fef3c7;color:#92400e"><i class="bi bi-credit-card"></i></div>
            <div>
                <div class="stat-value">{{ $bookingBelumBayar }}</div>
                <div class="stat-label">Perlu Diproses</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#d1fae5;color:#065f46"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-value" style="font-size:18px">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</div>
                <div class="stat-label">Pendapatan Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon" style="background:#dbeafe;color:#1e40af"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="stat-value">{{ $transaksiHariIni }}</div>
                <div class="stat-label">Transaksi Hari Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-3">
    <a href="{{ route('kasir.pembayaran.index') }}" class="card flex-fill p-4 text-decoration-none"
       style="border-color:#e5e7eb!important;border-radius:12px">
        <div class="d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:#e8f5ee;color:#1a6b3c;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="bi bi-cash"></i>
            </div>
            <div>
                <div class="fw-bold" style="color:#111">Proses Pembayaran</div>
                <div style="font-size:13px;color:#888">DP & Pelunasan booking</div>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
        </div>
    </a>

    <a href="{{ route('kasir.laporan') }}" class="card flex-fill p-4 text-decoration-none"
       style="border-color:#e5e7eb!important;border-radius:12px">
        <div class="d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;background:#dbeafe;color:#1e40af;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <div class="fw-bold" style="color:#111">Laporan Harian</div>
                <div style="font-size:13px;color:#888">Rekap transaksi hari ini</div>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
        </div>
    </a>
</div>

@endsection
