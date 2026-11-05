# REQUIREMENT TRACEABILITY MATRIX (RTM)
## SIPERMA – Sistem Informasi ERP Pembayaran dan Keuangan Mahasiswa

| Informasi Matriks | Detail |
|-------------------|--------|
| **Cakupan Kebutuhan** | 100% Core Requirements Covered |
| **Arah Pelacakan** | Bidirectional (Forward: Req -> UC -> TC -> Result; Backward: TC -> Req) |
| **Status Verifikasi** | All Mapped Requirements Tested & Verified |

---

## 1. TABEL TRACEABILITY MATRIX (RTM)

| Requirement ID | Deskripsi Kebutuhan Bisnis | Use Case ID | Test Case ID | Metode Pengujian | Hasil Uji | Status Kepatuhan |
|---|---|---|---|---|---|---|
| **REQ-AUTH-01** | Pengguna harus dapat melakukan login aman menggunakan kredensial terdaftar | UC-01 (Login) | TC-AUTH-01<br>TC-AUTH-02<br>TC-AUTH-03<br>TC-AUTH-04<br>TC-AUTH-05 | White Box / Unit / API | Passed | **VERIFIED** |
| **REQ-AUTH-02** | Sistem menerapkan Role-Based Access Control (RBAC) 4 tingkatan | UC-01 (RBAC Guard) | TC-AUTH-06<br>TC-AUTH-07 | Unit & Security Testing | Passed | **VERIFIED** |
| **REQ-AUTH-03** | Pengguna dapat mengakhiri sesi kerja dengan aman (Logout) | UC-01 (Logout) | TC-AUTH-08 | System / Session Test | Passed | **VERIFIED** |
| **REQ-MHS-01** | Mahasiswa dapat memantau ringkasan tagihan & pembayaran pada dashboard | UC-04 (Dashboard Mhs) | TC-MHS-01 | Integration / UI | Passed | **VERIFIED** |
| **REQ-MHS-02** | Mahasiswa dapat melihat rincian seluruh tagihan aktif semester berjalan | UC-04 (View Tagihan) | TC-MHS-02 | System / Database | Passed | **VERIFIED** |
| **REQ-MHS-03** | Mahasiswa dapat mengunggah bukti transfer (JPG/PNG/PDF maks 2MB) | UC-04 (Upload Bukti) | TC-MHS-03<br>TC-MHS-04<br>TC-MHS-05<br>TC-MHS-06<br>TC-MHS-07<br>TC-MHS-08 | Black Box (EP/BVA) / Service | Passed | **VERIFIED** |
| **REQ-MHS-04** | Mahasiswa dapat melihat histori riwayat pembayaran beserta statusnya | UC-04 (Riwayat Bayar)| TC-MHS-09 | System / Query Test | Passed | **VERIFIED** |
| **REQ-MHS-05** | Mahasiswa dapat mencetak bukti sah pelunasan (Kwitansi resmi) | UC-04 (Cetak Bukti) | TC-MHS-10 | UI / Print Render | Passed | **VERIFIED** |
| **REQ-BND-01** | Bendahara menerima notifikasi dan daftar antrean verifikasi pembayaran | UC-05 (Antrean Kas) | TC-BND-01 | Integration / Query | Passed | **VERIFIED** |
| **REQ-BND-02** | Bendahara dapat memeriksa file bukti transfer sebelum mengambil keputusan | UC-05 (Preview Bukti)| TC-BND-02 | UI Modal / File Test | Passed | **VERIFIED** |
| **REQ-BND-03** | Bendahara dapat menyetujui (approve) atau menolak (reject + catatan wajib)| UC-05 (Verifikasi Kas)| TC-BND-03<br>TC-BND-04<br>TC-BND-05 | Decision Table / White Box | Passed | **VERIFIED** |
| **REQ-BND-04** | Bendahara dapat melihat rekapitulasi realisasi penerimaan kas | UC-06 (Laporan Kas) | TC-BND-06 | Aggregation / Report | Passed | **VERIFIED** |
| **REQ-ADM-01** | Administrator dapat mengelola data mahasiswa (CRUD) tanpa duplikasi NIM | UC-02 (CRUD Mhs) | TC-ADM-01<br>TC-ADM-02<br>TC-ADM-03 | Database / API / CRUD | Passed | **VERIFIED** |
| **REQ-ADM-02** | Administrator dapat mengelola master jenis biaya akademik | UC-02 (CRUD Biaya) | TC-ADM-04 | Database / CRUD | Passed | **VERIFIED** |
| **REQ-ADM-03** | Administrator dapat menerbitkan tagihan individual & batch massal | UC-03 (Gen Tagihan) | TC-ADM-05<br>TC-ADM-06<br>TC-ADM-07 | Service / Batch Engine | Passed | **VERIFIED** |
| **REQ-ADM-04** | Administrator dapat mengelola akun pengguna dan hak akses role | UC-01 (Manage User) | TC-ADM-08<br>TC-ADM-09 | RBAC / Auth Service | Passed | **VERIFIED** |
| **REQ-EXC-01** | Pimpinan dapat melihat executive dashboard KPI realisasi vs target kas | UC-06 (Dashboard KPI)| TC-EXC-01 | Data Analytics / UI | Passed | **VERIFIED** |
| **REQ-EXC-02** | Pimpinan dapat mengakses laporan audit penerimaan kas mahasiswa | UC-06 (Laporan Kas) | TC-EXC-02 | Financial Report | Passed | **VERIFIED** |
| **REQ-EXC-03** | Pimpinan dapat melihat daftar mahasiswa yang memiliki tunggakan | UC-06 (Lap Tunggakan)| TC-EXC-03 | Filter / Report | Passed | **VERIFIED** |
| **REQ-EXC-04** | Pimpinan dapat melihat visualisasi grafik statistik keuangan | UC-06 (Chart Stat) | TC-EXC-04 | Chart.js Visualization | Passed | **VERIFIED** |

---

## 2. ANALISIS KELENGKAPAN REQUIREMENT
- **Total Requirement Didefinisikan:** 17 Butir
- **Requirement yang Memiliki Minimal 1 Test Case:** 17 Butir (100%)
- **Requirement Belum Teruji:** 0 Butir
- **Kesimpulan:** Seluruh requirement fungsional sistem SIPERMA ERP telah terpetakan dan terverifikasi secara penuh tanpa ada bagian yang terlewat.

