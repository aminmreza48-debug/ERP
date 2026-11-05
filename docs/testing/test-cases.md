# COMPREHENSIVE TEST CASES SPECIFICATION
## SIPERMA – Sistem Informasi ERP Pembayaran dan Keuangan Mahasiswa

| Informasi Pengujian | Detail |
|---------------------|--------|
| **Total Test Case** | 35 Formal Test Cases |
| **Metodologi** | Hybrid (Manual + Automated Execution) |
| **Tanggal Eksekusi** | 5 November 2026 |
| **Status Keseluruhan**| 100% Executed on Real Source Code |

---

## 1. TEST CASES: MODUL AUTENTIKASI & USER (AUTH)

| ID | Requirement ID | Module | Feature | Test Scenario | Precondition | Test Data | Test Steps | Expected Result | Actual Result | Status | Evidence |
|---|---|---|---|---|---|---|---|---|---|---|---|
| **TC-AUTH-01** | REQ-AUTH-01 | Auth | Login | Login dengan field kosong | Halaman login terbuka | username="", password="" | 1. Kosongkan field<br>2. Klik tombol Masuk | Tampil validasi error: Username dan password wajib diisi (HTTP 400) | Validasi menolak input kosong dengan status 400 | **PASSED** | Unit test TC-AUTH-01 log |
| **TC-AUTH-02** | REQ-AUTH-01 | Auth | Login | Login username tidak terdaftar | Halaman login terbuka | username="unknown", password="any" | 1. Input username fiktif<br>2. Klik Masuk | Tampil pesan: Kredensial login tidak valid (HTTP 401) | Sistem menolak dengan pesan kredensial tidak valid | **PASSED** | Unit test TC-AUTH-02 log |
| **TC-AUTH-03** | REQ-AUTH-01 | Auth | Login | Login password salah | Akun admin terdaftar | username="admin", password="wrong" | 1. Input username admin<br>2. Input password salah<br>3. Klik Masuk | Tampil pesan: Password yang dimasukkan salah (HTTP 401) | Sistem menolak dengan pesan password salah | **PASSED** | Unit test TC-AUTH-03 log |
| **TC-AUTH-04** | REQ-AUTH-01 | Auth | Login | Login sukses role Admin | Akun admin aktif | username="admin", password="admin123" | 1. Input username & password valid<br>2. Klik Masuk | Login sukses (HTTP 200), terbit session/token, redirect ke dashboard | Login berhasil, redirect ke /admin/dashboard | **PASSED** | Unit test TC-AUTH-04 log |
| **TC-AUTH-05** | REQ-AUTH-01 | Auth | Login | Login sukses role Mahasiswa | Akun mhs aktif | username="mhs2024001", password="mhs123" | 1. Input NIM & password valid<br>2. Klik Masuk | Login sukses (HTTP 200), redirect ke /mahasiswa/dashboard | Login berhasil, redirect ke dashboard mahasiswa | **PASSED** | Unit test TC-AUTH-05 log |
| **TC-AUTH-06** | REQ-AUTH-02 | Auth | RBAC | Admin universal access | Admin login | Role="admin" | 1. Admin akses rute bendahara | Akses diizinkan (Universal permission) | Akses diizinkan | **PASSED** | Unit test TC-AUTH-06 log |
| **TC-AUTH-07** | REQ-AUTH-02 | Auth | RBAC | Mahasiswa akses rute bendahara | Mahasiswa login | Role="mahasiswa" | 1. Mahasiswa akses /bendahara/verifikasi | Akses ditolak dengan HTTP 403 Forbidden | Akses ditolak, return 403 Forbidden | **PASSED** | Unit test TC-AUTH-08 log |
| **TC-AUTH-08** | REQ-AUTH-03 | Auth | Logout | Logout sesi pengguna | Pengguna sedang login | Klik tombol Logout | 1. Klik Logout di navbar | Session dihapus, redirect ke halaman login | Session terhapus, redirect ke /login | **PASSED** | Router test log |

---

## 2. TEST CASES: MODUL MAHASISWA (STUDENT)

| ID | Requirement ID | Module | Feature | Test Scenario | Precondition | Test Data | Test Steps | Expected Result | Actual Result | Status | Evidence |
|---|---|---|---|---|---|---|---|---|---|---|---|
| **TC-MHS-01** | REQ-MHS-01 | Mahasiswa | Dashboard | Membuka dashboard mahasiswa | Login sebagai mhs2024001 | Sesi aktif | 1. Akses /mahasiswa/dashboard | Tampil ringkasan: Total Tagihan, Terbayar, Menunggu, Sisa Tunggakan | Dashboard menampilkan seluruh card KPI secara tepat | **PASSED** | Browser render index.php |
| **TC-MHS-02** | REQ-MHS-02 | Mahasiswa | Tagihan | Melihat daftar tagihan aktif | Terdapat tagihan di DB | NIM 202401001 | 1. Akses halaman Tagihan | Tampil daftar tagihan, komponen, nominal, dan status | Daftar tagihan tampil lengkap | **PASSED** | SQL Query check & UI |
| **TC-MHS-03** | REQ-MHS-03 | Mahasiswa | Upload Bukti | Upload bukti pembayaran valid | Tagihan status belum_bayar | Nominal=750000, File="bukti.jpg" (500KB) | 1. Pilih file JPG<br>2. Submit form | Status tagihan & pembayaran menjadi "menunggu_verifikasi" (HTTP 201) | Pembayaran tersimpan, status menunggu verifikasi | **PASSED** | Unit test TC-PAY-08 log |
| **TC-MHS-04** | REQ-MHS-03 | Mahasiswa | Upload Bukti | Upload bukti file kosong | Tagihan belum_bayar | File=null | 1. Submit form tanpa file | Validasi menolak: File bukti transfer wajib diunggah (HTTP 422) | Ditolak dengan pesan file wajib | **PASSED** | Unit test TC-PAY-01 log |
| **TC-MHS-05** | REQ-MHS-03 | Mahasiswa | Upload Bukti | Upload file > 2MB (BVA) | Tagihan belum_bayar | File="scan.pdf" (3MB) | 1. Upload file 3MB<br>2. Submit | Ditolak: Ukuran file melebihi batas maksimum 2MB (HTTP 422) | Ditolak dengan kode FILE_TOO_LARGE | **PASSED** | Unit test TC-PAY-02 log |
| **TC-MHS-06** | REQ-MHS-03 | Mahasiswa | Upload Bukti | Upload ekstensi tidak aman (.exe) | Tagihan belum_bayar | File="malware.exe" | 1. Upload file exe<br>2. Submit | Ditolak: Format file tidak didukung (HTTP 422) | Ditolak dengan kode FILE_INVALID_TYPE | **PASSED** | Unit test TC-PAY-03 log |
| **TC-MHS-07** | REQ-MHS-03 | Mahasiswa | Upload Bukti | Upload nominal tidak sesuai tagihan | Tagihan nominal 750.000 | Input nominal=500.000 | 1. Input nominal salah<br>2. Submit | Ditolak: Nominal pembayaran harus sesuai jumlah tagihan | Ditolak dengan kode NOMINAL_MISMATCH | **PASSED** | Unit test TC-PAY-05 log |
| **TC-MHS-08** | REQ-MHS-03 | Mahasiswa | Upload Bukti | Bayar tagihan yang sudah lunas | Tagihan status lunas | Tagihan ID 1 | 1. Coba submit bayar tagihan lunas | Ditolak: Tagihan sudah lunas (HTTP 400) | Ditolak dengan kode TAGIHAN_ALREADY_PAID | **PASSED** | Unit test TC-PAY-07 log |
| **TC-MHS-09** | REQ-MHS-04 | Mahasiswa | Riwayat | Melihat riwayat transaksi | Transaksi pernah dilakukan | Mahasiswa ID 1 | 1. Buka /mahasiswa/riwayat | Tampil tabel riwayat transaksi lengkap dengan status dan catatan | Riwayat transaksi terurut DESC tampil akurat | **PASSED** | Database & UI verify |
| **TC-MHS-10** | REQ-MHS-05 | Mahasiswa | Kwitansi | Mencetak kwitansi sah | Tagihan berstatus lunas | Tagihan ID 1 | 1. Klik tombol Kwitansi / Cetak | Tampil halaman kwitansi resmi dengan nomor transaksi, stempel lunas, & NIP | Dokumen kwitansi sah ter-render rapi | **PASSED** | View cetak.php output |

---

## 3. TEST CASES: MODUL BENDAHARA (TREASURER)

| ID | Requirement ID | Module | Feature | Test Scenario | Precondition | Test Data | Test Steps | Expected Result | Actual Result | Status | Evidence |
|---|---|---|---|---|---|---|---|---|---|---|---|
| **TC-BND-01** | REQ-BND-01 | Bendahara | Dashboard | Membuka dashboard bendahara | Login bendahara | Sesi bendahara | 1. Akses /bendahara/dashboard | Tampil counter antrean verifikasi dan total realisasi | Dashboard menampilkan antrean verifikasi | **PASSED** | UI view render |
| **TC-BND-02** | REQ-BND-02 | Bendahara | Verifikasi | Membuka preview bukti transfer | Ada pembayaran pending | Pembayaran ID 2 | 1. Klik Buka Bukti di tabel | Modal preview terbuka menampilkan link/file bukti transfer | Modal preview bukti terbuka normal | **PASSED** | Modal popup check |
| **TC-BND-03** | REQ-BND-03 | Bendahara | Verifikasi | Approve pembayaran pending | Pembayaran status pending | Action="approve", Catatan="Sah" | 1. Klik tombol Setuju | Status pembayaran menjadi "diverifikasi", tagihan menjadi "lunas" (HTTP 200) | Pembayaran disetujui, tagihan lunas | **PASSED** | Unit test TC-VER-04 log |
| **TC-BND-04** | REQ-BND-03 | Bendahara | Verifikasi | Reject tanpa catatan (Decision Table) | Pembayaran pending | Action="reject", Catatan="" | 1. Tolak tanpa isi alasan | Ditolak: Catatan alasan penolakan wajib diisi (HTTP 422) | Validasi menolak: catatan wajib diisi | **PASSED** | Unit test TC-VER-03 log |
| **TC-BND-05** | REQ-BND-03 | Bendahara | Verifikasi | Reject dengan catatan alasan sah | Pembayaran pending | Action="reject", Catatan="Bukti buram" | 1. Masukkan alasan penolakan<br>2. Klik Konfirmasi Tolak | Status pembayaran & tagihan menjadi "ditolak" (HTTP 200) | Status ditolak dengan catatan terkirim | **PASSED** | VerificationService log |
| **TC-BND-06** | REQ-BND-04 | Bendahara | Laporan | Rekap laporan keuangan kas | Ada data diverifikasi | Periode aktif | 1. Buka /bendahara/laporan | Tampil tabel rekapitulasi penerimaan dengan kalkulasi total matematis akurat | Rekap tabel dan total penerimaan sinkron | **PASSED** | Laporan query & view |

---

## 4. TEST CASES: MODUL ADMINISTRATOR (ADMIN)

| ID | Requirement ID | Module | Feature | Test Scenario | Precondition | Test Data | Test Steps | Expected Result | Actual Result | Status | Evidence |
|---|---|---|---|---|---|---|---|---|---|---|---|
| **TC-ADM-01** | REQ-ADM-01 | Admin | Mahasiswa | Tambah mahasiswa NIM baru | Login admin | NIM="202401099", Nama="Fajar" | 1. Isi form modal<br>2. Klik Simpan | Data tersimpan di database (HTTP 201) | Mahasiswa baru berhasil tersimpan | **PASSED** | API / DB check |
| **TC-ADM-02** | REQ-ADM-01 | Admin | Mahasiswa | Tambah mahasiswa NIM duplikat | NIM sudah terdaftar | NIM="202401001" | 1. Input NIM duplikat<br>2. Submit | Tampil error: NIM sudah terdaftar (HTTP 409) | Ditolak dengan pesan duplikat | **PASSED** | DB Constraint & API |
| **TC-ADM-03** | REQ-ADM-01 | Admin | Mahasiswa | Edit biodata mahasiswa | Mahasiswa ID 1 | Update nama="Ahmad Fauzi, S.Kom" | 1. Edit nama<br>2. Klik Simpan | Data terupdate di database | Data berhasil terupdate | **PASSED** | UI edit modal |
| **TC-ADM-04** | REQ-ADM-02 | Admin | Jenis Biaya | Tambah jenis biaya baru | Login admin | Kode="PKL-02", Nama="Magang" | 1. Isi form modal<br>2. Submit | Jenis biaya baru berhasil disimpan | Data jenis biaya bertambah | **PASSED** | UI biaya view |
| **TC-ADM-05** | REQ-ADM-03 | Admin | Tagihan | Buat tagihan individu | Mahasiswa aktif | NIM 202401001, Biaya=UKT, Sem=2025/1 | 1. Pilih mahasiswa<br>2. Terbitkan | Tagihan terbit (HTTP 201) dengan nomor otomatis TGH-xxxx | Tagihan tercipta dengan nomor unik | **PASSED** | Unit test TC-TAG-04 log |
| **TC-ADM-06** | REQ-ADM-03 | Admin | Tagihan | Cegah tagihan duplikat semester sama | Tagihan sudah ada | Mahasiswa 1, Biaya 1, Semester sama | 1. Coba buat tagihan sama | Ditolak: Tagihan untuk komponen dan semester ini sudah ada (HTTP 409) | Sistem menolak tagihan ganda | **PASSED** | Unit test TC-TAG-05 log |
| **TC-ADM-07** | REQ-ADM-03 | Admin | Tagihan | Generator tagihan massal per angkatan | Mahasiswa aktif | Angkatan="2024", JenisBiaya=1 | 1. Pilih jenis biaya & angkatan<br>2. Klik Generate Batch | Tagihan diterbitkan untuk semua mahasiswa pada angkatan tersebut | Batch tagihan terbuat sesuai filter | **PASSED** | Batch service execution |
| **TC-ADM-08** | REQ-ADM-04 | Admin | User RBAC | Tambah akun user baru | Login admin | username="staff_baru", role="bendahara" | 1. Input form user<br>2. Simpan | User baru tercipta dengan password terenkripsi bcrypt | Akun user baru aktif | **PASSED** | DB verification |
| **TC-ADM-09** | REQ-ADM-04 | Admin | User RBAC | Ubah role hak akses user | User terdaftar | Ubah role dari mahasiswa ke bendahara | 1. Pilih role baru<br>2. Simpan | Hak akses user terupdate sesuai role | Role dan permission terupdate | **PASSED** | RBAC update verify |

---

## 5. TEST CASES: MODUL PIMPINAN (EXECUTIVE)

| ID | Requirement ID | Module | Feature | Test Scenario | Precondition | Test Data | Test Steps | Expected Result | Actual Result | Status | Evidence |
|---|---|---|---|---|---|---|---|---|---|---|---|
| **TC-EXC-01** | REQ-EXC-01 | Pimpinan | Dashboard | Akses executive dashboard | Login pimpinan | Sesi pimpinan | 1. Buka /pimpinan/dashboard | Tampil KPI rasio realisasi kas & chart visualisasi | KPI dan grafik tampil responsif | **PASSED** | Browser render view |
| **TC-EXC-02** | REQ-EXC-02 | Pimpinan | Laporan | Akses laporan penerimaan kas | Transaksi sah ada | Sesi pimpinan | 1. Buka /pimpinan/laporan-pembayaran | Tampil tabel detail realisasi kas | Data transaksi lunas tampil lengkap | **PASSED** | Query filter verify |
| **TC-EXC-03** | REQ-EXC-03 | Pimpinan | Laporan | Akses laporan tunggakan | Ada tagihan belum bayar | Sesi pimpinan | 1. Buka /pimpinan/laporan-tunggakan | Tampil daftar mahasiswa yang memiliki tunggakan beserta jatuh tempo | Daftar tunggakan terdata rapi | **PASSED** | Tunggakan report view |
| **TC-EXC-04** | REQ-EXC-04 | Pimpinan | Statistik | Visualisasi chart analitik keuangan | Data tagihan & bayar | Sesi pimpinan | 1. Buka /pimpinan/statistik | Grafik bar & pie chart Chart.js ter-render interaktif | Chart ter-render tanpa Javascript error | **PASSED** | Canvas chart elements |

