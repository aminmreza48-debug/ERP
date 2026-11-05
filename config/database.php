<?php
// config/database.php - Database Configuration & PDO Connection

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dbPath = __DIR__ . '/../database/database.sqlite';
            try {
                self::$instance = new PDO('sqlite:' . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->exec('PRAGMA foreign_keys = ON;');
            } catch (PDOException $e) {
                die(json_encode([
                    'error' => 'Database connection failed: ' . $e->getMessage()
                ]));
            }
        }
        return self::$instance;
    }

    public static function resetInstance(): void {
        self::$instance = null;
    }
}

