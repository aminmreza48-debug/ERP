<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$currentUser = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran - <?= htmlspecialchars($pembayaran['nomor_transaksi'] ?? 'SIPERMA') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: 'Times New Roman', Times, serif; }
        .receipt-card {
            background: #fff;
            max-width: 750px;
            margin: 40px auto;
            padding: 40px;
            border: 2px solid #0f172a;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header-line {
            border-bottom: 3px double #0f172a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .stamp-box {
            border: 2px dashed #16a34a;
            color: #16a34a;
            display: inline-block;
            padding: 8px 16px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            transform: rotate(-5deg);
        }
        @media print {
            body { background: #fff; }
            .receipt-card { border: 2px solid #000; box-shadow: none; margin: 0; padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="container my-4 text-center no-print">
    <button onclick="window.print()" class="btn btn-primary btn-sm px-4"><i class="bi bi-printer"></i> Cetak Dokumen</button>
    <a href="/mahasiswa/dashboard" class="btn btn-outline-secondary btn-sm ms-2">Kembali</a>
</div>

<div class="receipt-card">
    <div class="text-center header-line">
        <h4 class="fw-bold mb-1" style="font-family: sans-serif; letter-spacing: 1px;">KEMENTERIAN PENDIDIKAN TINGGI</h4>
        <h5 class="fw-bold mb-1" style="font-family: sans-serif;">SIPERMA ERP FINANCIAL SYSTEM</h5>
        <p class="mb-0 small text-muted">Jl. Kampus Merdeka No. 101, Kota Pendidikan | Telp: (021) 555-0199 | www.siperma.ac.id</p>
    </div>

    <div class="text-center mb-4">
        <h5 class="text-decoration-underline fw-bold mb-1">BUKTI PEMBAYARAN SAH (KWITANSI)</h5>
        <small class="text-muted">Nomor Registrasi: <strong><?= htmlspecialchars($pembayaran['nomor_transaksi'] ?? '-') ?></strong></small>
    </div>

    <table class="table table-borderless mb-4">
        <tbody>
            <tr><td width="30%">Telah Diterima Dari</td><td width="5%">:</td><td><strong><?= htmlspecialchars($pembayaran['nama_mahasiswa'] ?? '-') ?></strong></td></tr>
            <tr><td>Nomor Induk Mahasiswa (NIM)</td><td>:</td><td><?= htmlspecialchars($pembayaran['nim'] ?? '-') ?></td></tr>
            <tr><td>Program Studi / Angkatan</td><td>:</td><td><?= htmlspecialchars($pembayaran['prodi'] ?? '-') ?> / <?= htmlspecialchars($pembayaran['angkatan'] ?? '-') ?></td></tr>
            <tr><td>Untuk Pembayaran</td><td>:</td><td><?= htmlspecialchars($pembayaran['nama_biaya'] ?? '-') ?> (Semester <?= htmlspecialchars($pembayaran['semester'] ?? '-') ?>)</td></tr>
            <tr><td>Nomor Tagihan Terkait</td><td>:</td><td><?= htmlspecialchars($pembayaran['nomor_tagihan'] ?? '-') ?></td></tr>
            <tr><td>Metode Pembayaran</td><td>:</td><td><?= htmlspecialchars($pembayaran['metode_pembayaran'] ?? '-') ?> (<?= htmlspecialchars($pembayaran['bank_tujuan'] ?? '-') ?>)</td></tr>
            <tr><td>Tanggal Bayar & Verifikasi</td><td>:</td><td><?= htmlspecialchars($pembayaran['tanggal_bayar'] ?? '-') ?> / <?= htmlspecialchars($pembayaran['tanggal_verifikasi'] ?? '-') ?></td></tr>
            <tr style="border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                <td class="fs-5 fw-bold py-3">Jumlah Nominal</td>
                <td class="fs-5 fw-bold py-3">:</td>
                <td class="fs-4 fw-bold text-success py-3">Rp <?= number_format($pembayaran['nominal_bayar'] ?? 0, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <div class="row align-items-center mt-5">
        <div class="col-6 text-center">
            <div class="stamp-box">
                TERVERIFIKASI & LUNAS<br>
                <small style="font-size: 10px; font-weight: normal;"><?= date('d M Y', strtotime($pembayaran['tanggal_verifikasi'] ?? 'now')) ?></small>
            </div>
        </div>
        <div class="col-6 text-center">
            <p class="mb-5 small text-muted">Bendahara Penerima Keuangan,</p>
            <strong class="text-decoration-underline"><?= htmlspecialchars($pembayaran['verifikator_nama'] ?? 'Bendahara Keuangan') ?></strong><br>
            <small class="text-muted">NIP. 198501152010122001</small>
        </div>
    </div>
</div>

</body>
</html>

