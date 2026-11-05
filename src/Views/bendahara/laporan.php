<?php $pageTitle = 'Laporan Keuangan Bendahara'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm mb-4">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Rekapitulasi Pembayaran</h4>
            <p class="text-muted small mb-0">Laporan pembayaran mahasiswa yang telah terverifikasi sah</p>
        </div>
        <div class="page-actions-responsive">
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i> Cetak / Export PDF</button>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light rounded border">
                <small class="text-muted">Total Transaksi Diverifikasi</small>
                <div class="fs-4 fw-bold text-success"><?= count($laporanList ?? []) ?> Transaksi</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light rounded border">
                <small class="text-muted">Total Penerimaan Dana</small>
                <div class="fs-4 fw-bold text-primary">Rp <?= number_format($totalPenerimaan ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light rounded border">
                <small class="text-muted">Periode</small>
                <div class="fs-6 fw-semibold text-light mt-1">Tahun Akademik 2024/2025</div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal Verifikasi</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Program Studi</th>
                    <th>Komponen Biaya</th>
                    <th>Bank</th>
                    <th>Nominal (Rp)</th>
                    <th>Verifikator</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($laporanList)): ?>
                <tr><td colspan="10" class="text-center text-muted py-4">Belum ada data pembayaran terverifikasi</td></tr>
                <?php else: $no = 1; foreach ($laporanList as $l): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><code><?= htmlspecialchars($l['nomor_transaksi']) ?></code></td>
                    <td><?= date('d/m/Y H:i', strtotime($l['tanggal_verifikasi'])) ?></td>
                    <td><?= htmlspecialchars($l['nim']) ?></td>
                    <td><strong><?= htmlspecialchars($l['nama_mhs']) ?></strong></td>
                    <td><?= htmlspecialchars($l['prodi']) ?></td>
                    <td><?= htmlspecialchars($l['nama_biaya']) ?></td>
                    <td><?= htmlspecialchars($l['bank_tujuan']) ?></td>
                    <td class="text-end fw-semibold">Rp <?= number_format($l['nominal_bayar'], 0, ',', '.') ?></td>
                    <td><small class="badge bg-secondary"><?= htmlspecialchars($l['verifikator_nama'] ?? 'Bendahara') ?></small></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-primary fw-bold">
                    <td colspan="8" class="text-end">TOTAL PENERIMAAN:</td>
                    <td class="text-end">Rp <?= number_format($totalPenerimaan ?? 0, 0, ',', '.') ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

