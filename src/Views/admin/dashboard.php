<?php $pageTitle = 'Dashboard Administrator'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header-responsive">
    <div>
        <h3 class="fw-bold text-light mb-1">Dashboard Administrator SIPERMA</h3>
        <p class="text-muted small mb-0">Pusat kendali master data, tagihan mahasiswa, dan hak akses pengguna</p>
    </div>
    <div class="page-actions-responsive">
        <a href="/admin/mahasiswa" class="btn btn-outline-primary btn-sm"><i class="bi bi-person-plus me-1"></i> Data Mahasiswa</a>
        <a href="/admin/tagihan" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i> Buat Tagihan Baru</a>
    </div>
</div>

<!-- Stats Overview -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-primary border-4 h-100">
            <div class="text-muted small fw-semibold">Total Mahasiswa Terdaftar</div>
            <div class="fs-4 fw-bold text-primary"><?= $totalMhs ?? 0 ?> Mahasiswa</div>
            <div class="small text-muted mt-1">Status aktif</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-info border-4 h-100">
            <div class="text-muted small fw-semibold">Master Jenis Biaya</div>
            <div class="fs-4 fw-bold text-info"><?= $totalBiaya ?? 0 ?> Komponen</div>
            <div class="small text-muted mt-1">UKT, Praktikum, Wisuda</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-warning border-4 h-100">
            <div class="text-muted small fw-semibold">Total Tagihan Diterbitkan</div>
            <div class="fs-4 fw-bold text-warning"><?= $totalTagihanCount ?? 0 ?> Tagihan</div>
            <div class="small text-muted mt-1">Rp <?= number_format($totalTagihanNominal ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-success border-4 h-100">
            <div class="text-muted small fw-semibold">User Sistem Aktif</div>
            <div class="fs-4 fw-bold text-success"><?= $totalUsers ?? 0 ?> Pengguna</div>
            <div class="small text-muted mt-1">4 Role terkelola</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3">
    <div class="col-md-6">
        <div class="card card-custom p-4 bg-white shadow-sm h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-gear-wide-connected text-primary me-2"></i>Modul Manajemen Utama</h5>
            <div class="list-group list-group-flush">
                <a href="/admin/mahasiswa" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">CRUD Data Mahasiswa</div>
                        <small class="text-muted">Tambah, edit, hapus, dan kelola biodata mahasiswa serta NIM</small>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="/admin/biaya" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">CRUD Master Jenis Biaya</div>
                        <small class="text-muted">Kelola tarif UKT, biaya praktikum, ujian, dan wisuda</small>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="/admin/tagihan" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">Generator Tagihan (Single & Massal)</div>
                        <small class="text-muted">Terbitkan tagihan per mahasiswa atau serentak per prodi/angkatan</small>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="/admin/users" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                    <div>
                        <div class="fw-bold">Manajemen User & Role Access</div>
                        <small class="text-muted">Kelola hak akses untuk Admin, Bendahara, Mahasiswa, dan Pimpinan</small>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-custom p-4 bg-white shadow-sm h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-shield-check text-success me-2"></i>Aktivitas Audit Log Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-sm small align-middle">
                    <thead><tr><th>Waktu</th><th>Aksi</th><th>Modul</th><th>User</th></tr></thead>
                    <tbody>
                        <?php if (empty($auditLogs)): ?>
                        <tr><td colspan="4" class="text-muted text-center py-3">Belum ada audit log</td></tr>
                        <?php else: foreach ($auditLogs as $log): ?>
                        <tr>
                            <td><?= date('d/m H:i', strtotime($log['created_at'])) ?></td>
                            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($log['action']) ?></span></td>
                            <td><?= htmlspecialchars($log['module']) ?></td>
                            <td><?= htmlspecialchars($log['user_id'] ? 'User #' . $log['user_id'] : 'System') ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

