// src/services-js/PaymentService.js

class PaymentService {
  constructor(db) {
    this.db = db;
  }

  validateProofFile(file) {
    if (!file || !file.name) {
      return { valid: false, errorCode: 'FILE_EMPTY', message: 'File bukti transfer wajib diunggah' };
    }

    const maxBytes = 2 * 1024 * 1024; // 2MB
    if (file.size && file.size > maxBytes) {
      return { valid: false, errorCode: 'FILE_TOO_LARGE', message: 'Ukuran file melebihi batas maksimum 2MB' };
    }

    const ext = file.name.split('.').pop().toLowerCase();
    const allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!allowed.includes(ext)) {
      return { valid: false, errorCode: 'FILE_INVALID_TYPE', message: 'Format file tidak didukung. Gunakan format JPG, PNG, atau PDF' };
    }

    return {
      valid: true,
      fileName: `bukti_${Date.now()}_${Math.random().toString(36).substring(2, 6)}.${ext}`
    };
  }

  async submitPayment(tagihanId, mahasiswaId, nominal, metode, bank, fileData) {
    if (nominal <= 0) {
      return {
        success: false,
        statusCode: 422,
        errorCode: 'INVALID_NOMINAL_ZERO_OR_NEGATIVE',
        message: 'Nominal pembayaran harus lebih besar dari 0'
      };
    }

    const tagihan = await this.db.findTagihan(tagihanId);
    if (!tagihan) {
      return {
        success: false,
        statusCode: 404,
        errorCode: 'TAGIHAN_NOT_FOUND',
        message: 'Tagihan tidak ditemukan'
      };
    }

    if (Number(tagihan.mahasiswa_id) !== Number(mahasiswaId)) {
      return {
        success: false,
        statusCode: 403,
        errorCode: 'UNAUTHORIZED_STUDENT',
        message: 'Anda tidak memiliki hak untuk membayar tagihan ini'
      };
    }

    if (tagihan.status === 'lunas') {
      return {
        success: false,
        statusCode: 400,
        errorCode: 'TAGIHAN_ALREADY_PAID',
        message: 'Tagihan sudah lunas'
      };
    }

    if (tagihan.status === 'menunggu_verifikasi') {
      return {
        success: false,
        statusCode: 409,
        errorCode: 'PAYMENT_PENDING_VERIFICATION',
        message: 'Pembayaran sedang menunggu proses verifikasi oleh Bendahara'
      };
    }

    if (Number(nominal) !== Number(tagihan.nominal)) {
      return {
        success: false,
        statusCode: 422,
        errorCode: 'NOMINAL_MISMATCH',
        message: `Nominal pembayaran (${nominal}) harus sesuai dengan jumlah tagihan (${tagihan.nominal})`
      };
    }

    const fileCheck = this.validateProofFile(fileData);
    if (!fileCheck.valid) {
      return {
        success: false,
        statusCode: 422,
        errorCode: fileCheck.errorCode,
        message: fileCheck.message
      };
    }

    const nomorTransaksi = `PAY-${new Date().toISOString().slice(0, 10).replace(/-/g, '')}-${Math.random().toString(36).substring(2, 7).toUpperCase()}`;

    const paymentId = await this.db.insertPembayaran({
      nomor_transaksi: nomorTransaksi,
      tagihan_id: tagihanId,
      mahasiswa_id: mahasiswaId,
      nominal_bayar: nominal,
      metode_pembayaran: metode || 'Transfer Bank',
      bank_tujuan: bank || 'Bank Mandiri',
      bukti_transfer: fileCheck.fileName,
      tanggal_bayar: new Date().toISOString(),
      status: 'menunggu_verifikasi'
    });

    await this.db.updateTagihanStatus(tagihanId, 'menunggu_verifikasi');

    return {
      success: true,
      statusCode: 201,
      message: 'Bukti pembayaran berhasil diupload dan menunggu verifikasi',
      paymentId,
      nomorTransaksi
    };
  }
}

module.exports = PaymentService;

