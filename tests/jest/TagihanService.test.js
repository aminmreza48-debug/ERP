// tests/jest/TagihanService.test.js - White Box Testing for TagihanService

const TagihanService = require('../../src/services-js/TagihanService');

describe('TagihanService - White Box & Unit Testing', () => {
  let tagihanService;
  let mockDb;

  beforeEach(() => {
    mockDb = {
      findMahasiswa: jest.fn(),
      findJenisBiaya: jest.fn(),
      findDuplicateTagihan: jest.fn(),
      insertTagihan: jest.fn(),
      findMahasiswaByFilter: jest.fn(),
    };
    tagihanService = new TagihanService(mockDb);
  });

  describe('createTagihan()', () => {
    test('TC-WB-TAG-01: should fail with 422 if required fields are missing', async () => {
      const res = await tagihanService.createTagihan({ mahasiswa_id: 1 });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(422);
      expect(res.message).toBe('Semua kolom wajib diisi');
    });

    test('TC-WB-TAG-02: should fail with 422 if nominal <= 0 (Boundary Value)', async () => {
      const res1 = await tagihanService.createTagihan({
        mahasiswa_id: 1,
        jenis_biaya_id: 1,
        semester: '2024/1',
        nominal: 0
      });
      expect(res1.success).toBe(false);
      expect(res1.statusCode).toBe(422);

      const res2 = await tagihanService.createTagihan({
        mahasiswa_id: 1,
        jenis_biaya_id: 1,
        semester: '2024/1',
        nominal: -500000
      });
      expect(res2.success).toBe(false);
      expect(res2.statusCode).toBe(422);
    });

    test('TC-WB-TAG-03: should fail with 404 if mahasiswa is not found', async () => {
      mockDb.findMahasiswa.mockResolvedValue(null);

      const res = await tagihanService.createTagihan({
        mahasiswa_id: 999,
        jenis_biaya_id: 1,
        semester: '2024/1',
        nominal: 2500000
      });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(404);
    });

    test('TC-WB-TAG-04: should fail with 422 if mahasiswa is not active', async () => {
      mockDb.findMahasiswa.mockResolvedValue({ id: 1, status: 'cuti' });

      const res = await tagihanService.createTagihan({
        mahasiswa_id: 1,
        jenis_biaya_id: 1,
        semester: '2024/1',
        nominal: 2500000
      });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(422);
      expect(res.message).toContain('non-aktif');
    });

    test('TC-WB-TAG-05: should fail with 422 if jenis biaya is inactive or not found', async () => {
      mockDb.findMahasiswa.mockResolvedValue({ id: 1, status: 'aktif' });
      mockDb.findJenisBiaya.mockResolvedValue(null);

      const res = await tagihanService.createTagihan({
        mahasiswa_id: 1,
        jenis_biaya_id: 99,
        semester: '2024/1',
        nominal: 2500000
      });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(422);
    });

    test('TC-WB-TAG-06: should fail with 409 on duplicate active invoice', async () => {
      mockDb.findMahasiswa.mockResolvedValue({ id: 1, status: 'aktif' });
      mockDb.findJenisBiaya.mockResolvedValue({ id: 1, status: 'aktif' });
      mockDb.findDuplicateTagihan.mockResolvedValue({ id: 10 });

      const res = await tagihanService.createTagihan({
        mahasiswa_id: 1,
        jenis_biaya_id: 1,
        semester: '2024/1',
        nominal: 2500000
      });
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(409);
      expect(res.message).toContain('sudah ada');
    });

    test('TC-WB-TAG-07: should create invoice successfully with valid inputs', async () => {
      mockDb.findMahasiswa.mockResolvedValue({ id: 1, status: 'aktif' });
      mockDb.findJenisBiaya.mockResolvedValue({ id: 1, status: 'aktif' });
      mockDb.findDuplicateTagihan.mockResolvedValue(null);
      mockDb.insertTagihan.mockResolvedValue(42);

      const res = await tagihanService.createTagihan({
        mahasiswa_id: 1,
        jenis_biaya_id: 1,
        semester: '2024/1',
        nominal: 2500000
      });
      expect(res.success).toBe(true);
      expect(res.statusCode).toBe(201);
      expect(res.tagihan_id).toBe(42);
      expect(res.nomor_tagihan).toMatch(/^TGH-/);
    });
  });

  describe('generateBatch()', () => {
    test('TC-WB-TAG-08: should generate batch invoices for filtered students', async () => {
      mockDb.findJenisBiaya.mockResolvedValue({ id: 1, status: 'aktif', nominal_default: 2000000 });
      mockDb.findMahasiswaByFilter.mockResolvedValue([
        { id: 1, status: 'aktif' },
        { id: 2, status: 'aktif' }
      ]);
      mockDb.findMahasiswa.mockResolvedValue({ id: 1, status: 'aktif' });
      mockDb.findDuplicateTagihan.mockResolvedValue(null);
      mockDb.insertTagihan.mockResolvedValue(100);

      const res = await tagihanService.generateBatch(1, '2024/1', 'Teknik Informatika', '2024');
      expect(res.success).toBe(true);
      expect(res.created_count).toBe(2);
      expect(res.skipped_count).toBe(0);
    });
  });
});

