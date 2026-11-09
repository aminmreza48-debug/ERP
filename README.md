# SIPERMA ERP
### Sistem Informasi ERP Pembayaran dan Keuangan Mahasiswa

[![SIPERMA ERP CI/CD](https://github.com/aminmreza48-debug/ERP/actions/workflows/ci.yml/badge.svg)](https://github.com/aminmreza48-debug/ERP/actions/workflows/ci.yml)
[![Tests Passed](https://img.shields.io/badge/SQA%20Tests-29%2F29%20Passed-brightgreen)](https://github.com/aminmreza48-debug/ERP)
[![Coverage](https://img.shields.io/badge/Jest%20Coverage-100%25-brightgreen)](https://github.com/aminmreza48-debug/ERP)

SIPERMA ERP adalah aplikasi enterprise berbasis web dan RESTful API untuk mengelola transaksi keuangan perkuliahan, penagihan mahasiswa, verifikasi kas oleh bendahara, dan analitik laporan keuangan eksekutif bagi pimpinan perguruan tinggi.

---

## 👥 Akun Demo Pengujian (Multi-Role)

| Role | Username | Password | Deskripsi Tugas Utama |
|---|---|---|---|
| **Mahasiswa** | `mhs2024001` | `mhs123` | Melihat tagihan, upload bukti transfer, cek status, cetak kwitansi sah |
| **Bendahara** | `bendahara` | `bendahara123` | Melihat pembayaran masuk, preview bukti, approve/reject, buat laporan |
| **Administrator** | `admin` | `admin123` | CRUD mahasiswa, CRUD jenis biaya, terbitkan tagihan (single/batch), kelola user |
| **Pimpinan** | `pimpinan` | `pimpinan123` | Dashboard KPI, laporan realisasi kas, laporan tunggakan, grafik statistik |

---

## 🚀 Panduan Menjalankan Aplikasi

### 1. Inisialisasi Database (SQLite)
```bash
# Menyiapkan skema dan data awal demo
"C:\xampp\php\php.exe" database/init-db.php
```

### 2. Menjalankan Web Server Lokal
```bash
# Jalankan built-in PHP server pada port 8080
"C:\xampp\php\php.exe" -S 0.0.0.0:8080 -t public
# Atau cukup klik 2x file run.bat di root direktori project
```
Buka browser ke: **`http://localhost:8080`**

---

## 🔄 CI/CD Automation Pipeline (GitHub Actions)
Aplikasi ini dilengkapi dengan pipeline CI/CD otomatis di `.github/workflows/ci.yml` yang mencakup:
- **Linting & Syntax Check**: Pengecekan sintaks PHP menyeluruh.
- **SQA Phase 1-4**: Unit testing, Black Box (EP, BVA, Decision Table), White Box, dan Analisis Cyclomatic Complexity.
- **Phase 4**: Jest Code Coverage pada seluruh business service logic.
- **Phase 5**: REST API Testing otomatis dengan Newman / Postman Collection.
- **Phase 6**: Automated E2E Testing multi-role dengan Playwright.
```
Akses aplikasi melalui browser: **`http://localhost:8000`**

---

## 🧪 Menjalankan Pengujian (SQA Testing Suite)

### 1. Automated SQA Test Runner (Unit & Service Tests)
Menjalankan 29 automated test cases (White Box, Black Box EP/BVA, Decision Table, dan Database Integrity):
```bash
"C:\xampp\php\php.exe" tests/test-runner.php
```

### 2. White Box Testing dengan Jest
```bash
npm install
npm test
```

### 3. API Integration Testing dengan Newman / Postman
```bash
npx newman run docs/testing/postman-collection.json
```

### 4. End-to-End Testing dengan Playwright
```bash
npx playwright test docs/testing/playwright-e2e.spec.js
```

---

## 📚 Struktur Dokumentasi Pengujian Mingguan (Phases 1 - 6)

- **Phase 1 (Fundamental SQA, V-Model & Test Strategy):**
  - [`docs/testing/test-strategy.md`](docs/testing/test-strategy.md)
  - [`docs/testing/test-plan.md`](docs/testing/test-plan.md)
  - [`docs/testing/test-scope.md`](docs/testing/test-scope.md)
- **Phase 2 (Test Cases & Traceability Matrix):**
  - [`docs/testing/test-cases.md`](docs/testing/test-cases.md)
  - [`docs/testing/traceability-matrix.md`](docs/testing/traceability-matrix.md)
- **Phase 3 (Black Box Testing - EP, BVA, Decision Table):**
  - [`docs/testing/black-box-testing.md`](docs/testing/black-box-testing.md)
- **Phase 4 (White Box Testing - Jest, Complexity & Coverage):**
  - [`docs/testing/white-box-testing.md`](docs/testing/white-box-testing.md)
- **Phase 5 (API Testing - Swagger & Postman):**
  - [`docs/api/openapi.json`](docs/api/openapi.json)
  - [`docs/testing/postman-collection.json`](docs/testing/postman-collection.json)
- **Phase 6 (Automated E2E Testing - Playwright):**
  - [`docs/testing/playwright-e2e.spec.js`](docs/testing/playwright-e2e.spec.js)

