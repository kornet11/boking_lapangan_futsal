@extends('layouts.app')
@section('title', 'Cek Ketersediaan')
@section('page-title', 'Cek Ketersediaan Lapangan')

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-sm-5">
                <label class="form-label" style="font-size:13px;font-weight:500">Lapangan</label>
                <select name="lapangan_id" class="form-select" required>
                    <option value="">Pilih lapangan...</option>
                    @foreach($lapangan as $lp)
                    <option value="{{ $lp->id }}" {{ request('lapangan_id') == $lp->id ? 'selected':'' }}>
                        {{ $lp->nama_lapangan }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label class="form-label" style="font-size:13px;font-weight:500">Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                       value="{{ request('tanggal', today()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Cek
                </button>
            </div>
        </form>
    </div>
</div>

@if($lapanganDipilih)
<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar3 me-2 text-primary"></i>
        Jadwal <strong>{{ $lapanganDipilih->nama_lapangan }}</strong> —
        {{ \Carbon\Carbon::parse(request('tanggal'))->isoFormat('dddd, D MMMM Y') }}
    </div>
    <div class="card-body">
        @php
            // Generate slot jam 06:00 - 23:00
            $slots = [];
            for ($h = 6; $h < 23; $h++) {
                $jamMulai  = sprintf('%02d:00', $h);
                $jamSelesai= sprintf('%02d:00', $h + 1);
                $terisi    = $bookingAda->first(fn($b) =>
                    $b->jam_mulai <= $jamMulai && $b->jam_selesai > $jamMulai
                );
                $slots[] = compact('jamMulai', 'jamSelesai', 'terisi');
            }
        @endphp

        <div class="row g-2">
            @foreach($slots as $slot)
            <div class="col-6 col-sm-4 col-md-3">
                <div class="p-2 rounded text-center" style="
                    border: 1.5px solid {{ $slot['terisi'] ? '#fca5a5' : '#bbf7d0' }};
                    background: {{ $slot['terisi'] ? '#fee2e2' : '#f0fdf4' }};
                    font-size: 13px;">
                    <div class="fw-semibold">{{ $slot['jamMulai'] }} – {{ $slot['jamSelesai'] }}</div>
                    @if($slot['terisi'])
                        <div style="color:#991b1b;font-size:11px;margin-top:2px">
                            <i class="bi bi-x-circle me-1"></i>Terisi
                        </div>
                        <div style="font-size:10px;color:#aaa">{{ $slot['terisi']->kode_booking }}</div>
                    @else
                        <div style="color:#166534;font-size:11px;margin-top:2px">
                            <i class="bi bi-check-circle me-1"></i>Tersedia
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="d-flex gap-3 mt-4" style="font-size:12px">
            <span><span style="display:inline-block;width:12px;height:12px;background:#bbf7d0;border-radius:3px;margin-right:4px"></span>Tersedia</span>
            <span><span style="display:inline-block;width:12px;height:12px;background:#fca5a5;border-radius:3px;margin-right:4px"></span>Terisi</span>
        </div>
    </div>
</div>
@endif

@endsection
