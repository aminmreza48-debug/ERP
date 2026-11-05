<?php $pageTitle = 'Kelola Jenis Biaya'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm mb-4">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-cash-stack me-2 text-primary"></i>Master Jenis Biaya Kuliah</h4>
            <p class="text-muted small mb-0">Kelola tarif standar komponen biaya akademik</p>
        </div>
        <div class="page-actions-responsive">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBiaya">
                <i class="bi bi-plus-circle me-1"></i> Tambah Jenis Biaya
            </button>
        </div>
    </div>

    <?php if (!empty($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> <?= htmlspecialchars($successMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead>
                <tr>
                    <th>Kode Biaya</th>
                    <th>Nama Komponen Biaya</th>
                    <th>Nominal Standar</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($biayaList)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada jenis biaya</td></tr>
                <?php else: foreach ($biayaList as $b): ?>
                <tr>
                    <td><code><?= htmlspecialchars($b['kode']) ?></code></td>
                    <td><strong><?= htmlspecialchars($b['nama']) ?></strong></td>
                    <td class="fw-bold text-primary">Rp <?= number_format($b['nominal_default'], 0, ',', '.') ?></td>
                    <td><small class="text-muted"><?= htmlspecialchars($b['keterangan'] ?? '-') ?></small></td>
                    <td>
                        <span class="badge bg-success-subtle text-success badge-status"><?= htmlspecialchars($b['status']) ?></span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditBiaya<?= $b['id'] ?>">
                            <i class="bi bi-pencil"></i> Edit
                        </button>

                        <!-- Modal Edit Biaya -->
                        <div class="modal fade" id="modalEditBiaya<?= $b['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="/admin/biaya" method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">Edit Jenis Biaya - <?= htmlspecialchars($b['kode']) ?></h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Nama Komponen</label>
                                                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($b['nama']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Nominal Standar (Rp)</label>
                                                <input type="number" name="nominal_default" class="form-control" value="<?= htmlspecialchars($b['nominal_default']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Keterangan</label>
                                                <textarea name="keterangan" class="form-control" rows="2"><?= htmlspecialchars($b['keterangan'] ?? '') ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="aktif" <?= $b['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="nonaktif" <?= $b['status'] === 'nonaktif' ? 'selected' : '' ?>>Non-Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Biaya -->
<div class="modal fade" id="modalTambahBiaya" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/biaya" method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Tambah Komponen Biaya Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode Biaya</label>
                        <input type="text" name="kode" class="form-control" placeholder="Contoh: SKS-01, PKL-01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Komponen Biaya</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Biaya Magang / PKL" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nominal Standar (Rp)</label>
                        <input type="number" name="nominal_default" class="form-control" placeholder="500000" min="1000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Deskripsi komponen biaya"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Biaya</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

