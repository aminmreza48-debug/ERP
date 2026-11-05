// tests/jest/VerificationService.test.js - White Box Testing for VerificationService

const VerificationService = require('../../src/services-js/VerificationService');

describe('VerificationService - White Box & Decision Table Testing', () => {
  let verificationService;
  let mockDb;

  beforeEach(() => {
    mockDb = {
      findPembayaran: jest.fn(),
      updatePembayaran: jest.fn(),
      updateTagihanStatus: jest.fn(),
    };
    verificationService = new VerificationService(mockDb);
  });

  describe('processDecision()', () => {
    test('TC-WB-VER-01: should fail with 400 if action is not approve or reject', async () => {
      const res = await verificationService.processDecision(1, 2, 'cancel');
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(400);
      expect(res.errorCode).toBe('INVALID_ACTION');
    });

    test('TC-WB-VER-02: should fail with 404 if payment record not found', async () => {
      mockDb.findPembayaran.mockResolvedValue(null);

      const res = await verificationService.processDecision(999, 2, 'approve');
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(404);
      expect(res.errorCode).toBe('PAYMENT_NOT_FOUND');
    });

    test('TC-WB-VER-03: should fail with 409 if payment status is already processed', async () => {
      mockDb.findPembayaran.mockResolvedValue({
        id: 1,
        status: 'diverifikasi',
        tagihan_id: 1
      });

      const res = await verificationService.processDecision(1, 2, 'approve');
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(409);
      expect(res.errorCode).toBe('PAYMENT_ALREADY_PROCESSED');
    });

    test('TC-WB-VER-04: should fail with 422 if reject action without notes (Decision Table Rule)', async () => {
      mockDb.findPembayaran.mockResolvedValue({
        id: 1,
        status: 'menunggu_verifikasi',
        tagihan_id: 1
      });

      const res = await verificationService.processDecision(1, 2, 'reject', '');
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(422);
      expect(res.errorCode).toBe('CATATAN_REQUIRED_ON_REJECT');
    });

    test('TC-WB-VER-05: should successfully approve pending payment and mark invoice as lunas', async () => {
      mockDb.findPembayaran.mockResolvedValue({
        id: 1,
        status: 'menunggu_verifikasi',
        tagihan_id: 10
      });
      mockDb.updatePembayaran.mockResolvedValue(true);
      mockDb.updateTagihanStatus.mockResolvedValue(true);

      const res = await verificationService.processDecision(1, 2, 'approve', 'Valid payment');
      expect(res.success).toBe(true);
      expect(res.statusCode).toBe(200);
      expect(res.actionTaken).toBe('approve');
      expect(mockDb.updateTagihanStatus).toHaveBeenCalledWith(10, 'lunas');
    });

    test('TC-WB-VER-06: should successfully reject pending payment with reason and mark invoice as ditolak', async () => {
      mockDb.findPembayaran.mockResolvedValue({
        id: 1,
        status: 'menunggu_verifikasi',
        tagihan_id: 10
      });
      mockDb.updatePembayaran.mockResolvedValue(true);
      mockDb.updateTagihanStatus.mockResolvedValue(true);

      const res = await verificationService.processDecision(1, 2, 'reject', 'Bukti buram');
      expect(res.success).toBe(true);
      expect(res.statusCode).toBe(200);
      expect(res.actionTaken).toBe('reject');
      expect(mockDb.updateTagihanStatus).toHaveBeenCalledWith(10, 'ditolak');
    });
  });
});

