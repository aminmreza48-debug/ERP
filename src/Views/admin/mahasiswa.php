<?php $pageTitle = 'Kelola Mahasiswa'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm mb-4">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-people me-2 text-primary"></i>Manajemen Data Mahasiswa</h4>
            <p class="text-muted small mb-0">Kelola master data mahasiswa terdaftar</p>
        </div>
        <div class="page-actions-responsive">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMhs">
                <i class="bi bi-person-plus me-1"></i> Tambah Mahasiswa
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
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Program Studi</th>
                    <th>Angkatan</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mahasiswaList)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data mahasiswa</td></tr>
                <?php else: foreach ($mahasiswaList as $m): ?>
                <tr>
                    <td><code><?= htmlspecialchars($m['nim']) ?></code></td>
                    <td><strong><?= htmlspecialchars($m['nama']) ?></strong></td>
                    <td><?= htmlspecialchars($m['prodi']) ?></td>
                    <td><?= htmlspecialchars($m['angkatan']) ?></td>
                    <td><?= htmlspecialchars($m['email'] ?? '-') ?></td>
                    <td>
                        <span class="badge bg-success-subtle text-success badge-status"><?= htmlspecialchars($m['status']) ?></span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditMhs<?= $m['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="/admin/mahasiswa" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEditMhs<?= $m['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="/admin/mahasiswa" method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">Edit Mahasiswa - <?= htmlspecialchars($m['nim']) ?></h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Nama Mahasiswa</label>
                                                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($m['nama']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Program Studi</label>
                                                <input type="text" name="prodi" class="form-control" value="<?= htmlspecialchars($m['prodi']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Angkatan</label>
                                                <input type="text" name="angkatan" class="form-control" value="<?= htmlspecialchars($m['angkatan']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($m['email'] ?? '') ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="aktif" <?= $m['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="cuti" <?= $m['status'] === 'cuti' ? 'selected' : '' ?>>Cuti</option>
                                                    <option value="lulus" <?= $m['status'] === 'lulus' ? 'selected' : '' ?>>Lulus</option>
                                                    <option value="drop_out" <?= $m['status'] === 'drop_out' ? 'selected' : '' ?>>Drop Out</option>
                                                </select>
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
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Mahasiswa -->
<div class="modal fade" id="modalTambahMhs" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/mahasiswa" method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Tambah Mahasiswa Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">NIM (Nomor Induk Mahasiswa)</label>
                        <input type="text" name="nim" class="form-control" placeholder="Contoh: 202401004" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama lengkap mahasiswa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Program Studi</label>
                        <select name="prodi" class="form-select" required>
                            <option value="Teknik Informatika">Teknik Informatika</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                            <option value="Teknik Elektro">Teknik Elektro</option>
                            <option value="Akuntansi">Akuntansi</option>
                            <option value="Manajemen">Manajemen</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tahun Angkatan</label>
                        <input type="text" name="angkatan" class="form-control" value="2024" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Mahasiswa</label>
                        <input type="email" name="email" class="form-control" placeholder="student@siperma.ac.id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nomor Telepon / WA</label>
                        <input type="text" name="telepon" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Mahasiswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

