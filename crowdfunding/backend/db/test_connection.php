<?php
// test_connection.php
// Prueba simple para verificar conexión PDO a MySQL y manejo de errores

// Mostrar errores en pantalla (temporal para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ajusta estos valores si no coinciden con los tuyos
$host = "localhost";
$user = "root";
$pass = "";           // en XAMPP por defecto es cadena vacía
$dbname = "crowdfunding"; // asegúrate de que la DB exista

header('Content-Type: application/json; charset=utf-8');

$response = [
    "php_version" => phpversion(),
    "mysql_status" => null,
    "connection" => null,
    "error" => null
];

try {
    // Intentar conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Comprobar una consulta sencilla
    $stmt = $pdo->query("SELECT DATABASE() AS db, VERSION() AS mysql_version");
    $row = $stmt->fetch();

    $response["mysql_status"] = "connected";
    $response["connection"] = [
        "database" => $row['db'] ?? $dbname,
        "mysql_version" => $row['mysql_version'] ?? null
    ];
} catch (PDOException $e) {
    $response["mysql_status"] = "error";
    $response["error"] = [
        "message" => $e->getMessage(),
        "code" => $e->getCode()
    ];
}

// Opcional: escribe también un archivo debug para revisar si la salida JSON falla
@file_put_contents(__DIR__ . "/debug_connection.log", date('c') . " - " . json_encode($response) . PHP_EOL, FILE_APPEND);

echo json_encode($response, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
