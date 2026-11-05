<?php $pageTitle = 'Statistik & Analisis Keuangan'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm mb-4">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-pie-chart me-2 text-primary"></i>Statistik & Analisis Keuangan Mahasiswa</h4>
            <p class="text-muted small mb-0">Visualisasi data tren pembayaran per periode, per prodi, dan per jenis biaya</p>
        </div>
        <div class="page-actions-responsive">
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Cetak Analisis</button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
                <h6 class="fw-bold text-center mb-3">Distribusi Penerimaan per Komponen Biaya</h6>
                <canvas id="biayaDistChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded p-3 bg-light">
                <h6 class="fw-bold text-center mb-3">Rasio Realisasi per Program Studi</h6>
                <canvas id="prodiDistChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('biayaDistChart'), {
        type: 'pie',
        data: {
            labels: ['UKT Golongan 1', 'UKT Golongan 2', 'UKT Golongan 3', 'Praktikum', 'Wisuda'],
            datasets: [{
                data: [2500000, 4500000, 6000000, 750000, 1500000],
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899']
            }]
        },
        options: { responsive: true }
    });

    new Chart(document.getElementById('prodiDistChart'), {
        type: 'bar',
        data: {
            labels: ['Teknik Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Akuntansi'],
            datasets: [{
                label: 'Realisasi Kas (Juta Rp)',
                data: [4.5, 2.5, 0, 0],
                backgroundColor: '#3b82f6',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

