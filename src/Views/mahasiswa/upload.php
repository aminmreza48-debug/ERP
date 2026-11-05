<?php $pageTitle = 'Upload Bukti Pembayaran'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-custom p-4 bg-white shadow-sm">
            <h4 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Upload Bukti Pembayaran</h4>

            <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger py-2 small"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>
            <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success py-2 small"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>

            <div class="card bg-light p-3 mb-4 border-0">
                <div class="small text-muted mb-1">Rincian Tagihan:</div>
                <div class="fw-bold fs-5 text-dark"><?= htmlspecialchars($tagihan['nama_biaya'] ?? 'Tagihan Kuliah') ?></div>
                <div class="text-muted small">No. Tagihan: <code><?= htmlspecialchars($tagihan['nomor_tagihan'] ?? '-') ?></code></div>
                <div class="text-muted small">Semester: <?= htmlspecialchars($tagihan['semester'] ?? '-') ?></div>
                <div class="fw-bold text-primary fs-4 mt-2">Rp <?= number_format($tagihan['nominal'] ?? 0, 0, ',', '.') ?></div>
            </div>

            <form action="/mahasiswa/upload" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tagihan_id" value="<?= htmlspecialchars($tagihan['id'] ?? '') ?>">
                <input type="hidden" name="nominal_bayar" value="<?= htmlspecialchars($tagihan['nominal'] ?? 0) ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="form-select" required>
                        <option value="Transfer Bank">Transfer Bank / Virtual Account</option>
                        <option value="Mobile Banking">Mobile Banking (m-Banking)</option>
                        <option value="ATM">ATM Transfer</option>
                        <option value="Teller Bank">Setoran Tunai via Teller</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Bank Tujuan</label>
                    <select name="bank_tujuan" class="form-select" required>
                        <option value="Bank Mandiri">Bank Mandiri (Rek: 137-00-1234567-8 a/n SIPERMA)</option>
                        <option value="Bank BNI">Bank BNI (Rek: 023-456-7890 a/n SIPERMA)</option>
                        <option value="Bank BRI">Bank BRI (Rek: 0012-01-001234-50-1 a/n SIPERMA)</option>
                        <option value="Bank BCA">Bank BCA (Rek: 829-012-3456 a/n SIPERMA)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small">File Bukti Transfer (JPG, PNG, PDF maks. 2MB)</label>
                    <input type="file" name="bukti_transfer" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    <div class="form-text small">Pastikan foto struk / screenshot transaksi terlihat jelas dan tidak buram.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-send me-1"></i> Kirim Bukti Pembayaran</button>
                    <a href="/mahasiswa/dashboard" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

