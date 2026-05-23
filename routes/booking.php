<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Futsal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .sidebar {
            position: fixed; top: 0; left: 0; height: 100%; width: 250px;
            background: #1e293b; color: white; transition: all 0.3s; z-index: 1000;
        }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid #334155; }
        .sidebar-header h3 { margin: 0; font-size: 20px; }
        .sidebar-header p { font-size: 12px; margin: 5px 0 0; color: #94a3b8; }
        .sidebar-menu { padding: 0; margin-top: 20px; }
        .sidebar-menu li { list-style: none; padding: 12px 20px; cursor: pointer; transition: all 0.3s; }
        .sidebar-menu li:hover { background: #334155; }
        .sidebar-menu li.active { background: #3b82f6; }
        .sidebar-menu li i { margin-right: 10px; width: 25px; }
        
        .content { margin-left: 250px; padding: 20px; }
        .navbar-custom {
            background: white; padding: 15px 20px; border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;
        }
        .stat-card {
            background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-card i { font-size: 35px; color: #3b82f6; }
        .stat-card .number { font-size: 28px; font-weight: bold; margin: 10px 0; }
        .form-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        
        .badge-status { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-pending { background: #f59e0b; color: #000; }
        .badge-confirmed { background: #10b981; color: #fff; }
        .badge-cancelled { background: #ef4444; color: #fff; }
        .badge-completed { background: #06b6d4; color: #fff; }
        
        .info-harga { background: #dbeafe; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
        
        .btn-nav {
            background: #10b981;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-nav:hover { background: #059669; }
        
        @media (max-width: 768px) {
            .sidebar { left: -250px; }
            .content { margin-left: 0; }
            .sidebar.active { left: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-futbol fa-2x mb-2"></i>
        <h3>Futsal Booking</h3>
        <p>Sistem Booking Online</p>
    </div>
    <ul class="sidebar-menu">
        <li class="active" onclick="showPage('dashboard')"><i class="fas fa-tachometer-alt"></i> Dashboard</li>
        <li onclick="showPage('booking')"><i class="fas fa-calendar-plus"></i> Booking</li>
        <li onclick="showPage('daftar')"><i class="fas fa-list"></i> Daftar Booking</li>
        <li onclick="showPage('laporan')"><i class="fas fa-chart-line"></i> Laporan</li>
    </ul>
</div>

<div class="content">
    <div class="navbar-custom d-flex justify-content-between align-items-center">
        <div>
            <button class="btn btn-outline-secondary" id="menuToggle"><i class="fas fa-bars"></i></button>
            <span class="ms-3 fw-bold" id="pageTitle">Dashboard</span>
        </div>
        <div>
            <button class="btn-nav" onclick="window.location.href='lapangan.html'">
                <i class="fas fa-futbol"></i> Kelola Lapangan
            </button>
        </div>
    </div>

    <!-- Dashboard -->
    <div id="dashboardPage">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-calendar-check"></i>
                    <div class="number" id="totalBooking">0</div>
                    <div>Total Booking</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-users"></i>
                    <div class="number" id="totalPelanggan">0</div>
                    <div>Total Pelanggan</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-money-bill-wave"></i>
                    <div class="number" id="totalPendapatan">0</div>
                    <div>Pendapatan</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-clock"></i>
                    <div class="number" id="bookingHariIni">0</div>
                    <div>Booking Hari Ini</div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="form-card">
                    <h5><i class="fas fa-chart-pie"></i> Status Booking</h5>
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-card">
                    <h5><i class="fas fa-chart-bar"></i> Booking Per Lapangan</h5>
                    <canvas id="lapanganChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="form-card">
                    <h5><i class="fas fa-history"></i> 5 Booking Terbaru</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr style="background:#f1f5f9;">
                                    <th>Kode</th>
                                    <th>Pelanggan</th>
                                    <th>Lapangan</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentBookings"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Booking -->
    <div id="bookingPage" style="display: none;">
        <div class="form-card">
            <h5><i class="fas fa-plus-circle"></i> Form Booking Baru</h5>
            <div class="info-harga">
                <strong><i class="fas fa-tag"></i> Harga Sewa:</strong><br>
                🌞 Pagi-Sore (06:00-18:00) = Rp 30.000/jam &nbsp;&nbsp;🌙 Malam (18:00-24:00) = Rp 50.000/jam
            </div>
            <form id="bookingForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Nama Pelanggan *</label>
                            <input type="text" class="form-control" id="pelanggan_nama" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>No Telepon</label>
                            <input type="text" class="form-control" id="pelanggan_telp">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Pilih Lapangan *</label>
                            <select class="form-control" id="lapangan_id" required onchange="updateLapanganInfo()">
                                <option value="">-- Pilih Lapangan --</option>
                            </select>
                            <small class="text-muted" id="infoLapangan"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Diproses Oleh</label>
                            <input type="text" class="form-control" id="diproses_oleh" value="Admin" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Tanggal Main *</label>
                            <input type="date" class="form-control" id="tanggal_main" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Jam Mulai *</label>
                            <input type="time" class="form-control" id="jam_mulai" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Jam Selesai *</label>
                            <input type="time" class="form-control" id="jam_selesai" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Kode Booking</label>
                            <input type="text" class="form-control" id="kode_boking" readonly style="background:#e9ecef">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Total Harga</label>
                            <input type="text" class="form-control" id="total_harga" readonly style="background:#e9ecef">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Status</label>
                            <select class="form-control" id="status">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>ID (Auto)</label>
                            <input type="text" class="form-control" id="booking_id" readonly style="background:#e9ecef">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Booking</button>
                <button type="button" class="btn btn-secondary" onclick="resetForm()"><i class="fas fa-undo"></i> Reset</button>
            </form>
        </div>
    </div>

    <!-- Daftar Booking -->
    <div id="daftarPage" style="display: none;">
        <div class="form-card">
            <h5><i class="fas fa-list"></i> Semua Booking</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Lapangan</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="allBookingsTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Laporan -->
    <div id="laporanPage" style="display: none;">
        <div class="form-card">
            <h5><i class="fas fa-chart-line"></i> Laporan Booking</h5>
            <div class="row mb-4">
                <div class="col-md-4">
                    <label>Tanggal Mulai</label>
                    <input type="date" class="form-control" id="filter_tgl_mulai">
                </div>
                <div class="col-md-4">
                    <label>Tanggal Selesai</label>
                    <input type="date" class="form-control" id="filter_tgl_selesai">
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary form-control" onclick="filterLaporan()"><i class="fas fa-search"></i> Tampilkan</button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <strong>Total Booking:</strong> <span id="lap_total_booking">0</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <strong>Total Pendapatan:</strong> Rp <span id="lap_total_pendapatan">0</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        <strong>Rata-rata:</strong> Rp <span id="lap_rata_rata">0</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Lapangan</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="laporanTable"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    let lapanganData = JSON.parse(localStorage.getItem('lapangan')) || [];
    let bookings = JSON.parse(localStorage.getItem('bookings')) || [];
    let currentId = bookings.length > 0 ? Math.max(...bookings.map(b => b.id)) + 1 : 1;
    let statusChart = null;
    let lapanganChart = null;
    
    function loadLapanganOptions() {
        let select = document.getElementById('lapangan_id');
        if(select) {
            let options = '<option value="">-- Pilih Lapangan --</option>';
            for(let l of lapanganData) {
                if(l.status === 'tersedia') {
                    options += '<option value="' + l.id + '">' + l.nama_lapangan + ' - ' + l.jenis_lantai + ' (' + l.kapasitas + ' org)</option>';
                }
            }
            select.innerHTML = options;
        }
    }
    
    function updateLapanganInfo() {
        let select = document.getElementById('lapangan_id');
        let selectedId = parseInt(select.value);
        let lapangan = null;
        for(let l of lapanganData) {
            if(l.id === selectedId) {
                lapangan = l;
                break;
            }
        }
        let infoSpan = document.getElementById('infoLapangan');
        if(lapangan && infoSpan) {
            let fasilitasText = lapangan.fasilitas ? lapangan.fasilitas.join(', ') : '-';
            infoSpan.innerHTML = '<i class="fas fa-info-circle"></i> ' + lapangan.jenis_lantai + ' | Kapasitas ' + lapangan.kapasitas + ' org | Fasilitas: ' + fasilitasText;
        } else if(infoSpan) {
            infoSpan.innerHTML = '';
        }
        hitungTotal();
    }
    
    function getHargaPerJam(jam) {
        let jamAngka = parseInt(jam.split(':')[0]);
        return (jamAngka >= 6 && jamAngka < 18) ? 30000 : 50000;
    }
    
    function generateKodeBooking() {
        let nextNumber = bookings.length + 1;
        let numStr = nextNumber.toString();
        while(numStr.length < 3) numStr = '0' + numStr;
        return 'BK' + numStr;
    }
    
    function hitungTotal() {
        let jamMulai = document.getElementById('jam_mulai').value;
        let jamSelesai = document.getElementById('jam_selesai').value;
        if(jamMulai && jamSelesai) {
            let mulai = parseInt(jamMulai.split(':')[0]);
            let selesai = parseInt(jamSelesai.split(':')[0]);
            let durasi = selesai - mulai;
            if(durasi <= 0) durasi = 1;
            let total = 0;
            for(let i = 0; i < durasi; i++) {
                let jamKe = mulai + i;
                total += (jamKe >= 6 && jamKe < 18) ? 30000 : 50000;
            }
            document.getElementById('total_harga').value = formatRupiah(total);
            return total;
        }
        return 0;
    }
    
    function formatRupiah(angka) {
        let str = angka.toString();
        let result = '';
        let count = 0;
        for(let i = str.length - 1; i >= 0; i--) {
            result = str[i] + result;
            count++;
            if(count % 3 === 0 && i !== 0) {
                result = '.' + result;
            }
        }
        return 'Rp ' + result;
    }
    
    function getLapanganNamaById(id) {
        for(let l of lapanganData) {
            if(l.id === id) return l.nama_lapangan;
        }
        return 'Lapangan tidak ditemukan';
    }
    
    function updateDashboard() {
        document.getElementById('totalBooking').innerText = bookings.length;
        let uniquePelanggan = [];
        for(let b of bookings) {
            if(!uniquePelanggan.includes(b.pelanggan_nama)) {
                uniquePelanggan.push(b.pelanggan_nama);
            }
        }
        document.getElementById('totalPelanggan').innerText = uniquePelanggan.length;
        
        let totalPendapatan = 0;
        for(let b of bookings) {
            totalPendapatan += b.total_harga;
        }
        document.getElementById('totalPendapatan').innerText = formatRupiah(totalPendapatan);
        
        let today = new Date().toISOString().split('T')[0];
        let bookingHariIni = 0;
        for(let b of bookings) {
            if(b.tanggal_main === today) bookingHariIni++;
        }
        document.getElementById('bookingHariIni').innerText = bookingHariIni;
        
        let pending = 0, confirmed = 0, completed = 0, cancelled = 0;
        for(let b of bookings) {
            if(b.status === 'pending') pending++;
            else if(b.status === 'confirmed') confirmed++;
            else if(b.status === 'completed') completed++;
            else if(b.status === 'cancelled') cancelled++;
        }
        
        if(statusChart) statusChart.destroy();
        let ctxStatus = document.getElementById('statusChart');
        if(ctxStatus) {
            statusChart = new Chart(ctxStatus, {
                type: 'pie',
                data: {
                    labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
                    datasets: [{
                        data: [pending, confirmed, completed, cancelled],
                        backgroundColor: ['#f59e0b', '#10b981', '#06b6d4', '#ef4444']
                    }]
                }
            });
        }
        
        let lapanganCount = {};
        for(let b of bookings) {
            let nama = getLapanganNamaById(b.lapangan_id);
            if(lapanganCount[nama]) {
                lapanganCount[nama]++;
            } else {
                lapanganCount[nama] = 1;
            }
        }
        
        if(lapanganChart) lapanganChart.destroy();
        let ctxLapangan = document.getElementById('lapanganChart');
        if(ctxLapangan) {
            lapanganChart = new Chart(ctxLapangan, {
                type: 'bar',
                data: {
                    labels: Object.keys(lapanganCount),
                    datasets: [{
                        label: 'Jumlah Booking',
                        data: Object.values(lapanganCount),
                        backgroundColor: '#3b82f6'
                    }]
                }
            });
        }
        
        let recent = [...bookings].reverse().slice(0,5);
        let tbody = document.getElementById('recentBookings');
        let html = '';
        for(let b of recent) {
            html += '<tr>';
            html += '<td><strong>' + b.kode_boking + '</strong></td>';
            html += '<td>' + b.pelanggan_nama + '</td>';
            html += '<td>' + getLapanganNamaById(b.lapangan_id) + '</td>';
            html += '<td>' + b.tanggal_main + '</td>';
            html += '<td>' + b.jam_mulai + '-' + b.jam_selesai + '</td>';
            html += '<td>' + formatRupiah(b.total_harga) + '</td>';
            html += '<td><span class="badge-status badge-' + b.status + '">' + b.status + '</span></td>';
            html += '</tr>';
        }
        tbody.innerHTML = html;
    }
    
    function showAllBookings() {
        let tbody = document.getElementById('allBookingsTable');
        if(bookings.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>';
        } else {
            let html = '';
            for(let b of bookings) {
                html += '<tr>';
                html += '<td>' + b.id + '</td>';
                html += '<td><strong>' + b.kode_boking + '</strong></td>';
                html += '<td>' + b.pelanggan_nama + '</td>';
                html += '<td>' + getLapanganNamaById(b.lapangan_id) + '</td>';
                html += '<td>' + b.tanggal_main + '</td>';
                html += '<td>' + b.jam_mulai + '-' + b.jam_selesai + '</td>';
                html += '<td>' + formatRupiah(b.total_harga) + '</td>';
                html += '<td><span class="badge-status badge-' + b.status + '">' + b.status + '</span></td>';
                html += '<td>';
                html += '<button class="btn btn-sm btn-warning" onclick="editBooking(' + b.id + ')"><i class="fas fa-edit"></i></button> ';
                html += '<button class="btn btn-sm btn-danger" onclick="deleteBooking(' + b.id + ')"><i class="fas fa-trash"></i></button>';
                html += '</td>';
                html += '</tr>';
            }
            tbody.innerHTML = html;
        }
    }
    
    function editBooking(id) {
        let b = null;
        for(let item of bookings) {
            if(item.id === id) {
                b = item;
                break;
            }
        }
        if(b) {
            document.getElementById('booking_id').value = b.id;
            document.getElementById('pelanggan_nama').value = b.pelanggan_nama;
            document.getElementById('pelanggan_telp').value = b.pelanggan_telp || '';
            document.getElementById('lapangan_id').value = b.lapangan_id;
            document.getElementById('diproses_oleh').value = b.diproses_oleh;
            document.getElementById('kode_boking').value = b.kode_boking;
            document.getElementById('tanggal_main').value = b.tanggal_main;
            document.getElementById('jam_mulai').value = b.jam_mulai;
            document.getElementById('jam_selesai').value = b.jam_selesai;
            document.getElementById('status').value = b.status;
            document.getElementById('total_harga').value = formatRupiah(b.total_harga);
            updateLapanganInfo();
            deleteBooking(id, true);
            showPage('booking');
        }
    }
    
    function deleteBooking(id, silent) {
        if(silent === true || confirm('Yakin hapus booking ini?')) {
            let newBookings = [];
            for(let b of bookings) {
                if(b.id !== id) newBookings.push(b);
            }
            bookings = newBookings;
            for(let i = 0; i < bookings.length; i++) {
                let num = i + 1;
                let numStr = num.toString();
                while(numStr.length < 3) numStr = '0' + numStr;
                bookings[i].kode_boking = 'BK' + numStr;
            }
            localStorage.setItem('bookings', JSON.stringify(bookings));
            updateDashboard();
            showAllBookings();
            filterLaporan();
            if(!silent) alert('Booking dihapus');
        }
    }
    
    document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        let total = hitungTotal();
        let kodeBooking = document.getElementById('kode_boking').value;
        if(!kodeBooking) kodeBooking = generateKodeBooking();
        
        let newBooking = {
            id: document.getElementById('booking_id').value ? parseInt(document.getElementById('booking_id').value) : currentId++,
            pelanggan_nama: document.getElementById('pelanggan_nama').value,
            pelanggan_telp: document.getElementById('pelanggan_telp').value,
            lapangan_id: parseInt(document.getElementById('lapangan_id').value),
            diproses_oleh: document.getElementById('diproses_oleh').value,
            kode_boking: kodeBooking,
            tanggal_main: document.getElementById('tanggal_main').value,
            jam_mulai: document.getElementById('jam_mulai').value,
            jam_selesai: document.getElementById('jam_selesai').value,
            total_harga: total,
            status: document.getElementById('status').value,
            created_at: new Date().toISOString()
        };
        
        let idx = -1;
        for(let i = 0; i < bookings.length; i++) {
            if(bookings[i].id === newBooking.id) {
                idx = i;
                break;
            }
        }
        if(idx !== -1) {
            bookings[idx] = newBooking;
        } else {
            bookings.push(newBooking);
        }
        
        for(let i = 0; i < bookings.length; i++) {
            let num = i + 1;
            let numStr = num.toString();
            while(numStr.length < 3) numStr = '0' + numStr;
            bookings[i].kode_boking = 'BK' + numStr;
        }
        
        localStorage.setItem('bookings', JSON.stringify(bookings));
        resetForm();
        updateDashboard();
        showAllBookings();
        filterLaporan();
        alert('Booking tersimpan!');
        showPage('daftar');
    });
    
    function resetForm() {
        document.getElementById('bookingForm').reset();
        document.getElementById('booking_id').value = '';
        document.getElementById('kode_boking').value = '';
        let today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal_main').value = today;
        document.getElementById('jam_mulai').value = '15:00';
        document.getElementById('jam_selesai').value = '16:00';
        document.getElementById('diproses_oleh').value = 'Admin';
        document.getElementById('total_harga').value = '';
        document.getElementById('infoLapangan').innerHTML = '';
        hitungTotal();
    }
    
    function filterLaporan() {
        let filtered = [];
        for(let b of bookings) {
            filtered.push(b);
        }
        let tglMulai = document.getElementById('filter_tgl_mulai').value;
        let tglSelesai = document.getElementById('filter_tgl_selesai').value;
        
        if(tglMulai) {
            let newFiltered = [];
            for(let b of filtered) {
                if(b.tanggal_main >= tglMulai) newFiltered.push(b);
            }
            filtered = newFiltered;
        }
        if(tglSelesai) {
            let newFiltered = [];
            for(let b of filtered) {
                if(b.tanggal_main <= tglSelesai) newFiltered.push(b);
            }
            filtered = newFiltered;
        }
        
        let totalBooking = filtered.length;
        let totalPendapatan = 0;
        for(let b of filtered) {
            totalPendapatan += b.total_harga;
        }
        let rataRata = totalBooking > 0 ? totalPendapatan / totalBooking : 0;
        
        document.getElementById('lap_total_booking').innerText = totalBooking;
        document.getElementById('lap_total_pendapatan').innerText = totalPendapatan.toLocaleString('id-ID');
        document.getElementById('lap_rata_rata').innerText = rataRata.toLocaleString('id-ID');
        
        let tbody = document.getElementById('laporanTable');
        if(filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>';
        } else {
            let html = '';
            for(let i = 0; i < filtered.length; i++) {
                let b = filtered[i];
                html += '<tr>';
                html += '<td>' + (i+1) + '</td>';
                html += '<td><strong>' + b.kode_boking + '</strong></td>';
                html += '<td>' + b.pelanggan_nama + '</td>';
                html += '<td>' + getLapanganNamaById(b.lapangan_id) + '</td>';
                html += '<td>' + b.tanggal_main + '</td>';
                html += '<td>' + b.jam_mulai + '-' + b.jam_selesai + '</td>';
                html += '<td>' + formatRupiah(b.total_harga) + '</td>';
                html += '<td><span class="badge-status badge-' + b.status + '">' + b.status + '</span></td>';
                html += '</tr>';
            }
            tbody.innerHTML = html;
        }
    }
    
    function showPage(page) {
        document.getElementById('dashboardPage').style.display = 'none';
        document.getElementById('bookingPage').style.display = 'none';
        document.getElementById('daftarPage').style.display = 'none';
        document.getElementById('laporanPage').style.display = 'none';
        
        if(page === 'dashboard') {
            document.getElementById('dashboardPage').style.display = 'block';
            document.getElementById('pageTitle').innerText = 'Dashboard';
            updateDashboard();
        } else if(page === 'booking') {
            document.getElementById('bookingPage').style.display = 'block';
            document.getElementById('pageTitle').innerText = 'Form Booking';
            loadLapanganOptions();
        } else if(page === 'daftar') {
            document.getElementById('daftarPage').style.display = 'block';
            document.getElementById('pageTitle').innerText = 'Daftar Booking';
            showAllBookings();
        } else if(page === 'laporan') {
            document.getElementById('laporanPage').style.display = 'block';
            document.getElementById('pageTitle').innerText = 'Laporan';
            filterLaporan();
        }
        
        let menuItems = document.querySelectorAll('.sidebar-menu li');
        for(let i = 0; i < menuItems.length; i++) {
            menuItems[i].classList.remove('active');
        }
        if(page === 'dashboard') menuItems[0].classList.add('active');
        else if(page === 'booking') menuItems[1].classList.add('active');
        else if(page === 'daftar') menuItems[2].classList.add('active');
        else if(page === 'laporan') menuItems[3].classList.add('active');
    }
    
    document.getElementById('menuToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
    document.getElementById('lapangan_id')?.addEventListener('change', updateLapanganInfo);
    document.getElementById('jam_mulai')?.addEventListener('change', hitungTotal);
    document.getElementById('jam_selesai')?.addEventListener('change', hitungTotal);
    
    if(bookings.length === 0) {
        bookings = [
            {id:1, pelanggan_nama:'Budi Santoso', pelanggan_telp:'08123456789', lapangan_id:1, diproses_oleh:'Admin', kode_boking:'BK001', tanggal_main:'2026-05-25', jam_mulai:'15:00', jam_selesai:'17:00', total_harga:60000, status:'confirmed', created_at:new Date().toISOString()},
            {id:2, pelanggan_nama:'Andi Wijaya', pelanggan_telp:'08129876543', lapangan_id:2, diproses_oleh:'Admin', kode_boking:'BK002', tanggal_main:'2026-05-26', jam_mulai:'19:00', jam_selesai:'20:00', total_harga:50000, status:'pending', created_at:new Date().toISOString()}
        ];
        localStorage.setItem('bookings', JSON.stringify(bookings));
        currentId = 3;
    }
    
    let today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_main').value = today;
    document.getElementById('jam_mulai').value = '15:00';
    document.getElementById('jam_selesai').value = '16:00';
    loadLapanganOptions();
    showPage('dashboard');
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>