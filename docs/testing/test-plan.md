# SOFTWARE TEST PLAN (IEEE 829 FORMAT)
## SIPERMA – Sistem Informasi ERP Pembayaran dan Keuangan Mahasiswa

| Informasi Dokumen | Detail |
|-------------------|--------|
| **Test Plan ID** | TP-SIPERMA-2026-V1 |
| **Tanggal Terbit** | 5 November 2026 |
| **Penyusun** | QA Lead & DevOps Engineer |
| **Siklus Rilis** | Release Candidate 1 (RC1) |

---

## 1. TUJUAN TEST PLAN
Dokumen ini menetapkan rencana komprehensif untuk memverifikasi dan memvalidasi fungsionalitas, performa, keamanan, dan keandalan sistem SIPERMA ERP sebelum diserahterimakan kepada pengguna akhir.

---

## 2. RUANG LINGKUP PENGUJIAN (TEST SCOPE)

### 2.1 Fitur yang Diuji (In-Scope)
1. **Modul Autentikasi & RBAC:**
   - Multi-role login: Mahasiswa, Bendahara, Administrator, Pimpinan.
   - Proteksi session & JWT token authentication.
   - Hak akses rute dan isolasi data per mahasiswa.
2. **Modul Mahasiswa:**
   - Dashboard ringkasan keuangan dan status tagihan.
   - Upload bukti transfer bank.
   - Pelacakan riwayat transaksi real-time.
   - Cetak kwitansi sah terverifikasi.
3. **Modul Bendahara:**
   - Antrean pembayaran masuk (*Pending approval*).
   - Preview bukti transfer.
   - Approval persetujuan pelunasan kas.
   - Reject pembayaran dengan catatan alasan wajib.
   - Laporan rekapitulasi penerimaan harian/bulanan.
4. **Modul Administrator:**
   - CRUD Master Mahasiswa (NIM unik, prodi, angkatan).
   - CRUD Master Jenis Biaya (UKT, Praktikum, Wisuda).
   - Generator tagihan individual & batch angkatan.
   - Manajemen user, role assignment, dan status aktif/non-aktif.
5. **Modul Pimpinan:**
   - Executive Dashboard KPI real-time.
   - Laporan penerimaan kas dan laporan tunggakan mahasiswa.
   - Visualisasi grafik statistik keuangan (Chart.js).

### 2.2 Fitur yang Tidak Diuji (Out-of-Scope)
- Integrasi Payment Gateway pihak ketiga secara *live network* (pengujian menggunakan metode transfer bank manual yang disimulasikan).
- Sistem absensi perkuliahan (di luar domain keuangan/ERP pembayaran).

---

## 3. PENDEKATAN & METODOLOGI PENGUJIAN
1. **Black Box Testing:**
   - *Equivalence Partitioning (EP):* Pembagian kelas valid dan invalid pada form input.
   - *Boundary Value Analysis (BVA):* Pengujian batas nominal (0, nominal tagihan, nominal tagihan + 1) dan batas ukuran file (1.99MB, 2MB, 2.01MB).
   - *Decision Table Testing:* Kombinasi aksi approval dan penolakan bersyarat pada proses verifikasi bendahara.
2. **White Box Testing:**
   - Analisis *Control Flow Graph (CFG)*.
   - Pengukuran *Cyclomatic Complexity (CC)* pada `AuthService`, `TagihanService`, `PaymentService`, dan `VerificationService`.
   - Analisis *Statement Coverage* dan *Branch Coverage*.
3. **API Integration Testing:**
   - Pengujian RESTful endpoints menggunakan Postman dan automated HTTP runners.
4. **Defect Management:**
   - Klasifikasi bug berdasarkan *Severity* (Critical, High, Medium, Low) dan *Priority*.

---

## 4. JADWAL & FASE PENGUJIAN

| Fase / Pertemuan | Aktivitas Utama | Output Dokumen / Deliverable |
|------------------|-----------------|-----------------------------|
| **Minggu 1** | Fundamental SQA, V-Model, Test Strategy & Scope | `test-strategy.md`, `test-plan.md`, `test-scope.md` |
| **Minggu 2** | Test Planning, Test Cases & Traceability Matrix | `test-cases.md`, `traceability-matrix.md` |
| **Minggu 3** | Black Box Testing (EP, BVA, Decision Table) | `black-box-testing.md`, Bug Reports |
| **Minggu 4** | White Box Testing (Complexity & Coverage) | `white-box-testing.md`, Jest/PHP test suites |
| **Minggu 5** | API Testing (Postman, Newman, OpenAPI) | `openapi.json`, `postman-collection.json` |
| **Minggu 6** | Automated E2E Testing (Playwright) & Defect Close | `playwright-e2e.spec.js`, Regression Report |

---

## 5. LINGKUNGAN PENGUJIAN (TEST ENVIRONMENT)
- **Sistem Operasi:** Windows 10/11 x64
- **Runtime Backend:** PHP 8.2 (CLI & Built-in Server) + SQLite PDO Driver
- **Runtime Testing:** Node.js (Jest, Playwright) + PHP Test Runner
- **Database:** SQLite 3 (`database/database.sqlite`)
- **Web Browser:** Google Chrome, Microsoft Edge
- **Web Server Local:** `http://localhost:8000`

---

## 6. TIM & TANGGUNG JAWAB
- **QA Lead:** Bertanggung jawab atas perencanaan strategi, review test cases, dan pengesahan laporan akhir.
- **Software Tester:** Menjalankan test case manual, black box testing, dan pencatatan bug.
- **Automation / DevOps Engineer:** Menyusun script automated unit test, Newman collection, dan Playwright E2E.
- **SQA Analyst:** Menghitung metrik kompleksitas siklomatis, coverage code, dan UAT score.

