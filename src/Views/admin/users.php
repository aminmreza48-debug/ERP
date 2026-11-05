<?php $pageTitle = 'Manajemen User'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card card-custom bg-white p-3 p-md-4 shadow-sm mb-4">
    <div class="page-header-responsive mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1"><i class="bi bi-person-gear me-2 text-primary"></i>Manajemen User & Hak Akses</h4>
            <p class="text-muted small mb-0">Kelola akun pengguna, hak akses role, dan status aktif/non-aktif</p>
        </div>
        <div class="page-actions-responsive">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                <i class="bi bi-person-plus me-1"></i> Tambah User Baru
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
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Terdaftar Sejak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($userList)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada user</td></tr>
                <?php else: foreach ($userList as $u): ?>
                <tr>
                    <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                    <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($u['role']) ?></span>
                    </td>
                    <td>
                        <?php if ($u['status'] === 'active'): ?>
                            <span class="badge bg-success-subtle text-success badge-status">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary badge-status">Non-Aktif</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditUser<?= $u['id'] ?>">
                            <i class="bi bi-pencil"></i> Ubah Role / Status
                        </button>

                        <!-- Modal Edit User -->
                        <div class="modal fade" id="modalEditUser<?= $u['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="/admin/users" method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">Edit User - <?= htmlspecialchars($u['username']) ?></h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($u['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Role Akses</label>
                                                <select name="role" class="form-select" required>
                                                    <option value="mahasiswa" <?= $u['role'] === 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                                                    <option value="bendahara" <?= $u['role'] === 'bendahara' ? 'selected' : '' ?>>Bendahara</option>
                                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                    <option value="pimpinan" <?= $u['role'] === 'pimpinan' ? 'selected' : '' ?>>Pimpinan</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Status Akun</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="active" <?= $u['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="inactive" <?= $u['status'] === 'inactive' ? 'selected' : '' ?>>Non-Aktif (Blokir)</option>
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

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/users" method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Tambah Pengguna Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: bendahara2, dekan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama personil" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="user@siperma.ac.id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password Default</label>
                        <input type="password" name="password" class="form-control" placeholder="Password akun" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Role Akses</label>
                        <select name="role" class="form-select" required>
                            <option value="bendahara">Bendahara</option>
                            <option value="admin">Administrator</option>
                            <option value="pimpinan">Pimpinan</option>
                            <option value="mahasiswa">Mahasiswa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

