<?php
// src/Services/TagihanService.php - Invoicing Business Logic

class TagihanService {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Create single invoice
     * Cyclomatic Complexity = 7
     */
    public function createTagihan(array $data): array {
        // Validate required fields
        if (empty($data['mahasiswa_id']) || empty($data['jenis_biaya_id']) || empty($data['semester']) || !isset($data['nominal'])) {
            return ['success' => false, 'status_code' => 422, 'message' => 'Semua kolom wajib diisi'];
        }

        $nominal = (float)$data['nominal'];
        // Boundary Value / Rule: Nominal must be positive
        if ($nominal <= 0) {
            return ['success' => false, 'status_code' => 422, 'message' => 'Nominal tagihan harus lebih besar dari 0'];
        }

        // Validate mahasiswa exists and active
        $stmtM = $this->db->prepare('SELECT id, status FROM mahasiswa WHERE id = :id');
        $stmtM->execute(['id' => $data['mahasiswa_id']]);
        $mhs = $stmtM->fetch();
        if (!$mhs) {
            return ['success' => false, 'status_code' => 404, 'message' => 'Mahasiswa tidak ditemukan'];
        }
        if ($mhs['status'] !== 'aktif') {
            return ['success' => false, 'status_code' => 422, 'message' => 'Tidak dapat membuat tagihan untuk mahasiswa non-aktif'];
        }

        // Validate jenis biaya exists
        $stmtB = $this->db->prepare('SELECT id, status FROM jenis_biaya WHERE id = :id');
        $stmtB->execute(['id' => $data['jenis_biaya_id']]);
        $biaya = $stmtB->fetch();
        if (!$biaya || $biaya['status'] !== 'aktif') {
            return ['success' => false, 'status_code' => 422, 'message' => 'Jenis biaya tidak valid atau non-aktif'];
        }

        // Check duplicate active invoice for same student, type, and semester
        $stmtDup = $this->db->prepare(
            'SELECT id FROM tagihan WHERE mahasiswa_id = :mid AND jenis_biaya_id = :bid AND semester = :sem AND status != "dibatalkan"'
        );
        $stmtDup->execute([
            'mid' => $data['mahasiswa_id'],
            'bid' => $data['jenis_biaya_id'],
            'sem' => $data['semester']
        ]);
        if ($stmtDup->fetch()) {
            return ['success' => false, 'status_code' => 409, 'message' => 'Tagihan untuk komponen dan semester ini sudah ada'];
        }

        $nomorTagihan = 'TGH-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
        $tanggalTerbit = $data['tanggal_terbit'] ?? date('Y-m-d');
        $tanggalJatuhTempo = $data['tanggal_jatuh_tempo'] ?? date('Y-m-d', strtotime('+30 days'));

        $stmt = $this->db->prepare(
            'INSERT INTO tagihan (nomor_tagihan, mahasiswa_id, jenis_biaya_id, semester, nominal, tanggal_terbit, tanggal_jatuh_tempo, status)
             VALUES (:no, :mid, :bid, :sem, :nom, :tt, :jt, "belum_bayar")'
        );
        $stmt->execute([
            'no' => $nomorTagihan,
            'mid' => $data['mahasiswa_id'],
            'bid' => $data['jenis_biaya_id'],
            'sem' => $data['semester'],
            'nom' => $nominal,
            'tt' => $tanggalTerbit,
            'jt' => $tanggalJatuhTempo
        ]);

        $tagihanId = (int)$this->db->lastInsertId();

        return [
            'success' => true,
            'status_code' => 201,
            'message' => 'Tagihan berhasil dibuat',
            'tagihan_id' => $tagihanId,
            'nomor_tagihan' => $nomorTagihan
        ];
    }

    /**
     * Batch invoice generation for entire prodi/angkatan
     * Cyclomatic Complexity = 5
     */
    public function generateBatch(int $jenisBiayaId, string $semester, ?string $prodi = null, ?string $angkatan = null, ?float $customNominal = null): array {
        $stmtB = $this->db->prepare('SELECT * FROM jenis_biaya WHERE id = :id');
        $stmtB->execute(['id' => $jenisBiayaId]);
        $biaya = $stmtB->fetch();
        if (!$biaya) {
            return ['success' => false, 'status_code' => 404, 'message' => 'Jenis biaya tidak ditemukan'];
        }

        $nominal = ($customNominal !== null && $customNominal > 0) ? $customNominal : (float)$biaya['nominal_default'];

        $sql = 'SELECT id FROM mahasiswa WHERE status = "aktif"';
        $params = [];
        if (!empty($prodi)) {
            $sql .= ' AND prodi = :prodi';
            $params['prodi'] = $prodi;
        }
        if (!empty($angkatan)) {
            $sql .= ' AND angkatan = :angkatan';
            $params['angkatan'] = $angkatan;
        }

        $stmtM = $this->db->prepare($sql);
        $stmtM->execute($params);
        $mahasiswaList = $stmtM->fetchAll();

        $created = 0;
        $skipped = 0;

        foreach ($mahasiswaList as $m) {
            $res = $this->createTagihan([
                'mahasiswa_id' => $m['id'],
                'jenis_biaya_id' => $jenisBiayaId,
                'semester' => $semester,
                'nominal' => $nominal
            ]);
            if ($res['success']) {
                $created++;
            } else {
                $skipped++;
            }
        }

        return [
            'success' => true,
            'status_code' => 200,
            'message' => "Batch selesai: {$created} tagihan dibuat, {$skipped} dilewati (sudah ada/duplikat)",
            'created_count' => $created,
            'skipped_count' => $skipped
        ];
    }

    /**
     * Get invoices by student
     * Cyclomatic Complexity = 4
     */
    public function getTagihanMahasiswa(int $mahasiswaId, ?string $status = null): array {
        $sql = 'SELECT t.*, jb.nama as nama_biaya, jb.kode as kode_biaya, m.nama as nama_mahasiswa, m.nim
                FROM tagihan t
                JOIN jenis_biaya jb ON t.jenis_biaya_id = jb.id
                JOIN mahasiswa m ON t.mahasiswa_id = m.id
                WHERE t.mahasiswa_id = :mid';
        $params = ['mid' => $mahasiswaId];

        if (!empty($status)) {
            $sql .= ' AND t.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY t.tanggal_terbit DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}

