<?php $pageTitle = 'Login - SIPERMA ERP'; require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
/* Mobile optimization */
@media (max-width: 576px) {
    .login-container-card {
        padding: 20px 16px !important;
        border-radius: 14px !important;
        margin: 10px 0 !important;
    }
    .brand-logo-icon {
        width: 54px !important;
        height: 54px !important;
        padding: 10px !important;
        font-size: 1.5rem !important;
    }
    .brand-title {
        font-size: 1.4rem !important;
    }
    .brand-subtitle {
        font-size: 0.75rem !important;
    }
    .demo-grid-box {
        padding: 8px 10px !important;
    }
    .demo-role-badge {
        font-size: 10px !important;
    }
    .demo-user-text {
        font-size: 12px !important;
    }
    .demo-pass-text {
        font-size: 10px !important;
    }
    .form-control {
        font-size: 15px !important;
        padding: 8px 12px !important;
    }
    .input-group-text {
        padding: 8px 12px !important;
    }
    .btn-login-main {
        padding: 10px !important;
        font-size: 14px !important;
    }
}

.demo-card-item {
    background: #090e18;
    border: 1px solid #1e293b;
    border-radius: 10px;
    padding: 10px 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: left;
    width: 100%;
}
.demo-card-item:hover, .demo-card-item:active {
    background: #111a2e;
    border-color: #38bdf8;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(56, 189, 248, 0.2);
}
</style>

<div class="row justify-content-center align-items-center my-auto" style="min-height: 80vh;">
    <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 px-2 px-sm-3">
        
        <!-- Login Card -->
        <div class="card card-custom login-container-card p-4 p-sm-5 shadow-lg" style="background: #0d1322; border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 20px; box-shadow: 0 15px 45px rgba(0, 0, 0, 0.7);">
            
            <!-- Logo & Brand Header -->
            <div class="text-center mb-3 mb-sm-4">
                <div class="d-inline-flex p-3 rounded-circle mb-2 brand-logo-icon" style="background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(15, 23, 42, 0) 70%); border: 1px solid rgba(56, 189, 248, 0.4); box-shadow: 0 0 25px rgba(56, 189, 248, 0.25);">
                    <i class="bi bi-wallet2 fs-2 text-info"></i>
                </div>
                <h2 class="fw-bold text-white tracking-wide mb-1 brand-title" style="letter-spacing: 0.5px;">SIPERMA <span class="text-primary">ERP</span></h2>
                <p class="text-muted small mb-0 brand-subtitle">Sistem Informasi Pembayaran & Keuangan Mahasiswa</p>
            </div>

            <!-- Error Notification -->
            <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-3" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5;">
                <i class="bi bi-exclamation-octagon-fill fs-6"></i>
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="/login" method="POST" id="loginForm">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-light mb-1">Username / Email / NIM</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" id="inputIdentifier" name="identifier" class="form-control" placeholder="admin atau mhs2024001" required autofocus value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3 mb-sm-4">
                    <label class="form-label small fw-semibold text-light mb-1">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold fs-6 mb-2 btn-login-main">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                </button>
            </form>

            <!-- Test Center Direct Link -->
            <div class="mt-1 text-center">
                <a href="/testing" class="btn btn-sm btn-outline-info w-100 py-2 fw-semibold" style="border-color: rgba(56, 189, 248, 0.4); background: rgba(56, 189, 248, 0.05); font-size: 13px;">
                    <i class="bi bi-cpu-fill me-1"></i> Buka SQA Test Automation Center
                </a>
            </div>

            <!-- Quick Auto-Fill Demo Accounts (Mobile-Optimized Grid) -->
            <div class="mt-3 mt-sm-4 pt-3 border-top" style="border-color: rgba(51, 65, 85, 0.5) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-semibold text-light">Akun Demo Pengujian:</span>
                    <span class="badge bg-secondary-subtle text-info" style="font-size: 10px;">Sentuh untuk isi</span>
                </div>
                
                <!-- 2x2 Grid (No Horizontal Scrollbar, Perfect for Mobile Screens) -->
                <div class="row g-2">
                    <!-- Mahasiswa -->
                    <div class="col-6">
                        <div class="demo-card-item demo-grid-box" onclick="fillCreds('mhs2024001', 'mhs123')" role="button" title="Sentuh untuk login sebagai Mahasiswa">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-info bg-opacity-25 text-info demo-role-badge">Mahasiswa</span>
                                <i class="bi bi-check2-circle text-muted small"></i>
                            </div>
                            <div class="fw-bold font-monospace text-light demo-user-text text-truncate">mhs2024001</div>
                            <div class="text-muted font-monospace demo-pass-text">mhs123</div>
                        </div>
                    </div>

                    <!-- Bendahara -->
                    <div class="col-6">
                        <div class="demo-card-item demo-grid-box" onclick="fillCreds('bendahara', 'bendahara123')" role="button" title="Sentuh untuk login sebagai Bendahara">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-warning bg-opacity-25 text-warning demo-role-badge">Bendahara</span>
                                <i class="bi bi-check2-circle text-muted small"></i>
                            </div>
                            <div class="fw-bold font-monospace text-light demo-user-text text-truncate">bendahara</div>
                            <div class="text-muted font-monospace demo-pass-text">bendahara123</div>
                        </div>
                    </div>

                    <!-- Admin -->
                    <div class="col-6">
                        <div class="demo-card-item demo-grid-box" onclick="fillCreds('admin', 'admin123')" role="button" title="Sentuh untuk login sebagai Admin">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-danger bg-opacity-25 text-danger demo-role-badge">Admin</span>
                                <i class="bi bi-check2-circle text-muted small"></i>
                            </div>
                            <div class="fw-bold font-monospace text-light demo-user-text text-truncate">admin</div>
                            <div class="text-muted font-monospace demo-pass-text">admin123</div>
                        </div>
                    </div>

                    <!-- Pimpinan -->
                    <div class="col-6">
                        <div class="demo-card-item demo-grid-box" onclick="fillCreds('pimpinan', 'pimpinan123')" role="button" title="Sentuh untuk login sebagai Pimpinan">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-success bg-opacity-25 text-success demo-role-badge">Pimpinan</span>
                                <i class="bi bi-check2-circle text-muted small"></i>
                            </div>
                            <div class="fw-bold font-monospace text-light demo-user-text text-truncate">pimpinan</div>
                            <div class="text-muted font-monospace demo-pass-text">pimpinan123</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
function fillCreds(username, password) {
    const userInput = document.getElementById('inputIdentifier');
    const passInput = document.getElementById('inputPassword');
    userInput.value = username;
    passInput.value = password;
    userInput.classList.add('border-primary');
    passInput.classList.add('border-primary');
    setTimeout(() => {
        userInput.classList.remove('border-primary');
        passInput.classList.remove('border-primary');
    }, 800);
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
