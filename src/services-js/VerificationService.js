// src/services-js/VerificationService.js

class VerificationService {
  constructor(db) {
    this.db = db;
  }

  async processDecision(paymentId, verifierUserId, action, catatan = null) {
    action = (action || '').toLowerCase().trim();

    if (!['approve', 'reject'].includes(action)) {
      return {
        success: false,
        statusCode: 400,
        errorCode: 'INVALID_ACTION',
        message: 'Aksi verifikasi harus approve atau reject'
      };
    }

    const payment = await this.db.findPembayaran(paymentId);
    if (!payment) {
      return {
        success: false,
        statusCode: 404,
        errorCode: 'PAYMENT_NOT_FOUND',
        message: 'Data pembayaran tidak ditemukan'
      };
    }

    if (payment.status !== 'menunggu_verifikasi') {
      return {
        success: false,
        statusCode: 409,
        errorCode: 'PAYMENT_ALREADY_PROCESSED',
        message: `Pembayaran ini sudah pernah diproses sebelumnya (Status: ${payment.status})`
      };
    }

    const trimmedCatatan = (catatan || '').trim();
    if (action === 'reject' && !trimmedCatatan) {
      return {
        success: false,
        statusCode: 422,
        errorCode: 'CATATAN_REQUIRED_ON_REJECT',
        message: 'Catatan alasan penolakan wajib diisi untuk memberi tahu mahasiswa'
      };
    }

    if (action === 'approve') {
      await this.db.updatePembayaran(paymentId, {
        status: 'diverifikasi',
        diverifikasi_oleh: verifierUserId,
        catatan: trimmedCatatan || 'Pembayaran diverifikasi sesuai bukti transfer.'
      });
      await this.db.updateTagihanStatus(payment.tagihan_id, 'lunas');

      return {
        success: true,
        statusCode: 200,
        actionTaken: 'approve',
        message: 'Pembayaran berhasil diverifikasi (Disetujui)',
        paymentId
      };
    } else {
      await this.db.updatePembayaran(paymentId, {
        status: 'ditolak',
        diverifikasi_oleh: verifierUserId,
        catatan: trimmedCatatan
      });
      await this.db.updateTagihanStatus(payment.tagihan_id, 'ditolak');

      return {
        success: true,
        statusCode: 200,
        actionTaken: 'reject',
        message: 'Pembayaran berhasil ditolak dengan catatan',
        paymentId
      };
    }
  }
}

module.exports = VerificationService;

