<?php
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=crowdfunding;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(500);
    die(json_encode([
        "status" => "error",
        "msg" => "Error de conexión: " . $e->getMessage()
    ]));
}