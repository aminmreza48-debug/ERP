// tests/jest/PaymentService.test.js - White Box Testing for PaymentService

const PaymentService = require('../../src/services-js/PaymentService');

describe('PaymentService - White Box & Unit Testing', () => {
  let paymentService;
  let mockDb;

  beforeEach(() => {
    mockDb = {
      findTagihan: jest.fn(),
      insertPembayaran: jest.fn(),
      updateTagihanStatus: jest.fn(),
    };
    paymentService = new PaymentService(mockDb);
  });

  describe('validateProofFile() - EP & BVA', () => {
    test('TC-WB-PAY-01: should fail if file is null or empty', () => {
      const res = paymentService.validateProofFile(null);
      expect(res.valid).toBe(false);
      expect(res.errorCode).toBe('FILE_EMPTY');
    });

    test('TC-WB-PAY-02: should fail if file size exceeds 2MB boundary (2097153 bytes)', () => {
      const res = paymentService.validateProofFile({
        name: 'transfer.jpg',
        size: 2 * 1024 * 1024 + 1
      });
      expect(res.valid).toBe(false);
      expect(res.errorCode).toBe('FILE_TOO_LARGE');
    });

    test('TC-WB-PAY-03: should fail for disallowed extensions (e.g., .exe, .php, .docx)', () => {
      const res = paymentService.validateProofFile({
        name: 'malware.exe',
        size: 50000
      });
      expect(res.valid).toBe(false);
      expect(res.errorCode).toBe('FILE_INVALID_TYPE');
    });

    test('TC-WB-PAY-04: should accept valid file types (.jpg, .png, .pdf) under 2MB', () => {
      const res = paymentService.validateProofFile({
        name: 'bukti_transfer.PNG',
        size: 1024 * 500
      });
      expect(res.valid).toBe(true);
      expect(res.fileName).toMatch(/^bukti_\d+_[a-z0-9]+\.png$/);
    });
  });

  describe('submitPayment()', () => {
    test('TC-WB-PAY-05: should fail with 422 if nominal <= 0', async () => {
      const res = await paymentService.submitPayment(1, 1, 0, 'Transfer', 'Mandiri', { name: 'test.jpg' });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(422);
      expect(res.errorCode).toBe('INVALID_NOMINAL_ZERO_OR_NEGATIVE');
    });

    test('TC-WB-PAY-06: should fail with 404 if tagihan not found', async () => {
      mockDb.findTagihan.mockResolvedValue(null);

      const res = await paymentService.submitPayment(999, 1, 1000000, 'Transfer', 'Mandiri', { name: 'test.jpg' });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(404);
      expect(res.errorCode).toBe('TAGIHAN_NOT_FOUND');
    });

    test('TC-WB-PAY-07: should fail with 403 if tagihan does not belong to student', async () => {
      mockDb.findTagihan.mockResolvedValue({ id: 1, mahasiswa_id: 2, nominal: 1000000, status: 'belum_bayar' });

      const res = await paymentService.submitPayment(1, 1, 1000000, 'Transfer', 'Mandiri', { name: 'test.jpg' });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(403);
      expect(res.errorCode).toBe('UNAUTHORIZED_STUDENT');
    });

    test('TC-WB-PAY-08: should fail with 400 if tagihan is already paid (lunas)', async () => {
      mockDb.findTagihan.mockResolvedValue({ id: 1, mahasiswa_id: 1, nominal: 1000000, status: 'lunas' });

      const res = await paymentService.submitPayment(1, 1, 1000000, 'Transfer', 'Mandiri', { name: 'test.jpg' });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(400);
      expect(res.errorCode).toBe('TAGIHAN_ALREADY_PAID');
    });

    test('TC-WB-PAY-09: should fail with 409 if payment is already pending verification', async () => {
      mockDb.findTagihan.mockResolvedValue({ id: 1, mahasiswa_id: 1, nominal: 1000000, status: 'menunggu_verifikasi' });

      const res = await paymentService.submitPayment(1, 1, 1000000, 'Transfer', 'Mandiri', { name: 'test.jpg' });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(409);
      expect(res.errorCode).toBe('PAYMENT_PENDING_VERIFICATION');
    });

    test('TC-WB-PAY-10: should fail with 422 if nominal does not match tagihan amount', async () => {
      mockDb.findTagihan.mockResolvedValue({ id: 1, mahasiswa_id: 1, nominal: 2500000, status: 'belum_bayar' });

      const res = await paymentService.submitPayment(1, 1, 2000000, 'Transfer', 'Mandiri', { name: 'test.jpg', size: 1000 });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(422);
      expect(res.errorCode).toBe('NOMINAL_MISMATCH');
    });

    test('TC-WB-PAY-11: should succeed with 201 when payment data is valid', async () => {
      mockDb.findTagihan.mockResolvedValue({ id: 1, mahasiswa_id: 1, nominal: 2500000, status: 'belum_bayar' });
      mockDb.insertPembayaran.mockResolvedValue(55);
      mockDb.updateTagihanStatus.mockResolvedValue(true);

      const res = await paymentService.submitPayment(1, 1, 2500000, 'Transfer', 'Mandiri', { name: 'bukti.jpg', size: 50000 });
      expect(res.success).toBe(true);
      expect(res.statusCode).toBe(201);
      expect(res.paymentId).toBe(55);
      expect(res.nomorTransaksi).toMatch(/^PAY-/);
    });
  });
});

