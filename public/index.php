<?php
// public/index.php - Front Controller & Application Router

$sessionDir = __DIR__ . '/../storage/sessions';
if (!is_dir($sessionDir)) {
    @mkdir($sessionDir, 0777, true);
}
@session_save_path($sessionDir);

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Services/AuthService.php';
require_once __DIR__ . '/../src/Services/TagihanService.php';
require_once __DIR__ . '/../src/Services/PaymentService.php';
require_once __DIR__ . '/../src/Services/VerificationService.php';
require_once __DIR__ . '/../src/Controllers/ApiController.php';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// 1. Static file check (uploads)
if (preg_match('#^/uploads/(.+)$#', $uri, $m)) {
    $file = __DIR__ . '/uploads/' . basename($m[1]);
    if (file_exists($file)) {
        $mime = mime_content_type($file);
        header('Content-Type: ' . $mime);
        readfile($file);
        exit;
    }
}

// 2. API Routing
if (str_starts_with($uri, '/api')) {
    $api = new ApiController();
    $api->handleRequest($uri, $method);
    exit;
}

// SQA Test Runner Route (Publicly accessible for testers & evaluators)
if ($uri === '/testing') {
    require __DIR__ . '/../src/Views/testing/runner.php';
    exit;
}

// 3. Web UI Routing
$db = Database::getConnection();
$authService = new AuthService($db);
$tagihanService = new TagihanService($db);
$paymentService = new PaymentService($db);
$verificationService = new VerificationService($db);

$currentUser = $_SESSION['user'] ?? null;

// Auth routes
if ($uri === '/' || $uri === '/login') {
    if ($currentUser) {
        header('Location: /dashboard');
        exit;
    }
    $errorMessage = '';
    if ($method === 'POST') {
        $res = $authService->authenticate($_POST['identifier'] ?? '', $_POST['password'] ?? '');
        if ($res['success']) {
            $_SESSION['user'] = $res['user'];
            $_SESSION['token'] = $res['token'];
            header('Location: /dashboard');
            exit;
        } else {
            $errorMessage = $res['message'];
        }
    }
    require __DIR__ . '/../src/Views/auth/login.php';
    exit;
}

if ($uri === '/logout') {
    session_destroy();
    header('Location: /login');
    exit;
}

// Ensure logged in for protected routes
if (!$currentUser) {
    header('Location: /login');
    exit;
}

$role = $currentUser['role'];

// Universal dashboard router
if ($uri === '/dashboard') {
    if ($role === 'mahasiswa') header('Location: /mahasiswa/dashboard');
    elseif ($role === 'bendahara') header('Location: /bendahara/dashboard');
    elseif ($role === 'admin') header('Location: /admin/dashboard');
    elseif ($role === 'pimpinan') header('Location: /pimpinan/dashboard');
    else header('Location: /login');
    exit;
}

// ==================== MAHASISWA ROUTES ====================
if (str_starts_with($uri, '/mahasiswa')) {
    if ($role !== 'mahasiswa' && $role !== 'admin') {
        http_response_code(403);
        die('Akses ditolak. Halaman khusus Mahasiswa.');
    }

    $stmtM = $db->prepare('SELECT * FROM mahasiswa WHERE user_id = :uid LIMIT 1');
    $stmtM->execute(['uid' => $currentUser['id']]);
    $mahasiswa = $stmtM->fetch() ?: [
        'id' => 1,
        'nim' => '202401001',
        'nama' => $currentUser['name'],
        'prodi' => 'Teknik Informatika',
        'angkatan' => '2024'
    ];

    if ($uri === '/mahasiswa/dashboard' || $uri === '/mahasiswa/tagihan') {
        $tagihanList = $tagihanService->getTagihanMahasiswa((int)$mahasiswa['id']);
        $totalTagihan = 0;
        $totalLunas = 0;
        $totalMenunggu = 0;
        $totalTunggakan = 0;

        foreach ($tagihanList as $t) {
            $nom = (float)$t['nominal'];
            $totalTagihan += $nom;
            if ($t['status'] === 'lunas') $totalLunas += $nom;
            elseif ($t['status'] === 'menunggu_verifikasi') $totalMenunggu += $nom;
            else $totalTunggakan += $nom;
        }

        require __DIR__ . '/../src/Views/mahasiswa/dashboard.php';
        exit;
    }

    if ($uri === '/mahasiswa/upload') {
        $tagihanId = (int)($_GET['tagihan_id'] ?? $_POST['tagihan_id'] ?? 0);
        $stmtT = $db->prepare(
            'SELECT t.*, jb.nama as nama_biaya FROM tagihan t JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id WHERE t.id = :id'
        );
        $stmtT->execute(['id' => $tagihanId]);
        $tagihan = $stmtT->fetch();

        $errorMessage = '';
        $successMessage = '';

        if ($method === 'POST') {
            $nominal = (float)($_POST['nominal_bayar'] ?? 0);
            $metode = $_POST['metode_pembayaran'] ?? 'Transfer Bank';
            $bank = $_POST['bank_tujuan'] ?? 'Bank Mandiri';
            $file = $_FILES['bukti_transfer'] ?? null;

            $res = $paymentService->submitPayment($tagihanId, (int)$mahasiswa['id'], $nominal, $metode, $bank, $file);
            if ($res['success']) {
                $successMessage = $res['message'];
                header('Location: /mahasiswa/riwayat?success=1');
                exit;
            } else {
                $errorMessage = $res['message'];
            }
        }

        require __DIR__ . '/../src/Views/mahasiswa/upload.php';
        exit;
    }

    if ($uri === '/mahasiswa/riwayat') {
        $stmtP = $db->prepare(
            'SELECT p.*, t.nomor_tagihan, jb.nama as nama_biaya 
             FROM pembayaran p
             JOIN tagihan t ON p.tagihan_id = t.id
             JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id
             WHERE p.mahasiswa_id = :mid
             ORDER BY p.id DESC'
        );
        $stmtP->execute(['mid' => $mahasiswa['id']]);
        $pembayaranList = $stmtP->fetchAll();

        require __DIR__ . '/../src/Views/mahasiswa/riwayat.php';
        exit;
    }

    if ($uri === '/mahasiswa/cetak') {
        $tagihanId = (int)($_GET['tagihan_id'] ?? 0);
        $stmtC = $db->prepare(
            'SELECT p.*, t.nomor_tagihan, t.semester, jb.nama as nama_biaya, m.nama as nama_mahasiswa, m.nim, m.prodi, m.angkatan, u.name as verifikator_nama
             FROM pembayaran p
             JOIN tagihan t ON p.tagihan_id = t.id
             JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id
             JOIN mahasiswa m ON p.mahasiswa_id = m.id
             LEFT JOIN users u ON p.diverifikasi_oleh = u.id
             WHERE p.tagihan_id = :tid AND p.status = "diverifikasi"
             LIMIT 1'
        );
        $stmtC->execute(['tid' => $tagihanId]);
        $pembayaran = $stmtC->fetch();
        if (!$pembayaran) {
            die('Bukti pembayaran resmi belum tersedia atau belum diverifikasi oleh bendahara.');
        }

        require __DIR__ . '/../src/Views/mahasiswa/cetak.php';
        exit;
    }
}

// ==================== BENDAHARA ROUTES ====================
if (str_starts_with($uri, '/bendahara')) {
    if ($role !== 'bendahara' && $role !== 'admin') {
        http_response_code(403);
        die('Akses ditolak. Khusus Bendahara Keuangan.');
    }

    if ($uri === '/bendahara/dashboard') {
        $stmtPend = $db->query(
            'SELECT p.*, t.nomor_tagihan, jb.nama as nama_biaya, m.nama as nama_mahasiswa, m.nim, m.prodi
             FROM pembayaran p
             JOIN tagihan t ON p.tagihan_id = t.id
             JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id
             JOIN mahasiswa m ON p.mahasiswa_id = m.id
             WHERE p.status = "menunggu_verifikasi"
             ORDER BY p.id ASC'
        );
        $pendingList = $stmtPend->fetchAll();
        $totalDiverifikasi = (float)$db->query('SELECT COALESCE(SUM(nominal_bayar),0) FROM pembayaran WHERE status="diverifikasi"')->fetchColumn();
        $totalDitolakCount = (int)$db->query('SELECT count(*) FROM pembayaran WHERE status="ditolak"')->fetchColumn();
        $totalTarget = (float)$db->query('SELECT COALESCE(SUM(nominal),0) FROM tagihan')->fetchColumn();

        require __DIR__ . '/../src/Views/bendahara/dashboard.php';
        exit;
    }

    if ($uri === '/bendahara/verifikasi') {
        $successMessage = '';
        $errorMessage = '';

        if ($method === 'POST') {
            $paymentId = (int)($_POST['payment_id'] ?? 0);
            $action = $_POST['action'] ?? '';
            $catatan = $_POST['catatan'] ?? null;

            $res = $verificationService->processDecision($paymentId, (int)$currentUser['id'], $action, $catatan);
            if ($res['success']) {
                $successMessage = $res['message'];
            } else {
                $errorMessage = $res['message'];
            }
        }

        $stmtAll = $db->query(
            'SELECT p.*, t.nomor_tagihan, jb.nama as nama_biaya, m.nama as nama_mahasiswa, m.nim
             FROM pembayaran p
             JOIN tagihan t ON p.tagihan_id = t.id
             JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id
             JOIN mahasiswa m ON p.mahasiswa_id = m.id
             ORDER BY CASE WHEN p.status = "menunggu_verifikasi" THEN 1 ELSE 2 END, p.id DESC'
        );
        $pembayaranList = $stmtAll->fetchAll();

        require __DIR__ . '/../src/Views/bendahara/verifikasi.php';
        exit;
    }

    if ($uri === '/bendahara/laporan') {
        $stmtLap = $db->query(
            'SELECT p.*, t.nomor_tagihan, m.nim, m.nama as nama_mhs, m.prodi, jb.nama as nama_biaya, u.name as verifikator_nama
             FROM pembayaran p
             JOIN tagihan t ON p.tagihan_id = t.id
             JOIN mahasiswa m ON p.mahasiswa_id = m.id
             JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id
             LEFT JOIN users u ON p.diverifikasi_oleh = u.id
             WHERE p.status = "diverifikasi"
             ORDER BY p.tanggal_verifikasi DESC'
        );
        $laporanList = $stmtLap->fetchAll();
        $totalPenerimaan = 0;
        foreach ($laporanList as $l) {
            $totalPenerimaan += (float)$l['nominal_bayar'];
        }

        require __DIR__ . '/../src/Views/bendahara/laporan.php';
        exit;
    }
}

// ==================== ADMIN ROUTES ====================
if (str_starts_with($uri, '/admin')) {
    if ($role !== 'admin') {
        http_response_code(403);
        die('Akses ditolak. Halaman khusus Administrator.');
    }

    if ($uri === '/admin/dashboard') {
        $totalMhs = (int)$db->query('SELECT count(*) FROM mahasiswa WHERE status="aktif"')->fetchColumn();
        $totalBiaya = (int)$db->query('SELECT count(*) FROM jenis_biaya WHERE status="aktif"')->fetchColumn();
        $totalTagihanCount = (int)$db->query('SELECT count(*) FROM tagihan')->fetchColumn();
        $totalTagihanNominal = (float)$db->query('SELECT COALESCE(SUM(nominal),0) FROM tagihan')->fetchColumn();
        $totalUsers = (int)$db->query('SELECT count(*) FROM users')->fetchColumn();
        $auditLogs = $db->query('SELECT * FROM audit_logs ORDER BY id DESC LIMIT 5')->fetchAll();

        require __DIR__ . '/../src/Views/admin/dashboard.php';
        exit;
    }

    if ($uri === '/admin/mahasiswa') {
        $successMessage = '';
        $errorMessage = '';
        if ($method === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'create') {
                $chk = $db->prepare('SELECT id FROM mahasiswa WHERE nim = :nim');
                $chk->execute(['nim' => $_POST['nim']]);
                if ($chk->fetch()) {
                    $errorMessage = 'NIM sudah terdaftar';
                } else {
                    $ins = $db->prepare('INSERT INTO mahasiswa (nim, nama, prodi, angkatan, email, telepon, status) VALUES (:nim, :nama, :prodi, :ang, :email, :telp, "aktif")');
                    $ins->execute([
                        'nim' => $_POST['nim'],
                        'nama' => $_POST['nama'],
                        'prodi' => $_POST['prodi'],
                        'ang' => $_POST['angkatan'],
                        'email' => $_POST['email'] ?? null,
                        'telp' => $_POST['telepon'] ?? null
                    ]);
                    $successMessage = 'Mahasiswa berhasil ditambahkan';
                }
            } elseif ($action === 'edit') {
                $upd = $db->prepare('UPDATE mahasiswa SET nama = :nama, prodi = :prodi, angkatan = :ang, email = :email, status = :status WHERE id = :id');
                $upd->execute([
                    'nama' => $_POST['nama'],
                    'prodi' => $_POST['prodi'],
                    'ang' => $_POST['angkatan'],
                    'email' => $_POST['email'],
                    'status' => $_POST['status'],
                    'id' => (int)$_POST['id']
                ]);
                $successMessage = 'Data mahasiswa berhasil diperbarui';
            } elseif ($action === 'delete') {
                $del = $db->prepare('DELETE FROM mahasiswa WHERE id = :id');
                $del->execute(['id' => (int)$_POST['id']]);
                $successMessage = 'Data mahasiswa berhasil dihapus';
            }
        }
        $mahasiswaList = $db->query('SELECT * FROM mahasiswa ORDER BY id DESC')->fetchAll();
        require __DIR__ . '/../src/Views/admin/mahasiswa.php';
        exit;
    }

    if ($uri === '/admin/biaya') {
        $successMessage = '';
        if ($method === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'create') {
                $ins = $db->prepare('INSERT INTO jenis_biaya (kode, nama, nominal_default, keterangan, status) VALUES (:kode, :nama, :nom, :ket, "aktif")');
                $ins->execute([
                    'kode' => $_POST['kode'],
                    'nama' => $_POST['nama'],
                    'nom' => (float)$_POST['nominal_default'],
                    'ket' => $_POST['keterangan'] ?? null
                ]);
                $successMessage = 'Jenis biaya baru berhasil disimpan';
            } elseif ($action === 'edit') {
                $upd = $db->prepare('UPDATE jenis_biaya SET nama = :nama, nominal_default = :nom, keterangan = :ket, status = :status WHERE id = :id');
                $upd->execute([
                    'nama' => $_POST['nama'],
                    'nom' => (float)$_POST['nominal_default'],
                    'ket' => $_POST['keterangan'],
                    'status' => $_POST['status'],
                    'id' => (int)$_POST['id']
                ]);
                $successMessage = 'Jenis biaya berhasil diperbarui';
            }
        }
        $biayaList = $db->query('SELECT * FROM jenis_biaya ORDER BY id ASC')->fetchAll();
        require __DIR__ . '/../src/Views/admin/biaya.php';
        exit;
    }

    if ($uri === '/admin/tagihan') {
        $successMessage = '';
        $errorMessage = '';
        if ($method === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'create_single') {
                $res = $tagihanService->createTagihan([
                    'mahasiswa_id' => (int)$_POST['mahasiswa_id'],
                    'jenis_biaya_id' => (int)$_POST['jenis_biaya_id'],
                    'semester' => $_POST['semester'],
                    'nominal' => !empty($_POST['nominal']) ? (float)$_POST['nominal'] : null,
                    'tanggal_jatuh_tempo' => $_POST['tanggal_jatuh_tempo']
                ]);
                if ($res['success']) $successMessage = $res['message'];
                else $errorMessage = $res['message'];
            } elseif ($action === 'create_batch') {
                $res = $tagihanService->generateBatch(
                    (int)$_POST['jenis_biaya_id'],
                    $_POST['semester'],
                    !empty($_POST['prodi']) ? $_POST['prodi'] : null,
                    !empty($_POST['angkatan']) ? $_POST['angkatan'] : null
                );
                $successMessage = $res['message'];
            } elseif ($action === 'edit') {
                $upd = $db->prepare('UPDATE tagihan SET nominal = :nom, tanggal_jatuh_tempo = :jt WHERE id = :id');
                $upd->execute([
                    'nom' => (float)$_POST['nominal'],
                    'jt' => $_POST['tanggal_jatuh_tempo'],
                    'id' => (int)$_POST['id']
                ]);
                $successMessage = 'Tagihan berhasil diperbarui';
            }
        }

        $tagihanList = $db->query(
            'SELECT t.*, jb.nama as nama_biaya, m.nama as nama_mahasiswa, m.nim, m.prodi
             FROM tagihan t
             JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id
             JOIN mahasiswa m ON t.mahasiswa_id = m.id
             ORDER BY t.id DESC'
        )->fetchAll();
        $mahasiswaList = $db->query('SELECT id, nim, nama FROM mahasiswa WHERE status="aktif"')->fetchAll();
        $biayaList = $db->query('SELECT * FROM jenis_biaya WHERE status="aktif"')->fetchAll();

        require __DIR__ . '/../src/Views/admin/tagihan.php';
        exit;
    }

    if ($uri === '/admin/users') {
        $successMessage = '';
        if ($method === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'create') {
                $ins = $db->prepare('INSERT INTO users (username, password, name, email, role, status) VALUES (:u, :p, :n, :e, :r, "active")');
                $ins->execute([
                    'u' => $_POST['username'],
                    'p' => password_hash($_POST['password'], PASSWORD_BCRYPT),
                    'n' => $_POST['name'],
                    'e' => $_POST['email'],
                    'r' => $_POST['role']
                ]);
                $successMessage = 'Pengguna berhasil dibuat';
            } elseif ($action === 'edit') {
                $upd = $db->prepare('UPDATE users SET name = :n, role = :r, status = :s WHERE id = :id');
                $upd->execute([
                    'n' => $_POST['name'],
                    'r' => $_POST['role'],
                    's' => $_POST['status'],
                    'id' => (int)$_POST['id']
                ]);
                $successMessage = 'Hak akses user berhasil diperbarui';
            }
        }
        $userList = $db->query('SELECT * FROM users ORDER BY id ASC')->fetchAll();
        require __DIR__ . '/../src/Views/admin/users.php';
        exit;
    }
}

// ==================== PIMPINAN ROUTES ====================
if (str_starts_with($uri, '/pimpinan')) {
    if ($role !== 'pimpinan' && $role !== 'admin') {
        http_response_code(403);
        die('Akses ditolak. Khusus Pimpinan Institusi.');
    }

    if ($uri === '/pimpinan/dashboard') {
        $totTagihan = (float)$db->query('SELECT COALESCE(SUM(nominal),0) FROM tagihan')->fetchColumn();
        $totTerbayar = (float)$db->query('SELECT COALESCE(SUM(nominal_bayar),0) FROM pembayaran WHERE status="diverifikasi"')->fetchColumn();
        $totMenunggu = (float)$db->query('SELECT COALESCE(SUM(nominal_bayar),0) FROM pembayaran WHERE status="menunggu_verifikasi"')->fetchColumn();
        $totTunggakan = (float)$db->query('SELECT COALESCE(SUM(nominal),0) FROM tagihan WHERE status IN ("belum_bayar", "ditolak")')->fetchColumn();

        $kpi = [
            'total_tagihan' => $totTagihan,
            'total_terbayar' => $totTerbayar,
            'total_menunggu' => $totMenunggu,
            'total_tunggakan' => $totTunggakan,
            'persentase_realisasi' => $totTagihan > 0 ? round(($totTerbayar / $totTagihan) * 100, 1) : 0,
            'persentase_tunggakan' => $totTagihan > 0 ? round(($totTunggakan / $totTagihan) * 100, 1) : 0,
        ];

        require __DIR__ . '/../src/Views/pimpinan/dashboard.php';
        exit;
    }

    if ($uri === '/pimpinan/laporan-pembayaran') {
        $laporanList = $db->query(
            'SELECT p.*, t.nomor_tagihan, m.nim, m.nama as nama_mhs, m.prodi, jb.nama as nama_biaya
             FROM pembayaran p
             JOIN tagihan t ON p.tagihan_id = t.id
             JOIN mahasiswa m ON p.mahasiswa_id = m.id
             JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id
             WHERE p.status = "diverifikasi"
             ORDER BY p.tanggal_verifikasi DESC'
        )->fetchAll();
        $totalRealisasi = 0;
        foreach ($laporanList as $l) $totalRealisasi += (float)$l['nominal_bayar'];

        require __DIR__ . '/../src/Views/pimpinan/laporan-pembayaran.php';
        exit;
    }

    if ($uri === '/pimpinan/laporan-tunggakan') {
        $tunggakanList = $db->query(
            'SELECT t.*, m.nim, m.nama as nama_mhs, m.prodi, m.angkatan, jb.nama as nama_biaya
             FROM tagihan t
             JOIN mahasiswa m ON t.mahasiswa_id = m.id
             JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id
             WHERE t.status IN ("belum_bayar", "ditolak")
             ORDER BY t.tanggal_jatuh_tempo ASC'
        )->fetchAll();
        $totalTunggakanNominal = 0;
        foreach ($tunggakanList as $t) $totalTunggakanNominal += (float)$t['nominal'];

        require __DIR__ . '/../src/Views/pimpinan/laporan-tunggakan.php';
        exit;
    }

    if ($uri === '/pimpinan/statistik') {
        require __DIR__ . '/../src/Views/pimpinan/statistik.php';
        exit;
    }
}

// 404 Not Found
http_response_code(404);
echo '<div style="font-family: sans-serif; text-align: center; padding: 50px;"><h1>404 Not Found</h1><p>Halaman tidak ditemukan.</p><a href="/dashboard">Kembali ke Dashboard</a></div>';
