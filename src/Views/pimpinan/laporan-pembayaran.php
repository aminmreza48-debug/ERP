<?php $pageTitle = 'Laporan Realisasi Pembayaran'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm mb-4">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Laporan Realisasi Pembayaran Mahasiswa</h4>
            <p class="text-muted small mb-0">Rekapitulasi penerimaan kas institusi dari pembayaran mahasiswa yang telah diverifikasi</p>
        </div>
        <div class="page-actions-responsive">
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i> Cetak Laporan Eksekutif</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal Approval</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Program Studi</th>
                    <th>Komponen Biaya</th>
                    <th>Nominal Terbayar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($laporanList)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data pembayaran diverifikasi</td></tr>
                <?php else: $no = 1; foreach ($laporanList as $l): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><code><?= htmlspecialchars($l['nomor_transaksi']) ?></code></td>
                    <td><?= date('d/m/Y', strtotime($l['tanggal_verifikasi'])) ?></td>
                    <td><?= htmlspecialchars($l['nim']) ?></td>
                    <td><strong><?= htmlspecialchars($l['nama_mhs']) ?></strong></td>
                    <td><?= htmlspecialchars($l['prodi']) ?></td>
                    <td><?= htmlspecialchars($l['nama_biaya']) ?></td>
                    <td class="text-end fw-bold text-success">Rp <?= number_format($l['nominal_bayar'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-success fw-bold">
                    <td colspan="7" class="text-end">TOTAL REALISASI DITERIMA:</td>
                    <td class="text-end text-success fs-5">Rp <?= number_format($totalRealisasi ?? 0, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

