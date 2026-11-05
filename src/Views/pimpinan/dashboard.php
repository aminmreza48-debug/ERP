<?php $pageTitle = 'Dashboard Eksekutif Pimpinan'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header-responsive">
    <div>
        <h3 class="fw-bold text-light mb-1">Executive Dashboard Pimpinan</h3>
        <p class="text-muted small mb-0">Pemantauan penerimaan kas mahasiswa, rasio pelunasan, dan status tunggakan</p>
    </div>
    <div class="page-actions-responsive">
        <a href="/pimpinan/statistik" class="btn btn-outline-primary btn-sm"><i class="bi bi-pie-chart me-1"></i> Grafik Statistik</a>
        <a href="/pimpinan/laporan-tunggakan" class="btn btn-danger btn-sm"><i class="bi bi-exclamation-octagon me-1"></i> Rekap Tunggakan</a>
    </div>
</div>

<!-- Key Performance Indicators (KPIs) -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-primary border-4 h-100">
            <div class="text-muted small fw-semibold">Target Tagihan Akademik</div>
            <div class="fs-4 fw-bold text-primary">Rp <?= number_format($kpi['total_tagihan'] ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted mt-1">100% dari seluruh mahasiswa</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-success border-4 h-100">
            <div class="text-muted small fw-semibold">Realisasi Penerimaan (Kas)</div>
            <div class="fs-4 fw-bold text-success">Rp <?= number_format($kpi['total_terbayar'] ?? 0, 0, ',', '.') ?></div>
            <div class="small text-success mt-1 fw-bold"><?= $kpi['persentase_realisasi'] ?? 0 ?>% Terkumpul</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-danger border-4 h-100">
            <div class="text-muted small fw-semibold">Outstanding / Tunggakan</div>
            <div class="fs-4 fw-bold text-danger">Rp <?= number_format($kpi['total_tunggakan'] ?? 0, 0, ',', '.') ?></div>
            <div class="small text-danger mt-1 fw-bold"><?= $kpi['persentase_tunggakan'] ?? 0 ?>% Belum Terbayar</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-warning border-4 h-100">
            <div class="text-muted small fw-semibold">Proses Verifikasi (Pending)</div>
            <div class="fs-4 fw-bold text-warning">Rp <?= number_format($kpi['total_menunggu'] ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted mt-1">Menunggu approval</div>
        </div>
    </div>
</div>

<!-- Chart & Progress Overview -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card card-custom p-3 p-md-4 bg-white shadow-sm h-100">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                <h5 class="fw-bold mb-0 fs-6 fs-md-5"><i class="bi bi-bar-chart-line text-primary me-2"></i>Komparasi Target vs Realisasi Kas</h5>
                <span class="badge bg-light text-light border">Semester Ganjil 2024/2025</span>
            </div>
            <div style="position: relative; width: 100%; min-height: 220px;">
                <canvas id="financeChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card card-custom p-3 p-md-4 bg-white shadow-sm h-100">
            <h5 class="fw-bold mb-3 fs-6 fs-md-5"><i class="bi bi-pie-chart text-info me-2"></i>Proporsi Status Tagihan</h5>
            <div style="position: relative; width: 100%; max-height: 220px; display: flex; justify-content: center;">
                <canvas id="statusPieChart"></canvas>
            </div>
            <div class="mt-3 text-center small text-muted">
                Tingkat efektivitas penagihan periode ini
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bar Chart
    const ctxBar = document.getElementById('financeChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Target Akademik', 'Realisasi Kas Masuk', 'Tunggakan Mahasiswa'],
            datasets: [{
                label: 'Nominal (Rp)',
                data: [
                    <?= (float)($kpi['total_tagihan'] ?? 0) ?>,
                    <?= (float)($kpi['total_terbayar'] ?? 0) ?>,
                    <?= (float)($kpi['total_tunggakan'] ?? 0) ?>
                ],
                backgroundColor: ['#3b82f6', '#10b981', '#ef4444'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Pie Chart
    const ctxPie = document.getElementById('statusPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Tunggakan', 'Menunggu Verifikasi'],
            datasets: [{
                data: [
                    <?= (float)($kpi['total_terbayar'] ?? 0) ?>,
                    <?= (float)($kpi['total_tunggakan'] ?? 0) ?>,
                    <?= (float)($kpi['total_menunggu'] ?? 0) ?>
                ],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b']
            }]
        },
        options: { responsive: true }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

