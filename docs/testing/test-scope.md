# TEST SCOPE SPECIFICATION
## SIPERMA – Sistem Informasi ERP Pembayaran dan Keuangan Mahasiswa

| Properti | Detail |
|----------|--------|
| **Modul Utama** | SIPERMA ERP Core Engine |
| **Versi Target** | v1.0-RC |
| **Tanggal Analisis** | 5 November 2026 |

---

## 1. MATRIKS CAKUPAN PENGUJIAN (TEST COVERAGE MATRIX)

### 1.1 In-Scope Functional Modules

```
                    ┌───────────────────────────┐
                    │     SIPERMA ERP SCOPE     │
                    └─────────────┬─────────────┘
          ┌───────────────────────┼───────────────────────┐
          │                       │                       │
┌─────────▼─────────┐   ┌─────────▼─────────┐   ┌─────────▼─────────┐
│     MAHASISWA     │   │     BENDAHARA     │   │       ADMIN       │
├───────────────────┤   ├───────────────────┤   ├───────────────────┤
│ • Login/Logout    │   │ • Login/Logout    │   │ • CRUD Mahasiswa  │
│ • Dashboard Mhs   │   │ • Antrean Bayar   │   │ • CRUD Jenis Biaya│
│ • Lihat Tagihan   │   │ • Preview Bukti   │   │ • Single Tagihan  │
│ • Upload Transfer │   │ • Approve Payment │   │ • Batch Tagihan   │
│ • Riwayat Bayar   │   │ • Reject + Catatan│   │ • Edit Tagihan    │
│ • Cetak Kwitansi  │   │ • Rekap Laporan   │   │ • User & Role RBAC│
└───────────────────┘   └───────────────────┘   └───────────────────┘
                                  │
                        ┌─────────▼─────────┐
                        │     PIMPINAN      │
                        ├───────────────────┤
                        │ • Dashboard KPI   │
                        │ • Laporan Realis. │
                        │ • Laporan Tunggak │
                        │ • Chart Statistik │
                        └───────────────────┘
```

### 1.2 Detail Batasan Modul & Acceptance Threshold

| Modul | Batasan Fungsional | Kriteria Keberhasilan (*Pass Criteria*) |
|-------|--------------------|----------------------------------------|
| **Autentikasi & Sesi** | Validasi input, hashing bcrypt, RBAC per role | Token valid, session aman, unauthorized redirect |
| **Mahasiswa: Tagihan** | Menampilkan tagihan aktif dan riwayat | Data sinkron dengan database, status akurat |
| **Mahasiswa: Upload** | Upload file bukti bayar maks 2MB (JPG/PNG/PDF) | Status menjadi `menunggu_verifikasi`, file tersimpan |
| **Mahasiswa: Kwitansi**| Cetak dokumen bukti sah jika sudah lunas | Data kwitansi lengkap dengan NIP verifikator & stempel |
| **Bendahara: Verifikasi**| Validasi pembayaran: Approve atau Reject + Note | Status transaksi & tagihan sinkron, notifikasi terkirim |
| **Bendahara: Laporan** | Rekap kas masuk dengan total matematis benar | Total penerimaan = jumlah nominal diverifikasi |
| **Admin: Master Data** | CRUD Mahasiswa dan Jenis Biaya | Tidak ada duplikasi NIM atau kode biaya duplikat |
| **Admin: Generator** | Menerbitkan tagihan individu atau batch prodi | Tagihan massal tercipta tanpa error duplikasi |
| **Admin: User RBAC** | Pengelolaan kredensial dan hak akses | Hak akses role berubah sesuai konfigurasi admin |
| **Pimpinan: Eksekutif**| Visualisasi grafik Chart.js & rasio realisasi | Rasio realisasi kas dihitung presisi secara otomatis |

---

## 2. OUT-OF-SCOPE (FITUR DILUAR CAKUPAN)
- Fitur pembayaran tunai langsung di kasir loket tanpa bukti setor bank.
- Integrasi ke sistem perbankan Core Banking / Host-to-Host (H2H) secara riil (diuji melalui simulasi).
- Fitur akademik KRS / KHS (berada di modul SIAKAD terpisah).

---

## 3. NON-FUNCTIONAL TESTING SCOPE
1. **Security Testing:**
   - Validasi MIME-type dan proteksi upload file berbahaya (.php, .exe).
   - Pencegahan SQL Injection melalui *Prepared Statements PDO*.
   - Proteksi XSS melalui *HTML entity escaping*.
   - Validasi otorisasi rute (*Broken Access Control*).
2. **Performance Testing:**
   - Uji konkurensi 10, 50, dan 100 concurrent requests pada API login, dashboard, dan tagihan.
   - Ambang batas target: response time p95 $< 2000$ ms, error rate $< 1\%$.
3. **Usability & Responsiveness:**
   - Desain responsif mobile-friendly menggunakan Bootstrap 5.

