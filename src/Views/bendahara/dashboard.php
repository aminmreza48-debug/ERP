<?php $pageTitle = 'Dashboard Bendahara'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header-responsive">
    <div>
        <h3 class="fw-bold text-light mb-1">Dashboard Bendahara Keuangan</h3>
        <p class="text-muted small mb-0">Kelola dan verifikasi pembayaran mahasiswa secara real-time</p>
    </div>
    <div class="page-actions-responsive">
        <a href="/bendahara/verifikasi" class="btn btn-warning text-dark fw-semibold btn-sm"><i class="bi bi-patch-check me-1"></i> Antrean Verifikasi (<?= count($pendingList ?? []) ?>)</a>
    </div>
</div>

<!-- Stats -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-warning border-4 h-100">
            <div class="text-muted small fw-semibold">Menunggu Verifikasi</div>
            <div class="fs-4 fw-bold text-warning"><?= count($pendingList ?? []) ?> Transaksi</div>
            <div class="small text-muted mt-1">Perlu tindakan segera</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-success border-4 h-100">
            <div class="text-muted small fw-semibold">Total Terverifikasi</div>
            <div class="fs-4 fw-bold text-success">Rp <?= number_format($totalDiverifikasi ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted mt-1">Dana masuk valid</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-danger border-4 h-100">
            <div class="text-muted small fw-semibold">Pembayaran Ditolak</div>
            <div class="fs-4 fw-bold text-danger"><?= $totalDitolakCount ?? 0 ?> Transaksi</div>
            <div class="small text-muted mt-1">Bukti invalid / ditolak</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card card-custom p-3 bg-white border-start border-info border-4 h-100">
            <div class="text-muted small fw-semibold">Total Target Tagihan</div>
            <div class="fs-4 fw-bold text-info">Rp <?= number_format($totalTarget ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted mt-1">Semua mahasiswa</div>
        </div>
    </div>
</div>

<!-- Pending List -->
<div class="card card-custom bg-white p-4 shadow-sm mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-hourglass-split text-warning me-2"></i>Antrean Verifikasi Pembayaran Masuk</h5>
        <a href="/bendahara/verifikasi" class="btn btn-sm btn-outline-primary">Buka Halaman Verifikasi</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>NIM & Mahasiswa</th>
                    <th>Komponen Biaya</th>
                    <th>Nominal</th>
                    <th>Tanggal Masuk</th>
                    <th>Bukti</th>
                    <th>Aksi Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendingList)): ?>
                <tr><td colspan="7" class="text-center text-success py-4"><i class="bi bi-check2-circle fs-3 d-block mb-2"></i>Tidak ada pembayaran yang menunggu verifikasi</td></tr>
                <?php else: foreach ($pendingList as $p): ?>
                <tr>
                    <td><code><?= htmlspecialchars($p['nomor_transaksi']) ?></code></td>
                    <td>
                        <strong><?= htmlspecialchars($p['nama_mahasiswa']) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($p['nim']) ?> - <?= htmlspecialchars($p['prodi']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($p['nama_biaya']) ?></td>
                    <td class="fw-bold text-primary">Rp <?= number_format($p['nominal_bayar'], 0, ',', '.') ?></td>
                    <td><?= date('d M Y H:i', strtotime($p['tanggal_bayar'])) ?></td>
                    <td>
                        <a href="/uploads/<?= htmlspecialchars($p['bukti_transfer']) ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i> Lihat Bukti</a>
                    </td>
                    <td>
                        <a href="/bendahara/verifikasi" class="btn btn-sm btn-warning"><i class="bi bi-patch-check"></i> Proses</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

