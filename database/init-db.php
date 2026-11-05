<?php
// Database Initialization Script
$dbPath = __DIR__ . '/database.sqlite';

if (file_exists($dbPath)) {
    unlink($dbPath);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schema);
    echo "[OK] Schema created successfully.\n";

    $seeder = file_get_contents(__DIR__ . '/seeder.sql');
    $pdo->exec($seeder);
    echo "[OK] Seed data inserted successfully.\n";

    // Verify
    $stmt = $pdo->query("SELECT count(*) as total FROM users");
    $usersCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "[OK] Database ready! Total users seeded: {$usersCount}\n";
} catch (Exception $e) {
    echo "[ERROR] Database init failed: " . $e->getMessage() . "\n";
    exit(1);
}

