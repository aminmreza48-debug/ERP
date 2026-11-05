-- SIPERMA ERP Database Schema (SQLite & MySQL Compatible)
-- Enterprise Resource Planning - Sistem Informasi Pembayaran Mahasiswa

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role VARCHAR(20) NOT NULL CHECK(role IN ('admin', 'bendahara', 'mahasiswa', 'pimpinan')),
    status VARCHAR(20) DEFAULT 'active' CHECK(status IN ('active', 'inactive')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS mahasiswa (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NULL,
    nim VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    prodi VARCHAR(100) NOT NULL,
    angkatan VARCHAR(10) NOT NULL,
    email VARCHAR(100),
    telepon VARCHAR(20),
    status VARCHAR(20) DEFAULT 'aktif' CHECK(status IN ('aktif', 'cuti', 'lulus', 'drop_out')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS jenis_biaya (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    kode VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    nominal_default DECIMAL(12,2) NOT NULL,
    keterangan TEXT,
    status VARCHAR(20) DEFAULT 'aktif' CHECK(status IN ('aktif', 'nonaktif')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tagihan (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nomor_tagihan VARCHAR(50) UNIQUE NOT NULL,
    mahasiswa_id INTEGER NOT NULL,
    jenis_biaya_id INTEGER NOT NULL,
    semester VARCHAR(20) NOT NULL,
    nominal DECIMAL(12,2) NOT NULL,
    tanggal_terbit DATE NOT NULL,
    tanggal_jatuh_tempo DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'belum_bayar' CHECK(status IN ('belum_bayar', 'menunggu_verifikasi', 'lunas', 'ditolak', 'dibatalkan')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
    FOREIGN KEY (jenis_biaya_id) REFERENCES jenis_biaya(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS pembayaran (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nomor_transaksi VARCHAR(50) UNIQUE NOT NULL,
    tagihan_id INTEGER NOT NULL,
    mahasiswa_id INTEGER NOT NULL,
    nominal_bayar DECIMAL(12,2) NOT NULL,
    metode_pembayaran VARCHAR(50) NOT NULL,
    bank_tujuan VARCHAR(50),
    bukti_transfer VARCHAR(255) NOT NULL,
    tanggal_bayar DATETIME NOT NULL,
    status VARCHAR(20) DEFAULT 'menunggu_verifikasi' CHECK(status IN ('menunggu_verifikasi', 'diverifikasi', 'ditolak')),
    diverifikasi_oleh INTEGER NULL,
    tanggal_verifikasi DATETIME NULL,
    catatan TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tagihan_id) REFERENCES tagihan(id) ON DELETE CASCADE,
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
    FOREIGN KEY (diverifikasi_oleh) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS notifikasi (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    judul VARCHAR(150) NOT NULL,
    pesan TEXT NOT NULL,
    is_read INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50) NOT NULL,
    detail TEXT,
    ip_address VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

