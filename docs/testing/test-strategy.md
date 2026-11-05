# SOFTWARE QUALITY ASSURANCE TEST STRATEGY
## SIPERMA – Sistem Informasi ERP Pembayaran dan Keuangan Mahasiswa

| Atribut Dokumen | Keterangan |
|-----------------|------------|
| **Nama Proyek** | SIPERMA ERP (Sistem Informasi Pembayaran & Keuangan Mahasiswa) |
| **Versi Dokumen** | 1.0.0 |
| **Tanggal Pembuatan** | 5 November 2026 |
| **Standar Acuan** | ISO/IEC/IEEE 29119 Software Testing Standards & IEEE 829 |
| **Penulis** | QA Engineer & SQA Analyst |
| **Status** | Approved & Active |

---

## 1. PENDAHULUAN & LATAR BELAKANG
SIPERMA adalah sistem Enterprise Resource Planning (ERP) tingkat institusi pendidikan tinggi yang mengelola transaksi keuangan akademik, penagihan, pembayaran multi-channel, verifikasi kas oleh bendahara, dan pelaporan keuangan eksekutif bagi pimpinan. Mengingat sistem ini mengelola data sensitif transaksi finansial dan hak akses multi-role (Mahasiswa, Bendahara, Admin, Pimpinan), implementasi Software Quality Assurance (SQA) yang ketat dan terstruktur menjadi kebutuhan mutlak.

---

## 2. PENERAPAN MODEL PENGEMBANGAN: V-MODEL SQA
Pengujian pada proyek SIPERMA mengadopsi siklus **V-Model**, di mana setiap fase pengembangan perangkat lunak diverifikasi dan divalidasi secara paralel oleh fase pengujian yang bersesuaian:

```
[Requirement Analysis (SRS)] <====================> [User Acceptance Testing (UAT)]
         \                                                   /
  [System Design & Architecture] <===========> [System & Integration Testing]
           \                                       /
    [Module / Service Design] <=========> [Unit & White Box Testing]
             \                                 /
              =========> [Coding & Implementation]
```

1. **Software Requirement Analysis <-> User Acceptance Testing (UAT):**
   - Menyelaraskan kebutuhan pemangku kepentingan (Mahasiswa bayar SPP, Bendahara validasi kas) dengan kriteria penerimaan pengguna.
2. **System Architecture Design <-> System & API Testing:**
   - Memastikan interaksi antar subsistem (Web Controller, RESTful API, Database SQLite/MySQL) bekerja tanpa inkonsistensi data.
3. **Module & Service Design <-> Unit / White Box Testing:**
   - Memvalidasi logika bisnis internal pada `AuthService`, `TagihanService`, `PaymentService`, dan `VerificationService`.

---

## 3. ANALISIS KEBUTUHAN SISTEM (SRS, USE CASE, USER STORY, BUSINESS RULES)

### 3.1 Use Case Diagram Mapping
- **UC-01 (Autentikasi & Otorisasi):** Seluruh role login dengan kredensial unik; RBAC (Role-Based Access Control) membatasi akses URL dan API.
- **UC-02 (Manajemen Data Master):** Admin melakukan CRUD Mahasiswa dan Jenis Biaya.
- **UC-03 (Penerbitan Tagihan):** Admin menerbitkan tagihan tunggal dan massal per prodi/angkatan.
- **UC-04 (Pembayaran Mahasiswa):** Mahasiswa melihat tagihan aktif, mengunggah bukti transfer, dan memantau status pembayaran.
- **UC-05 (Verifikasi Pembayaran):** Bendahara memeriksa keabsahan bukti bayar, memberikan persetujuan (*approve*), atau menolak (*reject*) dengan alasan wajib.
- **UC-06 (Pelaporan & Statistik):** Pimpinan dan Bendahara mengakses rekapitulasi realisasi kas dan daftar tunggakan.

### 3.2 User Stories & Acceptance Criteria

#### US-01: Mahasiswa Membayar Tagihan
> *Sebagai Mahasiswa, saya ingin mengunggah bukti transfer pembayaran agar tagihan kuliah saya diproses oleh Bendahara.*
- **Acceptance Criteria:**
  - Hanya dapat membayar tagihan berstatus `belum_bayar` atau `ditolak`.
  - Nominal bayar harus bernilai positif dan sesuai dengan nominal tagihan.
  - File bukti transfer harus berformat JPG, JPEG, PNG, atau PDF dengan ukuran maksimal 2 MB.
  - Setelah berhasil, status tagihan berubah menjadi `menunggu_verifikasi`.

#### US-02: Bendahara Memvalidasi Pembayaran
> *Sebagai Bendahara, saya ingin memverifikasi transaksi pembayaran mahasiswa agar dana tercatat sah dalam kas institusi.*
- **Acceptance Criteria:**
  - Bendahara dapat melihat preview bukti pembayaran.
  - Jika disetujui (*approve*), status pembayaran menjadi `diverifikasi` dan status tagihan menjadi `lunas`.
  - Jika ditolak (*reject*), catatan alasan penolakan wajib diisi, status pembayaran menjadi `ditolak`, dan mahasiswa dapat melakukan upload ulang.

#### US-03: Admin Menerbitkan Tagihan Massal
> *Sebagai Admin, saya ingin menerbitkan tagihan massal untuk satu angkatan agar tidak perlu input satu per satu.*
- **Acceptance Criteria:**
  - Tagihan terbit untuk seluruh mahasiswa aktif pada prodi/angkatan yang dipilih.
  - Sistem secara otomatis mencegah penerbitan tagihan duplikat untuk jenis biaya dan semester yang sama.

### 3.3 Aturan Bisnis (Business Rules)
1. **BR-01 (Integritas Nominal):** Nominal tagihan dan pembayaran harus selalu $> 0$. Pembayaran parsial tidak diizinkan kecuali melalui skema cicilan resmi.
2. **BR-02 (Idempotensi Verifikasi):** Transaksi yang sudah diverifikasi tidak dapat diubah statusnya kembali secara sepihak tanpa mekanisme *reversal* resmi.
3. **BR-03 (Wajib Catatan Penolakan):** Setiap penolakan pembayaran wajib menyertakan alasan deskriptif minimal 5 karakter.
4. **BR-04 (Keamanan File Upload):** Ekstensi executable (.exe, .bat, .php, .sh) dilarang keras untuk mencegah serangan Remote Code Execution (RCE).

---

## 4. LEVEL DAN TIPE TESTING (TEST LEVELS & TYPES)

| Level Pengujian | Tipe Pengujian | Ruang Lingkup & Fokus | Tool yang Digunakan |
|-----------------|----------------|----------------------|---------------------|
| **Unit Testing** | White Box Testing | Method & function pada 4 Service utama | Jest / PHP Test Runner |
| **Integration Testing** | API Testing | Validasi interaksi HTTP, parameter, status code (200, 201, 400, 401, 403, 404, 409, 422, 500) | Postman, Newman, cURL |
| **System Testing** | Black Box Testing | Equivalence Partitioning, Boundary Value Analysis, Decision Table | Manual & Automated Scripts |
| **End-to-End (E2E)**| Functional Automation | Alur skenario pengguna menyeluruh dari login hingga cetak kuitansi | Playwright |
| **Acceptance Testing** | UAT | Pemenuhan kebutuhan 4 role stakeholder | UAT Rubric & Scenarios |
| **Regression Testing** | Automated Regression | Verifikasi bug fix tidak memicu kerusakan sampingan | Automated Suite |

---

## 5. RISK-BASED TESTING (RBT)

| ID Risiko | Area Fungsional | Deskripsi Risiko | Dampak (1-5) | Probabilitas (1-5) | Skor Risiko | Strategi Mitigasi Pengujian |
|-----------|-----------------|------------------|--------------|--------------------|-------------|-----------------------------|
| **R-01** | Pembayaran | Mahasiswa mengunggah file berbahaya / shell script | 5 | 3 | **15 (Tinggi)** | Uji BVA MIME type, file extension whitelist, dan sanitasi filename |
| **R-02** | Keuangan | Pembayaran ganda untuk satu tagihan (Race condition) | 5 | 2 | **10 (Tinggi)** | Uji idempotensi pada `PaymentService` & database transaction lock |
| **R-03** | Otorisasi | Mahasiswa mengakses dashboard/API Bendahara (IDOR/BOLA) | 4 | 2 | **8 (Sedang)** | Uji RBAC middleware, token parsing, and cross-student access |
| **R-04** | Tagihan | Duplikasi tagihan semester pada batch generator | 4 | 2 | **8 (Sedang)** | Uji constraint unik database dan pengecekan service level |
| **R-05** | Integritas | Nominal bayar manipulatif (negatif atau 0) | 5 | 1 | **5 (Sedang)** | Uji Boundary Value Analysis pada input nominal |

---

## 6. ENTRY & EXIT CRITERIA

### Entry Criteria
- Source code dan database schema telah terpasang di environment testing.
- Test data (seeder) telah tersedia untuk keempat role.
- Dokumen Test Plan dan Test Cases telah disetujui.

### Exit Criteria
- 100% Critical dan High test cases berstatus **PASSED**.
- UAT Score $\ge 80\%$.
- Seluruh bug berkategori CRITICAL dan HIGH telah diperbaiki dan lolos Regression Testing.
- Test Execution Report telah terdokumentasi lengkap.

