<?php $pageTitle = 'Login - SIPERMA ERP'; require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
/* Responsive optimization */
@media (max-width: 576px) {
    .login-container-card {
        padding: 24px 18px !important;
        border-radius: 16px !important;
        margin: 10px 0 !important;
    }
    .brand-logo-icon {
        width: 52px !important;
        height: 52px !important;
        padding: 10px !important;
        font-size: 1.4rem !important;
    }
    .brand-title {
        font-size: 1.35rem !important;
    }
    .brand-subtitle {
        font-size: 0.75rem !important;
    }
    .demo-card-item {
        padding: 10px 10px !important;
    }
    .demo-role-badge {
        font-size: 11px !important;
    }
    .demo-action-text {
        font-size: 11px !important;
    }
    .form-control {
        font-size: 15px !important;
        padding: 9px 12px !important;
    }
    .input-group-text {
        padding: 9px 12px !important;
    }
    .btn-login-main {
        padding: 10px !important;
        font-size: 14px !important;
    }
}

.demo-card-item {
    background: #090e18;
    border: 1px solid #1e293b;
    border-radius: 12px;
    padding: 12px 14px;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    text-align: left;
    width: 100%;
    position: relative;
    overflow: hidden;
}
.demo-card-item:hover, .demo-card-item:active {
    background: #111a2e;
    border-color: #38bdf8;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(56, 189, 248, 0.25);
}
.demo-card-item:hover .demo-arrow-icon {
    transform: translateX(4px);
    color: #38bdf8 !important;
}
.demo-arrow-icon {
    transition: transform 0.2s ease, color 0.2s ease;
}
.quick-login-divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: #64748b;
    font-size: 12px;
    margin: 18px 0 14px;
}
.quick-login-divider::before, .quick-login-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid rgba(51, 65, 85, 0.6);
}
.quick-login-divider:not(:empty)::before {
    margin-right: .75em;
}
.quick-login-divider:not(:empty)::after {
    margin-left: .75em;
}
</style>

<div class="row justify-content-center align-items-center my-auto" style="min-height: 82vh;">
    <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 px-2 px-sm-3">
        
        <!-- Login Card -->
        <div class="card card-custom login-container-card p-4 p-sm-5 shadow-lg" style="background: #0d1322; border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 22px; box-shadow: 0 15px 45px rgba(0, 0, 0, 0.7);">
            
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
            <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-3" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; border-radius: 10px;">
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
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-semibold text-light mb-0">Password</label>
                        <button type="button" class="btn btn-link p-0 text-decoration-none text-muted small" onclick="togglePasswordVisibility()" style="font-size: 11px;">
                            <i class="bi bi-eye" id="togglePassIcon"></i> <span id="togglePassText">Lihat</span>
                        </button>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>

                <button type="submit" id="btnLoginMain" class="btn btn-primary w-100 py-2 fw-semibold fs-6 mb-2 btn-login-main shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                </button>
            </form>

            <!-- Quick 1-Click Access Divider -->
            <div class="quick-login-divider">
                <span>atau 1-Klik Masuk Langsung</span>
            </div>

            <!-- 2x2 Grid: Instant 1-Click Role Login -->
            <div class="row g-2">
                <!-- Mahasiswa -->
                <div class="col-6">
                    <div class="demo-card-item" onclick="quickLogin('mhs2024001', 'mhs123', 'Mahasiswa')" role="button" title="1-Klik Langsung Masuk sebagai Mahasiswa">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-info bg-opacity-25 text-info demo-role-badge">🎓 Mahasiswa</span>
                            <i class="bi bi-arrow-right-short text-muted fs-5 demo-arrow-icon"></i>
                        </div>
                        <div class="small text-muted demo-action-text">Langsung masuk</div>
                    </div>
                </div>

                <!-- Bendahara -->
                <div class="col-6">
                    <div class="demo-card-item" onclick="quickLogin('bendahara', 'bendahara123', 'Bendahara')" role="button" title="1-Klik Langsung Masuk sebagai Bendahara">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-warning bg-opacity-25 text-warning demo-role-badge">💳 Bendahara</span>
                            <i class="bi bi-arrow-right-short text-muted fs-5 demo-arrow-icon"></i>
                        </div>
                        <div class="small text-muted demo-action-text">Langsung masuk</div>
                    </div>
                </div>

                <!-- Admin -->
                <div class="col-6">
                    <div class="demo-card-item" onclick="quickLogin('admin', 'admin123', 'Admin')" role="button" title="1-Klik Langsung Masuk sebagai Admin">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-danger bg-opacity-25 text-danger demo-role-badge">🛡️ Admin</span>
                            <i class="bi bi-arrow-right-short text-muted fs-5 demo-arrow-icon"></i>
                        </div>
                        <div class="small text-muted demo-action-text">Langsung masuk</div>
                    </div>
                </div>

                <!-- Pimpinan -->
                <div class="col-6">
                    <div class="demo-card-item" onclick="quickLogin('pimpinan', 'pimpinan123', 'Pimpinan')" role="button" title="1-Klik Langsung Masuk sebagai Pimpinan">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-success bg-opacity-25 text-success demo-role-badge">📊 Pimpinan</span>
                            <i class="bi bi-arrow-right-short text-muted fs-5 demo-arrow-icon"></i>
                        </div>
                        <div class="small text-muted demo-action-text">Langsung masuk</div>
                    </div>
                </div>
            </div>

            <!-- Test Center Direct Link -->
            <div class="mt-3 pt-2 text-center border-top" style="border-color: rgba(51, 65, 85, 0.4) !important;">
                <a href="/testing" class="text-decoration-none text-muted small" style="font-size: 12px;">
                    <i class="bi bi-cpu-fill text-info me-1"></i> Buka SQA Test Automation Center
                </a>
            </div>

        </div>
    </div>
</div>

<script>
function quickLogin(username, password, roleName) {
    const userInput = document.getElementById('inputIdentifier');
    const passInput = document.getElementById('inputPassword');
    const btn = document.getElementById('btnLoginMain');
    
    userInput.value = username;
    passInput.value = password;
    
    if (btn) {
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Masuk sebagai ${roleName}...`;
        btn.disabled = true;
    }
    
    // Auto-submit instantly for 1-click experience
    document.getElementById('loginForm').submit();
}

function togglePasswordVisibility() {
    const passInput = document.getElementById('inputPassword');
    const icon = document.getElementById('togglePassIcon');
    const text = document.getElementById('togglePassText');
    if (passInput.type === 'password') {
        passInput.type = 'text';
        icon.className = 'bi bi-eye-slash';
        text.innerText = 'Sembunyi';
    } else {
        passInput.type = 'password';
        icon.className = 'bi bi-eye';
        text.innerText = 'Lihat';
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
