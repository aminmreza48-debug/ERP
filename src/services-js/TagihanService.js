// src/services-js/TagihanService.js

class TagihanService {
  constructor(db) {
    this.db = db;
  }

  async createTagihan(data) {
    if (!data.mahasiswa_id || !data.jenis_biaya_id || !data.semester || data.nominal === undefined) {
      return { success: false, statusCode: 422, message: 'Semua kolom wajib diisi' };
    }

    const nominal = Number(data.nominal);
    if (nominal <= 0) {
      return { success: false, statusCode: 422, message: 'Nominal tagihan harus lebih besar dari 0' };
    }

    const mhs = await this.db.findMahasiswa(data.mahasiswa_id);
    if (!mhs) {
      return { success: false, statusCode: 404, message: 'Mahasiswa tidak ditemukan' };
    }
    if (mhs.status !== 'aktif') {
      return { success: false, statusCode: 422, message: 'Tidak dapat membuat tagihan untuk mahasiswa non-aktif' };
    }

    const biaya = await this.db.findJenisBiaya(data.jenis_biaya_id);
    if (!biaya || biaya.status !== 'aktif') {
      return { success: false, statusCode: 422, message: 'Jenis biaya tidak valid atau non-aktif' };
    }

    const duplicate = await this.db.findDuplicateTagihan(data.mahasiswa_id, data.jenis_biaya_id, data.semester);
    if (duplicate) {
      return { success: false, statusCode: 409, message: 'Tagihan untuk komponen dan semester ini sudah ada' };
    }

    const nomorTagihan = `TGH-${new Date().getFullYear()}-${Math.random().toString(36).substring(2, 8).toUpperCase()}`;
    const id = await this.db.insertTagihan({
      nomor_tagihan: nomorTagihan,
      mahasiswa_id: data.mahasiswa_id,
      jenis_biaya_id: data.jenis_biaya_id,
      semester: data.semester,
      nominal,
      tanggal_terbit: data.tanggal_terbit || new Date().toISOString().slice(0, 10),
      tanggal_jatuh_tempo: data.tanggal_jatuh_tempo || new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10),
      status: 'belum_bayar'
    });

    return {
      success: true,
      statusCode: 201,
      message: 'Tagihan berhasil dibuat',
      tagihan_id: id,
      nomor_tagihan: nomorTagihan
    };
  }

  async generateBatch(jenisBiayaId, semester, prodi = null, angkatan = null, customNominal = null) {
    const biaya = await this.db.findJenisBiaya(jenisBiayaId);
    if (!biaya) {
      return { success: false, statusCode: 404, message: 'Jenis biaya tidak ditemukan' };
    }

    const nominal = (customNominal && customNominal > 0) ? customNominal : Number(biaya.nominal_default);
    const mhsList = await this.db.findMahasiswaByFilter({ prodi, angkatan, status: 'aktif' });

    let created = 0;
    let skipped = 0;

    for (const m of mhsList) {
      const res = await this.createTagihan({
        mahasiswa_id: m.id,
        jenis_biaya_id: jenisBiayaId,
        semester,
        nominal
      });
      if (res.success) created++;
      else skipped++;
    }

    return {
      success: true,
      statusCode: 200,
      message: `Batch selesai: ${created} tagihan dibuat, ${skipped} dilewati (sudah ada/duplikat)`,
      created_count: created,
      skipped_count: skipped
    };
  }
}

module.exports = TagihanService;

