<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Lapangan Futsal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .badge-tersedia { background: #10b981; color: #fff; }
        .badge-tidak-tersedia { background: #ef4444; color: #fff; }
        .badge-maintenance { background: #f59e0b; color: #fff; }
        
        .lapangan-card {
            background: white; border-radius: 10px; overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.3s; margin-bottom: 20px;
        }
        .lapangan-card:hover { transform: translateY(-5px); }
        .lapangan-card img { width: 100%; height: 180px; object-fit: cover; }
        .lapangan-card .card-body { padding: 15px; }
        .fasilitas-item {
            display: inline-block; background: #e2e8f0; padding: 3px 8px;
            border-radius: 15px; font-size: 11px; margin: 2px;
        }
        
        .btn-nav {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-nav:hover {
            background: #2563eb;
        }
        
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
        <h3>Futsal Arena</h3>
        <p>Manajemen Lapangan</p>
    </div>
    <ul class="sidebar-menu">
        <li class="active" onclick="showPage('dashboard')"><i class="fas fa-tachometer-alt"></i> Dashboard</li>
        <li onclick="showPage('tambah')"><i class="fas fa-plus-circle"></i> Tambah Lapangan</li>
        <li onclick="showPage('daftar')"><i class="fas fa-list"></i> Daftar Lapangan</li>
        <li onclick="showPage('galeri')"><i class="fas fa-images"></i> Galeri</li>
    </ul>
</div>

<div class="content">
    <div class="navbar-custom d-flex justify-content-between align-items-center">
        <div>
            <button class="btn btn-outline-secondary" id="menuToggle"><i class="fas fa-bars"></i></button>
            <span class="ms-3 fw-bold" id="pageTitle">Dashboard</span>
        </div>
        <div>
            <button class="btn-nav" onclick="goToBooking()">
                <i class="fas fa-calendar-check"></i> Buka Halaman Booking
            </button>
        </div>
    </div>

    <!-- Dashboard -->
    <div id="dashboardPage">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-futbol"></i>
                    <div class="number" id="totalLapangan">0</div>
                    <div>Total Lapangan</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-check-circle"></i>
                    <div class="number" id="lapanganTersedia">0</div>
                    <div>Lapangan Tersedia</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-tools"></i>
                    <div class="number" id="lapanganMaintenance">0</div>
                    <div>Maintenance</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-users"></i>
                    <div class="number" id="totalKapasitas">0</div>
                    <div>Total Kapasitas</div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="form-card">
                    <h5><i class="fas fa-list"></i> Daftar Lapangan</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th>ID</th><th>Nama</th><th>Jenis Lantai</th><th>Kapasitas</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody id="dashboardTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Tambah -->
    <div id="tambahPage" style="display: none;">
        <div class="form-card">
            <h5><i class="fas fa-plus-circle"></i> Form Lapangan</h5>
            <form id="lapanganForm">
                <div class="row">
                    <div class="col-md-6"><div class="mb-3"><label>ID</label><input type="text" class="form-control" id="id" readonly style="background:#e9ecef"></div></div>
                    <div class="col-md-6"><div class="mb-3"><label>Nama Lapangan *</label><input type="text" class="form-control" id="nama_lapangan" required></div></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><div class="mb-3"><label>Jenis Lantai *</label>
                        <select class="form-control" id="jenis_lantai" required>
                            <option value="">Pilih</option><option value="Sintetis">Sintetis</option><option value="Vinyl">Vinyl</option>
                            <option value="Kayu">Kayu</option><option value="Semen">Semen</option><option value="Interlock">Interlock</option>
                        </select>
                    </div></div>
                    <div class="col-md-6"><div class="mb-3"><label>Kapasitas (orang) *</label><input type="number" class="form-control" id="kapasitas" required></div></div>
                </div>
                <div class="mb-3">
                    <label>Fasilitas</label>
                    <div class="row">
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="Lampu" id="f_lampu"><label>Lampu</label></div></div>
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="Kursi Penonton" id="f_kursi"><label>Kursi</label></div></div>
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="Ruang Ganti" id="f_ganti"><label>Ruang Ganti</label></div></div>
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="Toilet" id="f_toilet"><label>Toilet</label></div></div>
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="Mushola" id="f_mushola"><label>Mushola</label></div></div>
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="Parkir Luas" id="f_parkir"><label>Parkir</label></div></div>
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="Cafe" id="f_cafe"><label>Cafe</label></div></div>
                        <div class="col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="Free WiFi" id="f_wifi"><label>WiFi</label></div></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8"><div class="mb-3"><label>Foto (URL)</label><input type="text" class="form-control" id="foto" placeholder="https://..."></div></div>
                    <div class="col-md-4"><div class="mb-3"><label>Preview</label><div id="previewFoto" style="width:100px;height:100px;background:#e2e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image fa-2x text-muted"></i></div></div></div>
                </div>
                <div class="mb-3"><label>Status *</label>
                    <select class="form-control" id="status" required>
                        <option value="tersedia">Tersedia</option><option value="tidak tersedia">Tidak Tersedia</option><option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="resetForm()"><i class="fas fa-undo"></i> Reset</button>
            </form>
        </div>
    </div>

    <!-- Daftar -->
    <div id="daftarPage" style="display: none;">
        <div class="form-card">
            <h5><i class="fas fa-list"></i> Daftar Lapangan</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>Foto</th><th>Nama</th><th>Jenis</th><th>Kapasitas</th><th>Fasilitas</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody id="allLapanganTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Galeri -->
    <div id="galeriPage" style="display: none;">
        <div class="form-card">
            <h5><i class="fas fa-images"></i> Galeri Lapangan</h5>
            <div class="row" id="galeriContainer"></div>
        </div>
    </div>
</div>

<script>
    let lapangan = JSON.parse(localStorage.getItem('lapangan')) || [];
    let currentId = lapangan.length > 0 ? Math.max(...lapangan.map(l => l.id)) + 1 : 1;
    
    function goToBooking() {
        // Coba beberapa metode
        if(window.location.protocol === 'file:') {
            window.location.href = 'booking.html';
        } else {
            // Untuk Live Server, buka di tab baru
            window.open('booking.html', '_blank');
        }
    }
    
    function getFasilitas() {
        let f = [];
        if(document.getElementById('f_lampu')?.checked) f.push('Lampu');
        if(document.getElementById('f_kursi')?.checked) f.push('Kursi Penonton');
        if(document.getElementById('f_ganti')?.checked) f.push('Ruang Ganti');
        if(document.getElementById('f_toilet')?.checked) f.push('Toilet');
        if(document.getElementById('f_mushola')?.checked) f.push('Mushola');
        if(document.getElementById('f_parkir')?.checked) f.push('Parkir Luas');
        if(document.getElementById('f_cafe')?.checked) f.push('Cafe');
        if(document.getElementById('f_wifi')?.checked) f.push('Free WiFi');
        return f;
    }
    
    function setFasilitas(f) {
        document.getElementById('f_lampu').checked = f.includes('Lampu');
        document.getElementById('f_kursi').checked = f.includes('Kursi Penonton');
        document.getElementById('f_ganti').checked = f.includes('Ruang Ganti');
        document.getElementById('f_toilet').checked = f.includes('Toilet');
        document.getElementById('f_mushola').checked = f.includes('Mushola');
        document.getElementById('f_parkir').checked = f.includes('Parkir Luas');
        document.getElementById('f_cafe').checked = f.includes('Cafe');
        document.getElementById('f_wifi').checked = f.includes('Free WiFi');
    }
    
    document.getElementById('foto')?.addEventListener('input', function() {
        let preview = document.getElementById('previewFoto');
        if(this.value) preview.innerHTML = `<img src="${this.value}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`;
        else preview.innerHTML = '<i class="fas fa-image fa-2x text-muted"></i>';
    });
    
    function updateDashboard() {
        document.getElementById('totalLapangan').innerText = lapangan.length;
        document.getElementById('lapanganTersedia').innerText = lapangan.filter(l => l.status === 'tersedia').length;
        document.getElementById('lapanganMaintenance').innerText = lapangan.filter(l => l.status === 'maintenance').length;
        document.getElementById('totalKapasitas').innerText = lapangan.reduce((s,l) => s + (l.kapasitas || 0), 0);
        
        const tbody = document.getElementById('dashboardTable');
        if(lapangan.length === 0) tbody.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>';
        else {
            tbody.innerHTML = lapangan.map(l => `
                <tr>
                    <td>${l.id}</td><td><strong>${l.nama_lapangan}</strong></td><td>${l.jenis_lantai}</td>
                    <td>${l.kapasitas} org</td>
                    <td><span class="badge-status badge-${l.status === 'tersedia' ? 'tersedia' : (l.status === 'maintenance' ? 'maintenance' : 'tidak-tersedia')}">${l.status}</span></td>
                    <td><button class="btn btn-sm btn-warning" onclick="editLapangan(${l.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="deleteLapangan(${l.id})"><i class="fas fa-trash"></i></button></td>
                </tr>
            `).join('');
        }
    }
    
    function showAllLapangan() {
        const tbody = document.getElementById('allLapanganTable');
        if(lapangan.length === 0) tbody.innerHTML = '<tr><td colspan="8" class="text-center">Tidak ada数据</td></tr>';
        else {
            tbody.innerHTML = lapangan.map(l => `
                <tr>
                    <td>${l.id}</td>
                    <td>${l.foto ? `<img src="${l.foto}" style="width:40px;height:40px;object-fit:cover;border-radius:8px;">` : '<i class="fas fa-image"></i>'}</td>
                    <td><strong>${l.nama_lapangan}</strong></td><td>${l.jenis_lantai}</td><td>${l.kapasitas} org</td>
                    <td>${l.fasilitas ? l.fasilitas.slice(0,2).join(', ') + (l.fasilitas.length > 2 ? '...' : '') : '-'}</td>
                    <td><span class="badge-status badge-${l.status === 'tersedia' ? 'tersedia' : (l.status === 'maintenance' ? 'maintenance' : 'tidak-tersedia')}">${l.status}</span></td>
                    <td><button class="btn btn-sm btn-warning" onclick="editLapangan(${l.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="deleteLapangan(${l.id})"><i class="fas fa-trash"></i></button></td>
                </tr>
            `).join('');
        }
    }
    
    function showGaleri() {
        const container = document.getElementById('galeriContainer');
        if(lapangan.length === 0) container.innerHTML = '<div class="col-12 text-center">Tidak ada data</div>';
        else {
            container.innerHTML = lapangan.map(l => `
                <div class="col-md-4">
                    <div class="lapangan-card">
                        ${l.foto ? `<img src="${l.foto}" alt="${l.nama_lapangan}">` : `<div style="height:180px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image fa-3x text-muted"></i></div>`}
                        <div class="card-body">
                            <h6><strong>${l.nama_lapangan}</strong></h6>
                            <small><i class="fas fa-layer-group"></i> ${l.jenis_lantai}</small><br>
                            <small><i class="fas fa-users"></i> Kapasitas: ${l.kapasitas} orang</small>
                            <div class="mt-2">${l.fasilitas ? l.fasilitas.map(f => `<span class="fasilitas-item">${f}</span>`).join('') : ''}</div>
                            <div class="mt-2"><span class="badge-status badge-${l.status === 'tersedia' ? 'tersedia' : (l.status === 'maintenance' ? 'maintenance' : 'tidak-tersedia')}">${l.status}</span></div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    }
    
    function editLapangan(id) {
        const l = lapangan.find(l => l.id === id);
        if(l) {
            document.getElementById('id').value = l.id;
            document.getElementById('nama_lapangan').value = l.nama_lapangan;
            document.getElementById('jenis_lantai').value = l.jenis_lantai;
            document.getElementById('kapasitas').value = l.kapasitas;
            document.getElementById('foto').value = l.foto || '';
            document.getElementById('status').value = l.status;
            setFasilitas(l.fasilitas || []);
            if(l.foto) document.getElementById('previewFoto').innerHTML = `<img src="${l.foto}" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">`;
            deleteLapangan(id, true);
            showPage('tambah');
        }
    }
    
    function deleteLapangan(id, silent = false) {
        if(silent || confirm('Yakin hapus lapangan ini?')) {
            lapangan = lapangan.filter(l => l.id !== id);
            localStorage.setItem('lapangan', JSON.stringify(lapangan));
            updateDashboard(); showAllLapangan(); showGaleri();
            if(!silent) alert('Lapangan dihapus');
        }
    }
    
    document.getElementById('lapanganForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const newLapangan = {
            id: document.getElementById('id').value ? parseInt(document.getElementById('id').value) : currentId++,
            nama_lapangan: document.getElementById('nama_lapangan').value,
            jenis_lantai: document.getElementById('jenis_lantai').value,
            kapasitas: parseInt(document.getElementById('kapasitas').value),
            fasilitas: getFasilitas(),
            foto: document.getElementById('foto').value || null,
            status: document.getElementById('status').value,
            created_at: new Date().toISOString()
        };
        const idx = lapangan.findIndex(l => l.id === newLapangan.id);
        if(idx !== -1) lapangan[idx] = newLapangan;
        else lapangan.push(newLapangan);
        localStorage.setItem('lapangan', JSON.stringify(lapangan));
        resetForm();
        updateDashboard(); showAllLapangan(); showGaleri();
        alert('Lapangan tersimpan!');
        showPage('daftar');
    });
    
    function resetForm() {
        document.getElementById('lapanganForm').reset();
        document.getElementById('id').value = '';
        document.getElementById('foto').value = '';
        document.getElementById('previewFoto').innerHTML = '<i class="fas fa-image fa-2x text-muted"></i>';
        document.querySelectorAll('.form-check-input').forEach(cb => cb.checked = false);
    }
    
    function showPage(page) {
        document.getElementById('dashboardPage').style.display = 'none';
        document.getElementById('tambahPage').style.display = 'none';
        document.getElementById('daftarPage').style.display = 'none';
        document.getElementById('galeriPage').style.display = 'none';
        if(page === 'dashboard') { document.getElementById('dashboardPage').style.display = 'block'; document.getElementById('pageTitle').innerText = 'Dashboard'; updateDashboard(); }
        else if(page === 'tambah') { document.getElementById('tambahPage').style.display = 'block'; document.getElementById('pageTitle').innerText = 'Tambah Lapangan'; }
        else if(page === 'daftar') { document.getElementById('daftarPage').style.display = 'block'; document.getElementById('pageTitle').innerText = 'Daftar Lapangan'; showAllLapangan(); }
        else if(page === 'galeri') { document.getElementById('galeriPage').style.display = 'block'; document.getElementById('pageTitle').innerText = 'Galeri'; showGaleri(); }
        document.querySelectorAll('.sidebar-menu li').forEach(li => li.classList.remove('active'));
        if(page === 'dashboard') document.querySelectorAll('.sidebar-menu li')[0].classList.add('active');
        else if(page === 'tambah') document.querySelectorAll('.sidebar-menu li')[1].classList.add('active');
        else if(page === 'daftar') document.querySelectorAll('.sidebar-menu li')[2].classList.add('active');
        else if(page === 'galeri') document.querySelectorAll('.sidebar-menu li')[3].classList.add('active');
    }
    
    document.getElementById('menuToggle')?.addEventListener('click', () => document.getElementById('sidebar').classList.toggle('active'));
    
    if(lapangan.length === 0) {
        lapangan = [
            {id:1, nama_lapangan:'Lapangan A', jenis_lantai:'Sintetis', kapasitas:12, fasilitas:['Lampu','Kursi Penonton'], foto:'https://images.unsplash.com/photo-1574623452334-1e0ac2b3cc1b?w=300', status:'tersedia'},
            {id:2, nama_lapangan:'Lapangan B', jenis_lantai:'Vinyl', kapasitas:10, fasilitas:['Lampu','Toilet'], foto:'https://images.unsplash.com/photo-1522778119026-d647f0594c0e?w=300', status:'tersedia'},
            {id:3, nama_lapangan:'Lapangan C', jenis_lantai:'Kayu', kapasitas:14, fasilitas:['Lampu','Mushola','WiFi'], foto:'https://images.unsplash.com/photo-1542751110-97427bbecf20?w=300', status:'maintenance'}
        ];
        localStorage.setItem('lapangan', JSON.stringify(lapangan));
        currentId = 4;
    }
    showPage('dashboard');
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>