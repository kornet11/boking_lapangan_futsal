@extends('layouts.app')
@section('title', 'Jadwal & Harga')
@section('page-title', 'Jadwal & Harga')

@section('content')

<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah Tarif
    </button>
</div>

@foreach($lapangan as $lp)
@if($lp->jadwalHarga->count())
<div class="card mb-3">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-geo-alt-fill text-primary"></i>
        <span>{{ $lp->nama_lapangan }}</span>
        <span class="badge bg-light text-dark ms-auto">{{ $lp->jadwalHarga->count() }} slot</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr><th>Tipe Hari</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Harga/Jam</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($lp->jadwalHarga->sortBy(['tipe_hari','jam_mulai']) as $j)
                <tr>
                    <td>
                        <span class="badge rounded-pill px-2" style="font-size:11px;
                            background:{{ $j->tipe_hari === 'weekday' ? '#dbeafe' : '#ede9fe' }};
                            color:{{ $j->tipe_hari === 'weekday' ? '#1e40af' : '#5b21b6' }}">
                            {{ ucfirst($j->tipe_hari) }}
                        </span>
                    </td>
                    <td>{{ substr($j->jam_mulai,0,5) }}</td>
                    <td>{{ substr($j->jam_selesai,0,5) }}</td>
                    <td class="fw-semibold">Rp {{ number_format($j->harga_per_jam,0,',','.') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.jadwal.destroy', $j) }}"
                              onsubmit="return confirm('Hapus tarif ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endforeach

@if($jadwalHarga->isEmpty())
<div class="card text-center py-5">
    <i class="bi bi-clock" style="font-size:40px;opacity:.2"></i>
    <p class="mt-3 text-muted">Belum ada tarif. Tambahkan tarif untuk setiap lapangan.</p>
</div>
@endif

{{-- Modal Tambah Tarif --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Tarif</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.jadwal.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Lapangan</label>
                        <select name="lapangan_id" class="form-select" required>
                            <option value="">Pilih lapangan...</option>
                            @foreach($lapangan as $lp)
                            <option value="{{ $lp->id }}">{{ $lp->nama_lapangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:500">Tipe Hari</label>
                        <select name="tipe_hari" class="form-select" required>
                            <option value="weekday">Weekday (Senin–Jumat)</option>
                            <option value="weekend">Weekend (Sabtu–Minggu)</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:500">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:500">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:13px;font-weight:500">Harga per Jam (Rp)</label>
                        <input type="number" name="harga_per_jam" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
