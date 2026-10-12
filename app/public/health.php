<?php
require_once __DIR__ . '/../src/db.php';

// Kiểm tra sức khỏe DB
try {
    $stmt = $pdo->query("SELECT 1");
    if ($stmt) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'db' => 'connected']);
        exit;
    }
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'db' => 'disconnected']);
    exit;
}

