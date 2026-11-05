<?php
// src/Services/AuthService.php - Authentication & Authorization Logic

class AuthService {
    private PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db ?? Database::getConnection();
    }

    /**
     * Authenticate user with username/email and password
     * Cyclomatic Complexity = 6 (multiple conditional branches)
     */
    public function authenticate(string $identifier, string $password): array {
        $identifier = trim($identifier);
        $password = trim($password);

        // Branch 1: Empty input validation
        if (empty($identifier) || empty($password)) {
            return [
                'success' => false,
                'status_code' => 400,
                'message' => 'Username dan password wajib diisi'
            ];
        }

        // Branch 2: Find user by username OR email
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE (username = :id OR email = :id) LIMIT 1'
        );
        $stmt->execute(['id' => $identifier]);
        $user = $stmt->fetch();

        // Branch 3: User not found
        if (!$user) {
            return [
                'success' => false,
                'status_code' => 401,
                'message' => 'Kredensial login tidak valid'
            ];
        }

        // Branch 4: Account inactive check
        if ($user['status'] !== 'active') {
            return [
                'success' => false,
                'status_code' => 403,
                'message' => 'Akun dinonaktifkan. Hubungi administrator'
            ];
        }

        // Branch 5: Password verification
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'status_code' => 401,
                'message' => 'Password yang dimasukkan salah'
            ];
        }

        // Generate simple mock JWT / session token
        $token = base64_encode(json_encode([
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'exp' => time() + (86400 * 7)
        ]));

        unset($user['password']);

        // Log audit
        $this->logAudit((int)$user['id'], 'LOGIN_SUCCESS', 'Auth', 'User logged in successfully');

        return [
            'success' => true,
            'status_code' => 200,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ];
    }

    /**
     * Verify role permission
     * Cyclomatic Complexity = 4
     */
    public function hasPermission(string $userRole, array $allowedRoles): bool {
        if ($userRole === 'admin') {
            return true; // Admin has universal access
        }
        return in_array($userRole, $allowedRoles, true);
    }

    /**
     * Validate session or Bearer token
     * Cyclomatic Complexity = 5
     */
    public function validateToken(?string $token): ?array {
        if (empty($token)) {
            return null;
        }

        if (str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }

        $decoded = json_decode(base64_decode($token), true);
        if (!$decoded || !isset($decoded['id'], $decoded['exp'])) {
            return null;
        }

        if ($decoded['exp'] < time()) {
            return null; // Expired
        }

        return $decoded;
    }

    private function logAudit(?int $userId, string $action, string $module, string $detail): void {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO audit_logs (user_id, action, module, detail, ip_address) 
                 VALUES (:user_id, :action, :module, :detail, :ip)'
            );
            $stmt->execute([
                'user_id' => $userId,
                'action' => $action,
                'module' => $module,
                'detail' => $detail,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);
        } catch (Exception $e) {
            // suppress audit errors
        }
    }
}

