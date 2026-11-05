<?php $pageTitle = 'Kelola Tagihan'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm mb-4">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Manajemen Tagihan Mahasiswa</h4>
            <p class="text-muted small mb-0">Terbitkan tagihan baru secara individual atau serentak per angkatan</p>
        </div>
        <div class="page-actions-responsive">
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalBatchTagihan">
                <i class="bi bi-collection me-1"></i> Terbitkan Tagihan Massal
            </button>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalSingleTagihan">
                <i class="bi bi-plus-circle me-1"></i> Buat Tagihan Individu
            </button>
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
                    <th>No. Tagihan</th>
                    <th>Mahasiswa</th>
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
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada tagihan diterbitkan</td></tr>
                <?php else: foreach ($tagihanList as $t): ?>
                <tr>
                    <td><code><?= htmlspecialchars($t['nomor_tagihan']) ?></code></td>
                    <td>
                        <strong><?= htmlspecialchars($t['nama_mahasiswa']) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($t['nim']) ?> (<?= htmlspecialchars($t['prodi']) ?>)</small>
                    </td>
                    <td><?= htmlspecialchars($t['nama_biaya']) ?></td>
                    <td><?= htmlspecialchars($t['semester']) ?></td>
                    <td class="fw-bold text-primary">Rp <?= number_format($t['nominal'], 0, ',', '.') ?></td>
                    <td><?= date('d/m/Y', strtotime($t['tanggal_jatuh_tempo'])) ?></td>
                    <td>
                        <?php if ($t['status'] === 'lunas'): ?>
                            <span class="badge bg-success-subtle text-success badge-status"><i class="bi bi-check-circle"></i> Lunas</span>
                        <?php elseif ($t['status'] === 'menunggu_verifikasi'): ?>
                            <span class="badge bg-warning-subtle text-warning badge-status"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                        <?php elseif ($t['status'] === 'ditolak'): ?>
                            <span class="badge bg-danger-subtle text-danger badge-status"><i class="bi bi-x-circle"></i> Ditolak</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary badge-status"><i class="bi bi-clock"></i> Belum Bayar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($t['status'] === 'belum_bayar'): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditTagihan<?= $t['id'] ?>">
                            <i class="bi bi-pencil"></i> Ubah
                        </button>

                        <!-- Modal Edit Tagihan -->
                        <div class="modal fade" id="modalEditTagihan<?= $t['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="/admin/tagihan" method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">Ubah Tagihan - <?= htmlspecialchars($t['nomor_tagihan']) ?></h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Nominal Tagihan (Rp)</label>
                                                <input type="number" name="nominal" class="form-control" value="<?= htmlspecialchars($t['nominal']) ?>" min="1000" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Tanggal Jatuh Tempo</label>
                                                <input type="date" name="tanggal_jatuh_tempo" class="form-control" value="<?= htmlspecialchars($t['tanggal_jatuh_tempo']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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

<!-- Modal Single Tagihan -->
<div class="modal fade" id="modalSingleTagihan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/tagihan" method="POST">
                <input type="hidden" name="action" value="create_single">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Buat Tagihan Individu</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Mahasiswa</label>
                        <select name="mahasiswa_id" class="form-select" required>
                            <option value="">-- Pilih Mahasiswa --</option>
                            <?php foreach ($mahasiswaList as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nim']) ?> - <?= htmlspecialchars($m['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jenis Biaya</label>
                        <select name="jenis_biaya_id" class="form-select" required>
                            <option value="">-- Pilih Jenis Biaya --</option>
                            <?php foreach ($biayaList as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['kode']) ?> - <?= htmlspecialchars($b['nama']) ?> (Rp <?= number_format($b['nominal_default'], 0, ',', '.') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Semester</label>
                        <input type="text" name="semester" class="form-control" value="2024/2025 Ganjil" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control" placeholder="Kosongkan jika sesuai standar biaya" min="1000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tanggal Jatuh Tempo</label>
                        <input type="date" name="tanggal_jatuh_tempo" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Terbitkan Tagihan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Batch Tagihan -->
<div class="modal fade" id="modalBatchTagihan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/tagihan" method="POST">
                <input type="hidden" name="action" value="create_batch">
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title fw-bold">Penerbitan Tagihan Massal</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jenis Biaya</label>
                        <select name="jenis_biaya_id" class="form-select" required>
                            <?php foreach ($biayaList as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['kode']) ?> - <?= htmlspecialchars($b['nama']) ?> (Rp <?= number_format($b['nominal_default'], 0, ',', '.') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Semester Target</label>
                        <input type="text" name="semester" class="form-control" value="2024/2025 Ganjil" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter Program Studi (Opsional)</label>
                        <select name="prodi" class="form-select">
                            <option value="">-- Semua Program Studi --</option>
                            <option value="Teknik Informatika">Teknik Informatika</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                            <option value="Teknik Elektro">Teknik Elektro</option>
                            <option value="Akuntansi">Akuntansi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Filter Angkatan (Opsional)</label>
                        <input type="text" name="angkatan" class="form-control" placeholder="Contoh: 2024 (kosongkan untuk semua)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Generate Batch Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

