-- SIPERMA ERP Seeder Data

-- Users (passwords: admin123, bendahara123, mhs123, pimpinan123)
INSERT INTO users (id, username, password, name, email, role, status) VALUES
(1, 'admin', '$2y$10$/N913BrMF4ErBJ9Ugbe2MO0reB62CuVd71tXx4RHF.4Pet5i5fK6u', 'Administrator SIPERMA', 'admin@siperma.ac.id', 'admin', 'active'),
(2, 'bendahara', '$2y$10$pvDpnApoHxvf9Lrdu6GOm.3QuPqTeAgJwXn5AlmKw/7rMLRy.6ezC', 'Hj. Siti Aminah, S.E., M.M.', 'bendahara@siperma.ac.id', 'bendahara', 'active'),
(3, 'mhs2024001', '$2y$10$IbeREBayw1gs68Fjqi4ACeZYDbhfSdV3fSsX7/POtJEW1Rq0kLZ16', 'Ahmad Fauzi', 'ahmad.fauzi@student.ac.id', 'mahasiswa', 'active'),
(4, 'mhs2024002', '$2y$10$IbeREBayw1gs68Fjqi4ACeZYDbhfSdV3fSsX7/POtJEW1Rq0kLZ16', 'Siti Nurhaliza', 'siti.nurhaliza@student.ac.id', 'mahasiswa', 'active'),
(5, 'mhs2024003', '$2y$10$IbeREBayw1gs68Fjqi4ACeZYDbhfSdV3fSsX7/POtJEW1Rq0kLZ16', 'Budi Santoso', 'budi.santoso@student.ac.id', 'mahasiswa', 'active'),
(6, 'pimpinan', '$2y$10$W9N24bWE6qSxH/Ym21oLd.k8tsh3UjWGjC5smgAkr0ss6OQTrzZwm', 'Prof. Dr. Ir. Hendra Gunawan, M.Eng.', 'pimpinan@siperma.ac.id', 'pimpinan', 'active');

-- Mahasiswa
INSERT INTO mahasiswa (id, user_id, nim, nama, prodi, angkatan, email, telepon, status) VALUES
(1, 3, '202401001', 'Ahmad Fauzi', 'Teknik Informatika', '2024', 'ahmad.fauzi@student.ac.id', '081234567890', 'aktif'),
(2, 4, '202401002', 'Siti Nurhaliza', 'Sistem Informasi', '2024', 'siti.nurhaliza@student.ac.id', '081234567891', 'aktif'),
(3, 5, '202401003', 'Budi Santoso', 'Teknik Informatika', '2024', 'budi.santoso@student.ac.id', '081234567892', 'aktif'),
(4, NULL, '202301001', 'Reza Rahadian', 'Teknik Elektro', '2023', 'reza.rahadian@student.ac.id', '081234567893', 'aktif'),
(5, NULL, '202301002', 'Dewi Lestari', 'Akuntansi', '2023', 'dewi.lestari@student.ac.id', '081234567894', 'aktif');

-- Jenis Biaya
INSERT INTO jenis_biaya (id, kode, nama, nominal_default, keterangan, status) VALUES
(1, 'UKT-01', 'Uang Kuliah Tunggal (UKT) - Golongan 1', 2500000.00, 'UKT Golongan 1 untuk mahasiswa reguler', 'aktif'),
(2, 'UKT-02', 'Uang Kuliah Tunggal (UKT) - Golongan 2', 4500000.00, 'UKT Golongan 2 untuk mahasiswa reguler', 'aktif'),
(3, 'UKT-03', 'Uang Kuliah Tunggal (UKT) - Golongan 3', 6000000.00, 'UKT Golongan 3 untuk mahasiswa reguler', 'aktif'),
(4, 'PRAK-01', 'Biaya Praktikum Laboratorium', 750000.00, 'Biaya penggunaan laboratorium per semester', 'aktif'),
(5, 'WIS-01', 'Biaya Wisuda dan Ijazah', 1500000.00, 'Biaya pendaftaran wisuda dan pengesahan ijazah', 'aktif');

-- Tagihan
INSERT INTO tagihan (id, nomor_tagihan, mahasiswa_id, jenis_biaya_id, semester, nominal, tanggal_terbit, tanggal_jatuh_tempo, status) VALUES
(1, 'TGH-2024-001', 1, 2, '2024/2025 Ganjil', 4500000.00, '2024-08-01', '2024-09-30', 'lunas'),
(2, 'TGH-2024-002', 1, 4, '2024/2025 Ganjil', 750000.00, '2024-08-01', '2024-11-30', 'belum_bayar'),
(3, 'TGH-2024-003', 2, 1, '2024/2025 Ganjil', 2500000.00, '2024-08-01', '2024-11-30', 'menunggu_verifikasi'),
(4, 'TGH-2024-004', 3, 2, '2024/2025 Ganjil', 4500000.00, '2024-08-01', '2024-10-31', 'belum_bayar'),
(5, 'TGH-2024-005', 4, 3, '2024/2025 Ganjil', 6000000.00, '2024-08-01', '2024-09-30', 'ditolak');

-- Pembayaran
INSERT INTO pembayaran (id, nomor_transaksi, tagihan_id, mahasiswa_id, nominal_bayar, metode_pembayaran, bank_tujuan, bukti_transfer, tanggal_bayar, status, diverifikasi_oleh, tanggal_verifikasi, catatan) VALUES
(1, 'PAY-2024-001', 1, 1, 4500000.00, 'Transfer Bank', 'Bank Mandiri', 'sample_bukti_1.jpg', '2024-08-15 10:30:00', 'diverifikasi', 2, '2024-08-16 09:00:00', 'Pembayaran terverifikasi dan sesuai rekening koran.'),
(2, 'PAY-2024-002', 3, 2, 2500000.00, 'Transfer Bank', 'Bank BNI', 'sample_bukti_2.jpg', '2024-11-01 14:15:00', 'menunggu_verifikasi', NULL, NULL, NULL),
(3, 'PAY-2024-003', 5, 4, 6000000.00, 'Transfer Bank', 'Bank BRI', 'sample_bukti_3.jpg', '2024-09-20 16:45:00', 'ditolak', 2, '2024-09-21 11:20:00', 'Bukti transfer buram dan tidak terbaca nomor referensinya. Mohon upload ulang.');

-- Notifikasi
INSERT INTO notifikasi (user_id, judul, pesan, is_read) VALUES
(3, 'Pembayaran Diverifikasi', 'Pembayaran tagihan UKT-02 senilai Rp 4.500.000 telah diverifikasi oleh Bendahara.', 1),
(4, 'Pembayaran Menunggu Verifikasi', 'Bukti pembayaran tagihan UKT-01 senilai Rp 2.500.000 telah diterima dan sedang diverifikasi.', 0),
(2, 'Pembayaran Baru Masuk', 'Mahasiswa Siti Nurhaliza (202401002) mengupload bukti pembayaran untuk tagihan TGH-2024-003.', 0);

-- Audit Logs
INSERT INTO audit_logs (user_id, action, module, detail, ip_address) VALUES
(1, 'CREATE_USER', 'User Management', 'Admin membuat user bendahara dan mahasiswa demo', '127.0.0.1'),
(1, 'GENERATE_TAGIHAN', 'Tagihan', 'Admin menerbitkan tagihan semester 2024/2025 Ganjil', '127.0.0.1'),
(2, 'VERIFIKASI_PEMBAYARAN', 'Pembayaran', 'Bendahara memverifikasi transaksi PAY-2024-001', '127.0.0.1');

