<?php $pageTitle = 'Dashboard Mahasiswa'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header-responsive">
    <div>
        <h3 class="fw-bold text-light mb-1">Selamat Datang, <?= htmlspecialchars($currentUser['name']) ?></h3>
        <p class="text-muted small mb-0">NIM: <strong><?= htmlspecialchars($mahasiswa['nim'] ?? '-') ?></strong> | Program Studi: <strong><?= htmlspecialchars($mahasiswa['prodi'] ?? '-') ?></strong></p>
    </div>
    <div class="page-actions-responsive">
        <a href="/mahasiswa/tagihan" class="btn btn-primary btn-sm"><i class="bi bi-wallet2 me-1"></i> Bayar Tagihan</a>
    </div>
</div>

<!-- Stats Widgets -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-primary border-4 h-100">
            <div class="text-muted small fw-semibold">Total Tagihan</div>
            <div class="fs-4 fw-bold text-primary">Rp <?= number_format($totalTagihan ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted mt-1"><?= count($tagihanList ?? []) ?> Komponen Biaya</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-success border-4 h-100">
            <div class="text-muted small fw-semibold">Sudah Terbayar (Lunas)</div>
            <div class="fs-4 fw-bold text-success">Rp <?= number_format($totalLunas ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted mt-1">Status Lunas</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-warning border-4 h-100">
            <div class="text-muted small fw-semibold">Menunggu Verifikasi</div>
            <div class="fs-4 fw-bold text-warning">Rp <?= number_format($totalMenunggu ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted mt-1">Dalam proses bendahara</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-danger border-4 h-100">
            <div class="text-muted small fw-semibold">Sisa Tunggakan</div>
            <div class="fs-4 fw-bold text-danger">Rp <?= number_format($totalTunggakan ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted mt-1">Belum dibayar</div>
        </div>
    </div>
</div>

<!-- Active Invoices Table -->
<div class="card card-custom bg-white p-4 shadow-sm mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Daftar Tagihan Semester Ini</h5>
        <a href="/mahasiswa/tagihan" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead>
                <tr>
                    <th>No. Tagihan</th>
                    <th>Komponen Biaya</th>
                    <th>Semester</th>
                    <th>Nominal</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tagihanList)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada tagihan aktif</td></tr>
                <?php else: foreach ($tagihanList as $t): ?>
                <tr>
                    <td><code><?= htmlspecialchars($t['nomor_tagihan']) ?></code></td>
                    <td><strong><?= htmlspecialchars($t['nama_biaya']) ?></strong></td>
                    <td><?= htmlspecialchars($t['semester']) ?></td>
                    <td class="fw-semibold">Rp <?= number_format($t['nominal'], 0, ',', '.') ?></td>
                    <td><?= date('d M Y', strtotime($t['tanggal_jatuh_tempo'])) ?></td>
                    <td>
                        <?php if ($t['status'] === 'lunas'): ?>
                            <span class="badge bg-success-subtle text-success badge-status"><i class="bi bi-check-circle me-1"></i> Lunas</span>
                        <?php elseif ($t['status'] === 'menunggu_verifikasi'): ?>
                            <span class="badge bg-warning-subtle text-warning badge-status"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi</span>
                        <?php elseif ($t['status'] === 'ditolak'): ?>
                            <span class="badge bg-danger-subtle text-danger badge-status"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary badge-status"><i class="bi bi-clock me-1"></i> Belum Bayar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($t['status'] === 'belum_bayar' || $t['status'] === 'ditolak'): ?>
                            <a href="/mahasiswa/upload?tagihan_id=<?= $t['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-upload me-1"></i> Bayar</a>
                        <?php elseif ($t['status'] === 'lunas'): ?>
                            <a href="/mahasiswa/cetak?tagihan_id=<?= $t['id'] ?>" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-printer me-1"></i> Kwitansi</a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-light text-muted" disabled><i class="bi bi-hourglass"></i> Diproses</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

