<?php
// Koneksi database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'futsal_booking';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Buat tabel jika belum ada
$sql_lapangan = "CREATE TABLE IF NOT EXISTS lapangan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_lapangan VARCHAR(100) NOT NULL,
    jenis_lantai VARCHAR(50) NOT NULL,
    kapasitas INT NOT NULL,
    fasilitas TEXT,
    foto VARCHAR(255),
    status ENUM('tersedia', 'tidak tersedia', 'maintenance') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql_booking = "CREATE TABLE IF NOT EXISTS booking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pelanggan_nama VARCHAR(100) NOT NULL,
    pelanggan_telp VARCHAR(15),
    lapangan_id INT,
    diproses_oleh VARCHAR(100),
    kode_boking VARCHAR(20) UNIQUE,
    tanggal_main DATE NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    total_harga INT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lapangan_id) REFERENCES lapangan(id) ON DELETE SET NULL
)";

mysqli_query($conn, $sql_lapangan);
mysqli_query($conn, $sql_booking);

// Ambil data dari database
$lapanganData = [];
$queryLapangan = "SELECT * FROM lapangan";
$resultLapangan = mysqli_query($conn, $queryLapangan);
while ($row = mysqli_fetch_assoc($resultLapangan)) {
    $lapanganData[] = $row;
}

$bookings = [];
$queryBooking = "SELECT b.*, l.nama_lapangan FROM booking b LEFT JOIN lapangan l ON b.lapangan_id = l.id ORDER BY b.created_at DESC";
$resultBooking = mysqli_query($conn, $queryBooking);
while ($row = mysqli_fetch_assoc($resultBooking)) {
    $bookings[] = $row;
}

// Proses simpan booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_booking') {
        $pelanggan_nama = mysqli_real_escape_string($conn, $_POST['pelanggan_nama']);
        $pelanggan_telp = mysqli_real_escape_string($conn, $_POST['pelanggan_telp']);
        $lapangan_id = (int)$_POST['lapangan_id'];
        $diproses_oleh = mysqli_real_escape_string($conn, $_POST['diproses_oleh']);
        $tanggal_main = mysqli_real_escape_string($conn, $_POST['tanggal_main']);
        $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
        $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);
        $total_harga = (int)$_POST['total_harga'];
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Generate kode booking
        $countQuery = "SELECT COUNT(*) as total FROM booking";
        $countResult = mysqli_query($conn, $countQuery);
        $countRow = mysqli_fetch_assoc($countResult);
        $nextNumber = $countRow['total'] + 1;
        $kode_boking = 'BK' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        $insertQuery = "INSERT INTO booking (pelanggan_nama, pelanggan_telp, lapangan_id, diproses_oleh, kode_boking, tanggal_main, jam_mulai, jam_selesai, total_harga, status) 
                        VALUES ('$pelanggan_nama', '$pelanggan_telp', $lapangan_id, '$diproses_oleh', '$kode_boking', '$tanggal_main', '$jam_mulai', '$jam_selesai', $total_harga, '$status')";
        
        if (mysqli_query($conn, $insertQuery)) {
            echo "<script>alert('Booking berhasil disimpan! Kode: $kode_boking'); window.location.href='booking.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan booking: " . mysqli_error($conn) . "');</script>";
        }
    }
    
    if ($_POST['action'] === 'delete_booking') {
        $id = (int)$_POST['id'];
        $deleteQuery = "DELETE FROM booking WHERE id = $id";
        mysqli_query($conn, $deleteQuery);
        echo "<script>window.location.href='booking.php';</script>";
    }
    
    if ($_POST['action'] === 'update_status') {
        $id = (int)$_POST['id'];
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $updateQuery = "UPDATE booking SET status = '$status' WHERE id = $id";
        mysqli_query($conn, $updateQuery);
        echo "<script>window.location.href='booking.php';</script>";
    }
}
?>

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
            <button class="btn-nav" onclick="window.location.href='lapangan.php'">
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
            <form method="POST" action="" id="bookingForm">
                <input type="hidden" name="action" value="save_booking">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Nama Pelanggan *</label>
                            <input type="text" class="form-control" name="pelanggan_nama" id="pelanggan_nama" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>No Telepon</label>
                            <input type="text" class="form-control" name="pelanggan_telp" id="pelanggan_telp">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Pilih Lapangan *</label>
                            <select class="form-control" name="lapangan_id" id="lapangan_id" required onchange="updateLapanganInfo()">
                                <option value="">-- Pilih Lapangan --</option>
                                <?php foreach ($lapanganData as $l): ?>
                                    <?php if ($l['status'] == 'tersedia'): ?>
                                        <option value="<?php echo $l['id']; ?>">
                                            <?php echo $l['nama_lapangan']; ?> - <?php echo $l['jenis_lantai']; ?> (<?php echo $l['kapasitas']; ?> org)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted" id="infoLapangan"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Diproses Oleh</label>
                            <input type="text" class="form-control" name="diproses_oleh" id="diproses_oleh" value="Admin" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Tanggal Main *</label>
                            <input type="date" class="form-control" name="tanggal_main" id="tanggal_main" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Jam Mulai *</label>
                            <input type="time" class="form-control" name="jam_mulai" id="jam_mulai" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Jam Selesai *</label>
                            <input type="time" class="form-control" name="jam_selesai" id="jam_selesai" required>
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
                            <input type="text" class="form-control" name="total_harga" id="total_harga" readonly style="background:#e9ecef">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
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
    // Data dari PHP
    let lapanganData = <?php echo json_encode($lapanganData); ?>;
    let bookingsData = <?php echo json_encode($bookings); ?>;
    let bookings = bookingsData;
    
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
            if(l.id == id) return l.nama_lapangan;
        }
        return 'Lapangan tidak ditemukan';
    }
    
    function getLapanganById(id) {
        for(let l of lapanganData) {
            if(l.id == id) return l;
        }
        return null;
    }
    
    function updateLapanganInfo() {
        let select = document.getElementById('lapangan_id');
        let selectedId = parseInt(select.value);
        let lapangan = getLapanganById(selectedId);
        let infoSpan = document.getElementById('infoLapangan');
        if(lapangan && infoSpan) {
            let fasilitasText = lapangan.fasilitas ? lapangan.fasilitas : '-';
            infoSpan.innerHTML = '<i class="fas fa-info-circle"></i> ' + lapangan.jenis_lantai + ' | Kapasitas ' + lapangan.kapasitas + ' org | Fasilitas: ' + fasilitasText;
        } else if(infoSpan) {
            infoSpan.innerHTML = '';
        }
        hitungTotal();
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
            document.getElementById('total_harga').setAttribute('value', total);
            return total;
        }
        return 0;
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
            totalPendapatan += parseInt(b.total_harga);
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
        
        if(window.statusChart) window.statusChart.destroy();
        let ctxStatus = document.getElementById('statusChart');
        if(ctxStatus) {
            window.statusChart = new Chart(ctxStatus, {
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
        
        if(window.lapanganChart) window.lapanganChart.destroy();
        let ctxLapangan = document.getElementById('lapanganChart');
        if(ctxLapangan) {
            window.lapanganChart = new Chart(ctxLapangan, {
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
            html += '<td>' + formatRupiah(parseInt(b.total_harga)) + '</td>';
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
                html += '<td>' + formatRupiah(parseInt(b.total_harga)) + '</td>';
                html += '<td><span class="badge-status badge-' + b.status + '">' + b.status + '</span></td>';
                html += '<td>';
                html += '<form method="POST" style="display:inline-block">';
                html += '<input type="hidden" name="action" value="delete_booking">';
                html += '<input type="hidden" name="id" value="' + b.id + '">';
                html += '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin hapus booking ini?\')"><i class="fas fa-trash"></i> Hapus</button>';
                html += '</form>';
                html += '<form method="POST" style="display:inline-block; margin-left:5px;">';
                html += '<input type="hidden" name="action" value="update_status">';
                html += '<input type="hidden" name="id" value="' + b.id + '">';
                html += '<select name="status" class="form-select form-select-sm" style="width:100px; display:inline-block;" onchange="this.form.submit()">';
                html += '<option value="pending" ' + (b.status === 'pending' ? 'selected' : '') + '>Pending</option>';
                html += '<option value="confirmed" ' + (b.status === 'confirmed' ? 'selected' : '') + '>Confirmed</option>';
                html += '<option value="completed" ' + (b.status === 'completed' ? 'selected' : '') + '>Completed</option>';
                html += '<option value="cancelled" ' + (b.status === 'cancelled' ? 'selected' : '') + '>Cancelled</option>';
                html += '</select>';
                html += '</form>';
                html += '</td>';
                html += '</tr>';
            }
            tbody.innerHTML = html;
        }
    }
    
    function resetForm() {
        document.getElementById('bookingForm').reset();
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
        let filtered = [...bookings];
        let tglMulai = document.getElementById('filter_tgl_mulai').value;
        let tglSelesai = document.getElementById('filter_tgl_selesai').value;
        
        if(tglMulai) {
            filtered = filtered.filter(b => b.tanggal_main >= tglMulai);
        }
        if(tglSelesai) {
            filtered = filtered.filter(b => b.tanggal_main <= tglSelesai);
        }
        
        let totalBooking = filtered.length;
        let totalPendapatan = 0;
        for(let b of filtered) {
            totalPendapatan += parseInt(b.total_harga);
        }
        let rataRata = totalBooking > 0 ? totalPendapatan / totalBooking : 0;
        
        document.getElementById('lap_total_booking').innerText = totalBooking;
        document.getElementById('lap_total_pendapatan').innerText = totalPendapatan.toLocaleString('id-ID');
        document.getElementById('lap_rata_rata').innerText = rataRata.toLocaleString('id-ID');
        
        let tbody = document.getElementById('laporanTable');
        if(filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">Tidak ada数据</td></tr>';
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
                html += '<td>' + formatRupiah(parseInt(b.total_harga)) + '</td>';
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
    
    let today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_main').value = today;
    document.getElementById('jam_mulai').value = '15:00';
    document.getElementById('jam_selesai').value = '16:00';
    
    showPage('dashboard');
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>