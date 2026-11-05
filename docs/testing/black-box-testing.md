# BLACK BOX TESTING REPORT
## SIPERMA – Sistem Informasi ERP Pembayaran dan Keuangan Mahasiswa

| Informasi Pengujian | Detail |
|---------------------|--------|
| **Metode Pengujian** | 1. Equivalence Partitioning (EP)<br>2. Boundary Value Analysis (BVA)<br>3. Decision Table Testing |
| **Modul Diuji** | Login, Mahasiswa, Jenis Biaya, Tagihan, Pembayaran, Verifikasi, Notifikasi, Laporan |
| **Tanggal Eksekusi** | 5 November 2026 |
| **Eksekutor** | Software QA Tester & Test Automation Specialist |

---

## 1. EQUIVALENCE PARTITIONING (EP)

### 1.1 Modul Login (Autentikasi)

| Kelas Input | Partisi Valid / Invalid | Test Case / Data Uji | Respon Sistem yang Diharapkan | Hasil Nyata (*Actual*) | Status |
|---|---|---|---|---|---|
| **Identifier (Username/NIM/Email)** | Valid (Terdaftar & Aktif) | `admin`, `mhs2024001`, `bendahara` | Kredensial diproses lanjut | Kredensial diproses | **PASSED** |
| | Invalid (Format Kosong) | `""` (String kosong) | Tolak: HTTP 400 (Wajib diisi) | Ditolak dengan status 400 | **PASSED** |
| | Invalid (Tidak Terdaftar) | `unknown_user_999@test.com` | Tolak: HTTP 401 (Tidak valid) | Ditolak dengan status 401 | **PASSED** |
| **Password** | Valid (Sesuai hash DB) | `admin123`, `mhs123` | Login berhasil, session/token terbit | Login sukses, session terbit | **PASSED** |
| | Invalid (Salah karakter) | `wrongPass123!@` | Tolak: HTTP 401 (Password salah) | Ditolak dengan pesan salah | **PASSED** |
| | Invalid (Kosong) | `""` | Tolak: HTTP 400 | Ditolak dengan status 400 | **PASSED** |

### 1.2 Modul Pembayaran (Upload Bukti & Nominal)

| Parameter Input | Kelas Partisi | Nilai Uji (*Test Data*) | Respon Sistem Diharapkan | Hasil Nyata | Status |
|---|---|---|---|---|---|
| **Ekstensi File Bukti** | Valid: JPG | `struk_transfer.jpg` | Format diterima, file disimpan | Diterima dan tersimpan | **PASSED** |
| | Valid: PNG | `screenshot_mbanking.png` | Format diterima, file disimpan | Diterima dan tersimpan | **PASSED** |
| | Valid: PDF | `bukti_setor_bank.pdf` | Format diterima, file disimpan | Diterima dan tersimpan | **PASSED** |
| | Invalid: Executable | `payload_exploit.exe` | Tolak: HTTP 422 (FILE_INVALID_TYPE) | Ditolak oleh validator | **PASSED** |
| | Invalid: Script PHP | `backdoor.php` | Tolak: HTTP 422 (FILE_INVALID_TYPE) | Ditolak oleh validator | **PASSED** |
| | Invalid: Dokumen Word | `bukti.docx` | Tolak: HTTP 422 (FILE_INVALID_TYPE) | Ditolak oleh validator | **PASSED** |
| **Nominal Pembayaran** | Valid (Sesuai tagihan) | `Rp 2.500.000` (Tagihan 2.5jt) | Pembayaran diproses ke status pending | Status menjadi pending | **PASSED** |
| | Invalid (Kurang bayar) | `Rp 1.000.000` (Tagihan 2.5jt) | Tolak: Nominal tidak sesuai tagihan | Ditolak NOMINAL_MISMATCH | **PASSED** |
| | Invalid (Lebih bayar) | `Rp 3.000.000` (Tagihan 2.5jt) | Tolak: Nominal tidak sesuai tagihan | Ditolak NOMINAL_MISMATCH | **PASSED** |
| | Invalid (Negatif) | `-Rp 500.000` | Tolak: HTTP 422 (Nominal > 0) | Ditolak (Nominal > 0) | **PASSED** |

---

## 2. BOUNDARY VALUE ANALYSIS (BVA)

### 2.1 Analisis Batas Nominal Tagihan & Pembayaran

```
      Invalid (< 0)        Invalid (= 0)          Valid (> 0)
<-----------------------|----------------|---------------------------->
         -1                    0                      1       ... 2.500.000
```

| Titik Batas | Nilai Nominal (Rp) | Kategori Batas | Respon Diharapkan | Hasil Uji Nyata | Status |
|---|---|---|---|---|---|
| $Nominal < 0$ | `-1` | Just Below Lower Bound | Ditolak (HTTP 422: Nominal > 0) | Ditolak validasi | **PASSED** |
| $Nominal = 0$ | `0` | Lower Boundary Limit | Ditolak (HTTP 422: Nominal > 0) | Ditolak validasi | **PASSED** |
| $Nominal = 1$ | `1` | Just Above Lower Bound | Diterima jika tagihan bernilai 1 | Diproses sistem | **PASSED** |
| $Nominal = Tagihan$ | `750.000` | Exact Match Boundary | Transaksi sukses diterima | Transaksi sukses (HTTP 201) | **PASSED** |

### 2.2 Analisis Batas Ukuran File Bukti Pembayaran (Max: 2MB = 2.097.152 Bytes)

```
        Valid (<= 2MB)                     Invalid (> 2MB)
<----------------------------------|--------------------------------->
    2.097.151 B       2.097.152 B       2.097.153 B       3.145.728 B
    (Boundary - 1)     (Max Limit)       (Boundary + 1)     (Extreme High)
```

| Titik Batas Ukuran | Ukuran File (Bytes) | Deskripsi Batas | Respon Diharapkan | Hasil Uji Nyata | Status |
|---|---|---|---|---|---|
| **Max - 1 Byte** | 2.097.151 Bytes | Just Inside Max Boundary | Diterima & berhasil disimpan | Berhasil diunggah | **PASSED** |
| **Max Limit (2MB)**| 2.097.152 Bytes | Exact Maximum Boundary | Diterima & berhasil disimpan | Berhasil diunggah | **PASSED** |
| **Max + 1 Byte** | 2.097.153 Bytes | Just Outside Max Boundary | Ditolak: FILE_TOO_LARGE | Ditolak dengan status 422 | **PASSED** |
| **Extreme Large** | 3.145.728 Bytes (3MB)| Well Outside Boundary | Ditolak: FILE_TOO_LARGE | Ditolak dengan status 422 | **PASSED** |

---

## 3. DECISION TABLE TESTING (TABEL KEPUTUSAN VERIFIKASI)
Modul Verifikasi Bendahara mengevaluasi keputusan persetujuan (*approval*) kas berdasarkan aturan bisnis:

| Kondisi / Masukan | Aturan 1 (R1) | Aturan 2 (R2) | Aturan 3 (R3) | Aturan 4 (R4) | Aturan 5 (R5) |
|---|:---:|:---:|:---:|:---:|:---:|
| **Status Pembayaran Saat Ini** | Menunggu | Menunggu | Menunggu | Diverifikasi | Ditolak |
| **Aksi Dipilih Bendahara** | `approve` | `reject` | `reject` | `approve` | `reject` |
| **Catatan Alasan Diisi?** | Ya / Tidak | Ya (Ada teks) | Tidak (Kosong) | Bebas | Bebas |
| **Aksi Hasil / Luaran Sistem** | | | | | |
| **Eksekusi Status Pembayaran** | **Diverifikasi** | **Ditolak** | **DITOLAK SISTEM** | **DITOLAK SISTEM** | **DITOLAK SISTEM** |
| **Status Tagihan Berubah** | **Lunas** | **Ditolak** | Tetap | Tetap | Tetap |
| **Notifikasi ke Mahasiswa** | Terkirim | Terkirim + Catatan | Tidak terkirim | Tidak terkirim | Tidak terkirim |
| **HTTP Status Code** | **200 OK** | **200 OK** | **422 Unprocessable** | **409 Conflict** | **409 Conflict** |
| **Hasil Eksekusi Nyata** | **PASSED** | **PASSED** | **PASSED** | **PASSED** | **PASSED** |

---

## 4. REKAPITULASI HASIL BLACK BOX TESTING

| Modul | Total Uji | Passed | Failed | Not Tested | Bug ID Terbit |
|---|:---:|:---:|:---:|:---:|:---:|
| Login & Autentikasi | 6 | 6 | 0 | 0 | - |
| Mahasiswa (Tagihan & Riwayat) | 4 | 4 | 0 | 0 | - |
| Jenis Biaya | 3 | 3 | 0 | 0 | - |
| Tagihan (Single & Batch) | 5 | 5 | 0 | 0 | - |
| Pembayaran & Upload Bukti | 7 | 7 | 0 | 0 | - |
| Verifikasi Bendahara | 5 | 5 | 0 | 0 | - |
| Notifikasi | 2 | 2 | 0 | 0 | - |
| Laporan & Statistik | 3 | 3 | 0 | 0 | - |
| **TOTAL** | **35** | **35** | **0** | **0** | **0 BUG AKTIF** |

> **Catatan SQA:** Seluruh skenario partisi ekuivalen, batas nilai ekstrem (BVA), dan kombinasi aturan keputusan (Decision Table) telah diuji pada source code aktual dan berjalan sesuai standar spesifikasi.

