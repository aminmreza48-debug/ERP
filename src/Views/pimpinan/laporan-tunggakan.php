<?php $pageTitle = 'Laporan Tunggakan Mahasiswa'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm mb-4">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Laporan Tunggakan Biaya Mahasiswa</h4>
            <p class="text-muted small mb-0">Daftar tagihan yang belum terbayar atau jatuh tempo</p>
        </div>
        <div class="page-actions-responsive">
            <button onclick="window.print()" class="btn btn-outline-danger btn-sm"><i class="bi bi-printer me-1"></i> Cetak Rekap Tunggakan</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>No. Tagihan</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Program Studi</th>
                    <th>Angkatan</th>
                    <th>Komponen Biaya</th>
                    <th>Jatuh Tempo</th>
                    <th>Nominal Tunggakan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tunggakanList)): ?>
                <tr><td colspan="10" class="text-center text-success py-4"><i class="bi bi-check-circle fs-3 d-block mb-2"></i>Luar biasa! Tidak ada tunggakan mahasiswa saat ini.</td></tr>
                <?php else: $no = 1; foreach ($tunggakanList as $t): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><code><?= htmlspecialchars($t['nomor_tagihan']) ?></code></td>
                    <td><?= htmlspecialchars($t['nim']) ?></td>
                    <td><strong><?= htmlspecialchars($t['nama_mhs']) ?></strong></td>
                    <td><?= htmlspecialchars($t['prodi']) ?></td>
                    <td><?= htmlspecialchars($t['angkatan']) ?></td>
                    <td><?= htmlspecialchars($t['nama_biaya']) ?></td>
                    <td>
                        <?php 
                        $isOverdue = strtotime($t['tanggal_jatuh_tempo']) < time();
                        ?>
                        <span class="<?= $isOverdue ? 'text-danger fw-bold' : '' ?>">
                            <?= date('d/m/Y', strtotime($t['tanggal_jatuh_tempo'])) ?>
                            <?= $isOverdue ? ' (Lewat)' : '' ?>
                        </span>
                    </td>
                    <td class="text-end fw-bold text-danger">Rp <?= number_format($t['nominal'], 0, ',', '.') ?></td>
                    <td>
                        <?php if ($t['status'] === 'ditolak'): ?>
                            <span class="badge bg-danger">Bukti Ditolak</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Belum Bayar</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-danger fw-bold">
                    <td colspan="8" class="text-end">TOTAL OUTSTANDING TUNGGAKAN:</td>
                    <td class="text-end text-danger fs-5">Rp <?= number_format($totalTunggakanNominal ?? 0, 0, ',', '.') ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

