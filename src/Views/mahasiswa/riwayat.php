<?php $pageTitle = 'Riwayat Pembayaran'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pembayaran</h4>
            <p class="text-muted small mb-0">Daftar transaksi pembayaran yang pernah Anda lakukan</p>
        </div>
        <div class="page-actions-responsive">
            <a href="/mahasiswa/dashboard" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>No. Tagihan</th>
                    <th>Komponen Biaya</th>
                    <th>Nominal</th>
                    <th>Metode / Bank</th>
                    <th>Tgl Bayar</th>
                    <th>Status</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pembayaranList)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">Belum ada riwayat pembayaran</td></tr>
                <?php else: foreach ($pembayaranList as $p): ?>
                <tr>
                    <td><code><?= htmlspecialchars($p['nomor_transaksi']) ?></code></td>
                    <td><?= htmlspecialchars($p['nomor_tagihan']) ?></td>
                    <td><strong><?= htmlspecialchars($p['nama_biaya']) ?></strong></td>
                    <td class="fw-semibold">Rp <?= number_format($p['nominal_bayar'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($p['metode_pembayaran']) ?> (<?= htmlspecialchars($p['bank_tujuan']) ?>)</td>
                    <td><?= date('d M Y H:i', strtotime($p['tanggal_bayar'])) ?></td>
                    <td>
                        <?php if ($p['status'] === 'diverifikasi'): ?>
                            <span class="badge bg-success-subtle text-success badge-status"><i class="bi bi-check-circle me-1"></i> Diverifikasi</span>
                        <?php elseif ($p['status'] === 'menunggu_verifikasi'): ?>
                            <span class="badge bg-warning-subtle text-warning badge-status"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi</span>
                        <?php elseif ($p['status'] === 'ditolak'): ?>
                            <span class="badge bg-danger-subtle text-danger badge-status"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                        <?php endif; ?>
                    </td>
                    <td><small class="text-muted"><?= htmlspecialchars($p['catatan'] ?? '-') ?></small></td>
                    <td>
                        <?php if ($p['status'] === 'diverifikasi'): ?>
                            <a href="/mahasiswa/cetak?tagihan_id=<?= $p['tagihan_id'] ?>" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-printer"></i> Cetak</a>
                        <?php elseif ($p['status'] === 'ditolak'): ?>
                            <a href="/mahasiswa/upload?tagihan_id=<?= $p['tagihan_id'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-arrow-repeat"></i> Upload Ulang</a>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

