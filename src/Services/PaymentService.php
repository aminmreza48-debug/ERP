<?php
// src/Services/PaymentService.php - Payment Processing Logic

class PaymentService {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Submit payment with proof upload
     * Equivalence Partitioning & Boundary Value Analysis ready!
     * Cyclomatic Complexity = 9
     */
    public function submitPayment(int $tagihanId, int $mahasiswaId, float $nominal, string $metode, ?string $bank, ?array $fileData): array {
        // 1. Boundary & Equivalence: Nominal must be positive
        if ($nominal <= 0) {
            return [
                'success' => false,
                'status_code' => 422,
                'error_code' => 'INVALID_NOMINAL_ZERO_OR_NEGATIVE',
                'message' => 'Nominal pembayaran harus lebih besar dari 0'
            ];
        }

        // 2. Fetch tagihan and verify ownership
        $stmt = $this->db->prepare('SELECT * FROM tagihan WHERE id = :id');
        $stmt->execute(['id' => $tagihanId]);
        $tagihan = $stmt->fetch();

        if (!$tagihan) {
            return [
                'success' => false,
                'status_code' => 404,
                'error_code' => 'TAGIHAN_NOT_FOUND',
                'message' => 'Tagihan tidak ditemukan'
            ];
        }

        if ((int)$tagihan['mahasiswa_id'] !== $mahasiswaId) {
            return [
                'success' => false,
                'status_code' => 403,
                'error_code' => 'UNAUTHORIZED_STUDENT',
                'message' => 'Anda tidak memiliki hak untuk membayar tagihan ini'
            ];
        }

        // 3. State check
        if ($tagihan['status'] === 'lunas') {
            return [
                'success' => false,
                'status_code' => 400,
                'error_code' => 'TAGIHAN_ALREADY_PAID',
                'message' => 'Tagihan sudah lunas'
            ];
        }

        if ($tagihan['status'] === 'menunggu_verifikasi') {
            return [
                'success' => false,
                'status_code' => 409,
                'error_code' => 'PAYMENT_PENDING_VERIFICATION',
                'message' => 'Pembayaran sedang menunggu proses verifikasi oleh Bendahara'
            ];
        }

        // 4. Exact amount rule (Boundary: must match nominal tagihan)
        if ($nominal != (float)$tagihan['nominal']) {
            return [
                'success' => false,
                'status_code' => 422,
                'error_code' => 'NOMINAL_MISMATCH',
                'message' => 'Nominal pembayaran (' . number_format($nominal, 0, ',', '.') . ') harus sesuai dengan jumlah tagihan (' . number_format($tagihan['nominal'], 0, ',', '.') . ')'
            ];
        }

        // 5. File proof validation
        $fileValidation = $this->validateProofFile($fileData);
        if (!$fileValidation['valid']) {
            return [
                'success' => false,
                'status_code' => 422,
                'error_code' => $fileValidation['error_code'],
                'message' => $fileValidation['message']
            ];
        }

        $fileName = $fileValidation['file_name'];
        $nomorTransaksi = 'PAY-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // 6. Database transaction
        $this->db->beginTransaction();
        try {
            $stmtPay = $this->db->prepare(
                'INSERT INTO pembayaran (nomor_transaksi, tagihan_id, mahasiswa_id, nominal_bayar, metode_pembayaran, bank_tujuan, bukti_transfer, tanggal_bayar, status)
                 VALUES (:no, :tid, :mid, :nom, :met, :bank, :bukti, :tgl, "menunggu_verifikasi")'
            );
            $stmtPay->execute([
                'no' => $nomorTransaksi,
                'tid' => $tagihanId,
                'mid' => $mahasiswaId,
                'nom' => $nominal,
                'met' => $metode,
                'bank' => $bank ?? 'Bank Mandiri',
                'bukti' => $fileName,
                'tgl' => date('Y-m-d H:i:s')
            ]);
            $paymentId = (int)$this->db->lastInsertId();

            // Update tagihan status
            $stmtT = $this->db->prepare('UPDATE tagihan SET status = "menunggu_verifikasi" WHERE id = :id');
            $stmtT->execute(['id' => $tagihanId]);

            // Notify Bendahara
            $stmtNotif = $this->db->prepare(
                'INSERT INTO notifikasi (user_id, judul, pesan, is_read)
                 SELECT id, "Pembayaran Baru Masuk", "Pembayaran baru menunggu verifikasi untuk tagihan: ' . $tagihan['nomor_tagihan'] . '", 0
                 FROM users WHERE role = "bendahara"'
            );
            $stmtNotif->execute();

            $this->db->commit();

            return [
                'success' => true,
                'status_code' => 201,
                'message' => 'Bukti pembayaran berhasil diupload dan menunggu verifikasi',
                'payment_id' => $paymentId,
                'nomor_transaksi' => $nomorTransaksi
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'status_code' => 500,
                'error_code' => 'DATABASE_ERROR',
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ];
        }
    }

    /**
     * File Validation for Proof Upload
     * BVA & EP: Empty, size <= 2MB, extensions jpg/jpeg/png/pdf
     * Cyclomatic Complexity = 6
     */
    public function validateProofFile(?array $file): array {
        if (empty($file) || !isset($file['name']) || empty($file['name'])) {
            return [
                'valid' => false,
                'error_code' => 'FILE_EMPTY',
                'message' => 'File bukti transfer wajib diunggah'
            ];
        }

        // Size check (max 2MB = 2 * 1024 * 1024 = 2097152 bytes)
        $maxBytes = 2 * 1024 * 1024;
        if (isset($file['size']) && $file['size'] > $maxBytes) {
            return [
                'valid' => false,
                'error_code' => 'FILE_TOO_LARGE',
                'message' => 'Ukuran file melebihi batas maksimum 2MB'
            ];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($ext, $allowedExts, true)) {
            return [
                'valid' => false,
                'error_code' => 'FILE_INVALID_TYPE',
                'message' => 'Format file tidak didukung. Gunakan format JPG, PNG, atau PDF'
            ];
        }

        // Mock saving or moving if tmp_name exists
        $savedName = 'bukti_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        if (!empty($file['tmp_name']) && file_exists($file['tmp_name'])) {
            $targetPath = __DIR__ . '/../../public/uploads/' . $savedName;
            @move_uploaded_file($file['tmp_name'], $targetPath);
        }

        return [
            'valid' => true,
            'file_name' => $savedName
        ];
    }
}

