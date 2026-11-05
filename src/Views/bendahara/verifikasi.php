<?php $pageTitle = 'Verifikasi Pembayaran'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm mb-4">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-patch-check me-2 text-warning"></i>Verifikasi Bukti Pembayaran</h4>
            <p class="text-muted small mb-0">Periksa bukti transfer dan sesuaikan dengan mutasi bank sebelum melakukan persetujuan</p>
        </div>
        <div class="page-actions-responsive">
            <a href="/bendahara/dashboard" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <?php if (!empty($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> <?= htmlspecialchars($successMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
        <i class="bi bi-exclamation-octagon-fill me-1"></i> <?= htmlspecialchars($errorMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Mahasiswa</th>
                    <th>Tagihan / Komponen</th>
                    <th>Nominal</th>
                    <th>Bank & Waktu</th>
                    <th>Bukti Transfer</th>
                    <th>Status Saat Ini</th>
                    <th>Aksi Keputusan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pembayaranList)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data pembayaran yang ditemukan</td></tr>
                <?php else: foreach ($pembayaranList as $p): ?>
                <tr>
                    <td><code><?= htmlspecialchars($p['nomor_transaksi']) ?></code></td>
                    <td>
                        <strong><?= htmlspecialchars($p['nama_mahasiswa']) ?></strong><br>
                        <small class="text-muted">NIM: <?= htmlspecialchars($p['nim']) ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($p['nama_biaya']) ?><br>
                        <small class="text-muted">No: <?= htmlspecialchars($p['nomor_tagihan']) ?></small>
                    </td>
                    <td class="fw-bold text-primary">Rp <?= number_format($p['nominal_bayar'], 0, ',', '.') ?></td>
                    <td>
                        <?= htmlspecialchars($p['bank_tujuan']) ?><br>
                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($p['tanggal_bayar'])) ?></small>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalBukti<?= $p['id'] ?>">
                            <i class="bi bi-image"></i> Buka Bukti
                        </button>

                        <!-- Modal Preview Bukti -->
                        <div class="modal fade" id="modalBukti<?= $p['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold">Bukti Transfer - <?= htmlspecialchars($p['nomor_transaksi']) ?></h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center bg-light">
                                        <div class="p-3 border rounded bg-white">
                                            <i class="bi bi-file-earmark-image fs-1 text-primary d-block mb-2"></i>
                                            <p class="small text-muted mb-2">File: <?= htmlspecialchars($p['bukti_transfer']) ?></p>
                                            <a href="/uploads/<?= htmlspecialchars($p['bukti_transfer']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> Buka File di Tab Baru
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if ($p['status'] === 'diverifikasi'): ?>
                            <span class="badge bg-success-subtle text-success badge-status"><i class="bi bi-check-circle"></i> Diverifikasi</span>
                        <?php elseif ($p['status'] === 'menunggu_verifikasi'): ?>
                            <span class="badge bg-warning-subtle text-warning badge-status"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                        <?php elseif ($p['status'] === 'ditolak'): ?>
                            <span class="badge bg-danger-subtle text-danger badge-status"><i class="bi bi-x-circle"></i> Ditolak</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($p['status'] === 'menunggu_verifikasi'): ?>
                        <div class="d-flex gap-1">
                            <!-- Approve Form -->
                            <form action="/bendahara/verifikasi" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memverifikasi dan menyetujui pembayaran ini?')">
                                <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="catatan" value="Pembayaran diverifikasi dan sesuai.">
                                <button type="submit" class="btn btn-sm btn-success" title="Setujui Pembayaran"><i class="bi bi-check-lg"></i> Setuju</button>
                            </form>

                            <!-- Reject Modal Trigger -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalReject<?= $p['id'] ?>" title="Tolak Pembayaran">
                                <i class="bi bi-x-lg"></i> Tolak
                            </button>
                        </div>

                        <!-- Modal Tolak -->
                        <div class="modal fade" id="modalReject<?= $p['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="/bendahara/verifikasi" method="POST">
                                        <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <div class="modal-header bg-danger text-white">
                                            <h6 class="modal-title fw-bold"><i class="bi bi-x-circle me-1"></i> Tolak Pembayaran</h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="small text-muted">Mahasiswa: <strong><?= htmlspecialchars($p['nama_mahasiswa']) ?> (<?= htmlspecialchars($p['nim']) ?>)</strong></p>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Catatan / Alasan Penolakan (Wajib Diisi):</label>
                                                <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Bukti transfer buram, mutasi rekening belum masuk, dll." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-danger">Konfirmasi Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                            <small class="text-muted"><?= htmlspecialchars($p['catatan'] ?? 'Sudah diproses') ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

