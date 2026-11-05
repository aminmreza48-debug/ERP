<?php
// src/Services/VerificationService.php - Payment Verification Business Logic

class VerificationService {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Process verification decision (approve / reject)
     * Decision Table & White Box Testing ready!
     * Cyclomatic Complexity = 8
     */
    public function processDecision(int $paymentId, int $verifierUserId, string $action, ?string $catatan = null): array {
        $action = strtolower(trim($action));

        // 1. Validate action parameter
        if (!in_array($action, ['approve', 'reject'], true)) {
            return [
                'success' => false,
                'status_code' => 400,
                'error_code' => 'INVALID_ACTION',
                'message' => 'Aksi verifikasi harus approve atau reject'
            ];
        }

        // 2. Fetch payment record
        $stmt = $this->db->prepare(
            'SELECT p.*, t.nomor_tagihan, m.user_id as student_user_id, m.nama as nama_mhs 
             FROM pembayaran p
             JOIN tagihan t ON p.tagihan_id = t.id
             JOIN mahasiswa m ON p.mahasiswa_id = m.id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $paymentId]);
        $payment = $stmt->fetch();

        if (!$payment) {
            return [
                'success' => false,
                'status_code' => 404,
                'error_code' => 'PAYMENT_NOT_FOUND',
                'message' => 'Data pembayaran tidak ditemukan'
            ];
        }

        // 3. State check: only pending payments can be verified
        if ($payment['status'] !== 'menunggu_verifikasi') {
            return [
                'success' => false,
                'status_code' => 409,
                'error_code' => 'PAYMENT_ALREADY_PROCESSED',
                'message' => 'Pembayaran ini sudah pernah diproses sebelumnya (Status: ' . $payment['status'] . ')'
            ];
        }

        // 4. Decision Table Rule: Reject requires reason/note
        $trimmedCatatan = trim($catatan ?? '');
        if ($action === 'reject' && empty($trimmedCatatan)) {
            return [
                'success' => false,
                'status_code' => 422,
                'error_code' => 'CATATAN_REQUIRED_ON_REJECT',
                'message' => 'Catatan alasan penolakan wajib diisi untuk memberi tahu mahasiswa'
            ];
        }

        // 5. Execute decision inside transaction
        $this->db->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            if ($action === 'approve') {
                // Update payment to diverifikasi
                $stmtP = $this->db->prepare(
                    'UPDATE pembayaran 
                     SET status = "diverifikasi", diverifikasi_oleh = :vid, tanggal_verifikasi = :tgl, catatan = :catatan
                     WHERE id = :id'
                );
                $stmtP->execute([
                    'vid' => $verifierUserId,
                    'tgl' => $now,
                    'catatan' => !empty($trimmedCatatan) ? $trimmedCatatan : 'Pembayaran diverifikasi sesuai bukti transfer.',
                    'id' => $paymentId
                ]);

                // Update invoice to lunas
                $stmtT = $this->db->prepare('UPDATE tagihan SET status = "lunas" WHERE id = :id');
                $stmtT->execute(['id' => $payment['tagihan_id']]);

                // Notify student
                if (!empty($payment['student_user_id'])) {
                    $stmtN = $this->db->prepare(
                        'INSERT INTO notifikasi (user_id, judul, pesan, is_read)
                         VALUES (:uid, "Pembayaran Berhasil Diverifikasi", "Pembayaran tagihan ' . $payment['nomor_tagihan'] . ' telah diverifikasi. Tagihan dinyatakan LUNAS.", 0)'
                    );
                    $stmtN->execute(['uid' => $payment['student_user_id']]);
                }

                $message = 'Pembayaran berhasil diverifikasi (Disetujui)';
            } else {
                // Reject action
                $stmtP = $this->db->prepare(
                    'UPDATE pembayaran 
                     SET status = "ditolak", diverifikasi_oleh = :vid, tanggal_verifikasi = :tgl, catatan = :catatan
                     WHERE id = :id'
                );
                $stmtP->execute([
                    'vid' => $verifierUserId,
                    'tgl' => $now,
                    'catatan' => $trimmedCatatan,
                    'id' => $paymentId
                ]);

                // Update invoice back to ditolak
                $stmtT = $this->db->prepare('UPDATE tagihan SET status = "ditolak" WHERE id = :id');
                $stmtT->execute(['id' => $payment['tagihan_id']]);

                // Notify student with reason
                if (!empty($payment['student_user_id'])) {
                    $stmtN = $this->db->prepare(
                        'INSERT INTO notifikasi (user_id, judul, pesan, is_read)
                         VALUES (:uid, "Pembayaran Ditolak", "Pembayaran tagihan ' . $payment['nomor_tagihan'] . ' ditolak dengan alasan: ' . addslashes($trimmedCatatan) . '. Silakan upload bukti yang benar.", 0)'
                    );
                    $stmtN->execute(['uid' => $payment['student_user_id']]);
                }

                $message = 'Pembayaran berhasil ditolak dengan catatan';
            }

            $this->db->commit();

            return [
                'success' => true,
                'status_code' => 200,
                'action_taken' => $action,
                'message' => $message,
                'payment_id' => $paymentId
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'status_code' => 500,
                'error_code' => 'TRANSACTION_FAILED',
                'message' => 'Gagal memproses verifikasi: ' . $e->getMessage()
            ];
        }
    }
}

