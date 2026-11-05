// src/services-js/AuthService.js - Pure Business Logic for Testing & Node.js

class AuthService {
  constructor(db) {
    this.db = db;
  }

  async authenticate(identifier, password) {
    identifier = (identifier || '').trim();
    password = (password || '').trim();

    // Branch 1: Empty inputs
    if (!identifier || !password) {
      return {
        success: false,
        statusCode: 400,
        message: 'Username dan password wajib diisi'
      };
    }

    // Branch 2: Find user
    const user = await this.db.findUserByIdentifier(identifier);

    // Branch 3: User not found
    if (!user) {
      return {
        success: false,
        statusCode: 401,
        message: 'Kredensial login tidak valid'
      };
    }

    // Branch 4: Account inactive
    if (user.status !== 'active') {
      return {
        success: false,
        statusCode: 403,
        message: 'Akun dinonaktifkan. Hubungi administrator'
      };
    }

    // Branch 5: Password mismatch
    const isMatch = await this.db.verifyPassword(password, user.password);
    if (!isMatch) {
      return {
        success: false,
        statusCode: 401,
        message: 'Password yang dimasukkan salah'
      };
    }

    const { password: _, ...safeUser } = user;
    const token = Buffer.from(JSON.stringify({
      id: user.id,
      username: user.username,
      role: user.role,
      exp: Date.now() + 7 * 86400000
    })).toString('base64');

    return {
      success: true,
      statusCode: 200,
      message: 'Login berhasil',
      token,
      user: safeUser
    };
  }

  hasPermission(userRole, allowedRoles) {
    if (userRole === 'admin') return true;
    return allowedRoles.includes(userRole);
  }

  validateToken(token) {
    if (!token) return null;
    if (token.startsWith('Bearer ')) token = token.slice(7);

    try {
      const decoded = JSON.parse(Buffer.from(token, 'base64').toString('utf8'));
      if (!decoded.id || !decoded.exp) return null;
      if (decoded.exp < Date.now()) return null;
      return decoded;
    } catch {
      return null;
    }
  }
}

module.exports = AuthService;

