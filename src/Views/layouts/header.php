<?php
$sessionDir = __DIR__ . '/../../../storage/sessions';
if (is_dir($sessionDir)) {
    @session_save_path($sessionDir);
}
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
$currentUser = $_SESSION['user'] ?? null;
$role = $currentUser['role'] ?? 'guest';
$pageTitle = $pageTitle ?? 'SIPERMA ERP';
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - SIPERMA ERP</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><text y='14' font-size='14'>💳</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-dark: #07090e;
            --bg-surface: #0f172a;
            --bg-card: #131b2e;
            --border-color: #1e293b;
            --primary-accent: #3b82f6;
            --text-primary: #f8fafc;
            --text-muted: #94a3b8;
        }
        *, *::before, *::after {
            box-sizing: border-box;
        }
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
            width: 100%;
        }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #080c14;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(30, 58, 138, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(14, 165, 233, 0.08) 0%, transparent 40%);
            background-attachment: fixed;
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-custom {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(51, 65, 85, 0.6);
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        .card-custom {
            background-color: #0f172a;
            border: 1px solid rgba(51, 65, 85, 0.6);
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            color: #f1f5f9;
            transition: all 0.25s ease;
        }
        .card-custom:hover {
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 12px 35px rgba(0,0,0,0.55);
        }
        .form-control, .form-select {
            background-color: #090d16 !important;
            border: 1px solid #334155 !important;
            color: #f8fafc !important;
            border-radius: 8px;
            padding: 10px 14px;
        }
        .form-control:focus, .form-select:focus {
            background-color: #090d16 !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.2) !important;
            color: #ffffff !important;
        }
        .input-group-text {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            color: #38bdf8 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
            transform: translateY(-1px);
        }
        .table-custom {
            color: #f1f5f9 !important;
        }
        .table-custom th {
            background-color: #1e293b !important;
            color: #94a3b8 !important;
            border-color: #334155 !important;
            font-weight: 600;
        }
        .table-custom td {
            background-color: transparent !important;
            border-color: #1e293b !important;
            color: #e2e8f0 !important;
        }
        .table-hover tbody tr:hover td {
            background-color: rgba(51, 65, 85, 0.3) !important;
        }
        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.3px;
        }
        .modal-content {
            background-color: #0f172a;
            border: 1px solid #334155;
            color: #f8fafc;
            border-radius: 14px;
        }
        .list-group-item {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            color: #e2e8f0 !important;
        }
        .list-group-item:hover {
            background-color: #1e293b !important;
        }
        .border-top, .border-bottom, .border {
            border-color: rgba(51, 65, 85, 0.6) !important;
        }
        .bg-light {
            background-color: #0b1120 !important;
            color: #e2e8f0 !important;
        }
        .bg-white {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }
        .text-dark {
            color: #f8fafc !important;
        }
        .text-muted {
            color: #94a3b8 !important;
        }

        /* Mobile Polish & Responsive Header Utilities */
        .page-header-responsive {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 768px) {
            .page-header-responsive {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
            }
        }
        .page-actions-responsive {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            width: 100%;
        }
        @media (min-width: 768px) {
            .page-actions-responsive {
                width: auto;
            }
        }
        .page-actions-responsive .btn {
            flex: 1 1 auto;
            text-align: center;
            white-space: nowrap;
        }
        @media (min-width: 768px) {
            .page-actions-responsive .btn {
                flex: 0 0 auto;
            }
        }

        @media (max-width: 767.98px) {
            .container-fluid {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }
            .card-custom {
                border-radius: 12px !important;
                padding: 1rem !important;
            }
            .fs-4 {
                font-size: 1.25rem !important;
            }
            h3 {
                font-size: 1.3rem !important;
            }
            h4 {
                font-size: 1.15rem !important;
            }
            .navbar-brand span.fs-5 {
                font-size: 1.05rem !important;
            }
            .btn {
                font-size: 0.85rem !important;
            }
            .table-responsive {
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
</head>
<body>

<?php if ($currentUser): ?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container-fluid px-3 px-md-4">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/dashboard">
            <span class="d-inline-flex p-1 p-sm-2 rounded-3 bg-primary bg-opacity-25 text-info border border-info border-opacity-25">
                <i class="bi bi-wallet2 fs-5"></i>
            </span>
            <span class="fs-5 tracking-wide">SIPERMA <span class="badge bg-primary text-white fs-6">ERP</span></span>
        </a>
        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                </li>

                <?php if ($role === 'mahasiswa'): ?>
                <li class="nav-item"><a class="nav-link" href="/mahasiswa/tagihan"><i class="bi bi-receipt me-1"></i> Tagihan Saya</a></li>
                <li class="nav-item"><a class="nav-link" href="/mahasiswa/riwayat"><i class="bi bi-clock-history me-1"></i> Riwayat Pembayaran</a></li>
                <?php endif; ?>

                <?php if ($role === 'bendahara'): ?>
                <li class="nav-item"><a class="nav-link" href="/bendahara/verifikasi"><i class="bi bi-patch-check me-1"></i> Verifikasi Pembayaran</a></li>
                <li class="nav-item"><a class="nav-link" href="/bendahara/laporan"><i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan Keuangan</a></li>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="/admin/mahasiswa"><i class="bi bi-people me-1"></i> Mahasiswa</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/biaya"><i class="bi bi-cash-stack me-1"></i> Jenis Biaya</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/tagihan"><i class="bi bi-receipt-cutoff me-1"></i> Kelola Tagihan</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/users"><i class="bi bi-person-gear me-1"></i> Manajemen User</a></li>
                <?php endif; ?>

                <?php if ($role === 'pimpinan'): ?>
                <li class="nav-item"><a class="nav-link" href="/pimpinan/laporan-pembayaran"><i class="bi bi-graph-up-arrow me-1"></i> Laporan Realisasi</a></li>
                <li class="nav-item"><a class="nav-link" href="/pimpinan/laporan-tunggakan"><i class="bi bi-exclamation-triangle me-1"></i> Laporan Tunggakan</a></li>
                <li class="nav-item"><a class="nav-link" href="/pimpinan/statistik"><i class="bi bi-pie-chart me-1"></i> Statistik Keuangan</a></li>
                <?php endif; ?>

                <li class="nav-item ms-lg-2">
                    <a class="nav-link badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 mt-1 px-3 py-2" href="/testing">
                        <i class="bi bi-cpu me-1"></i> <strong>SQA Test Center</strong>
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-semibold text-white small"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></div>
                    <span class="badge bg-secondary-subtle text-info text-uppercase" style="font-size: 11px;"><?= htmlspecialchars($currentUser['role'] ?? '') ?></span>
                </div>
                <a href="/logout" class="btn btn-outline-danger btn-sm px-3"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<main class="py-3 py-md-4 flex-grow-1">
    <div class="container-fluid px-3 px-md-4">
