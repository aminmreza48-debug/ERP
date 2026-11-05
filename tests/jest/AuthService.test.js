// tests/jest/AuthService.test.js - White Box Testing for AuthService

const AuthService = require('../../src/services-js/AuthService');

describe('AuthService - White Box & Unit Testing', () => {
  let authService;
  let mockDb;

  beforeEach(() => {
    mockDb = {
      findUserByIdentifier: jest.fn(),
      verifyPassword: jest.fn(),
    };
    authService = new AuthService(mockDb);
  });

  describe('authenticate()', () => {
    test('TC-WB-AUTH-01: should fail with 400 when username or password is empty', async () => {
      const res1 = await authService.authenticate('', 'secret');
      expect(res1.success).toBe(false);
      expect(res1.statusCode).toBe(400);

      const res2 = await authService.authenticate('admin', '');
      expect(res2.success).toBe(false);
      expect(res2.statusCode).toBe(400);
    });

    test('TC-WB-AUTH-02: should fail with 401 when user is not found in database', async () => {
      mockDb.findUserByIdentifier.mockResolvedValue(null);

      const res = await authService.authenticate('unknown_user', 'password');
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(401);
      expect(res.message).toBe('Kredensial login tidak valid');
    });

    test('TC-WB-AUTH-03: should fail with 403 when user account is inactive', async () => {
      mockDb.findUserByIdentifier.mockResolvedValue({
        id: 1,
        username: 'inactive_user',
        status: 'inactive',
        password: 'hashed_password'
      });

      const res = await authService.authenticate('inactive_user', 'password');
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(403);
      expect(res.message).toContain('Akun dinonaktifkan');
    });

    test('TC-WB-AUTH-04: should fail with 401 when password mismatch occurs', async () => {
      mockDb.findUserByIdentifier.mockResolvedValue({
        id: 1,
        username: 'admin',
        status: 'active',
        password: 'hashed_correct'
      });
      mockDb.verifyPassword.mockResolvedValue(false);

      const res = await authService.authenticate('admin', 'wrong_password');
      expect(res.success).toBe(false);
      expect(res.statusCode).toBe(401);
      expect(res.message).toBe('Password yang dimasukkan salah');
    });

    test('TC-WB-AUTH-05: should succeed with 200 and return token on valid credentials', async () => {
      mockDb.findUserByIdentifier.mockResolvedValue({
        id: 1,
        username: 'admin',
        role: 'admin',
        status: 'active',
        password: 'hashed_correct'
      });
      mockDb.verifyPassword.mockResolvedValue(true);

      const res = await authService.authenticate('admin', 'admin123');
      expect(res.success).toBe(true);
      expect(res.statusCode).toBe(200);
      expect(res.token).toBeDefined();
      expect(res.user.password).toBeUndefined();
    });
  });

  describe('hasPermission()', () => {
    test('TC-WB-AUTH-06: should always grant access to admin role', () => {
      expect(authService.hasPermission('admin', ['bendahara'])).toBe(true);
    });

    test('TC-WB-AUTH-07: should grant access if role is explicitly in allowedRoles', () => {
      expect(authService.hasPermission('bendahara', ['bendahara', 'pimpinan'])).toBe(true);
    });

    test('TC-WB-AUTH-08: should deny access if role is not allowed', () => {
      expect(authService.hasPermission('mahasiswa', ['bendahara'])).toBe(false);
    });
  });

  describe('validateToken()', () => {
    test('TC-WB-AUTH-09: should return null for empty or invalid token format', () => {
      expect(authService.validateToken('')).toBeNull();
      expect(authService.validateToken('invalid-token-string')).toBeNull();
    });

    test('TC-WB-AUTH-10: should return payload for valid Bearer token', () => {
      const payload = { id: 1, username: 'test', exp: Date.now() + 60000 };
      const token = 'Bearer ' + Buffer.from(JSON.stringify(payload)).toString('base64');
      const decoded = authService.validateToken(token);
      expect(decoded).not.toBeNull();
      expect(decoded.id).toBe(1);
    });

    test('TC-WB-AUTH-11: should return null for expired token', () => {
      const payload = { id: 1, username: 'test', exp: Date.now() - 10000 };
      const token = Buffer.from(JSON.stringify(payload)).toString('base64');
      expect(authService.validateToken(token)).toBeNull();
    });
  });
});

