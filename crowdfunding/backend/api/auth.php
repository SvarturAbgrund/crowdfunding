<?php
header("Content-Type: application/json; charset=UTF-8");
require_once("../db/connection.php");

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

if (!$action) {
    echo json_encode(["status" => "error", "msg" => "No se especificó acción"]);
    exit;
}

// Función para validar campos
function validateInput($input) {
    return !empty(trim($input['nombre'] ?? '')) && 
           !empty(trim($input['email'] ?? '')) && 
           !empty(trim($input['password'] ?? ''));
}

// REGISTRO
if ($action === 'register') {
    if (!validateInput($input)) {
        echo json_encode(["status" => "error", "msg" => "Datos incompletos"]);
        exit;
    }

    $nombre = trim($input['nombre']);
    $email = trim($input['email']);
    $password = trim($input['password']);
    $rol = in_array($input['rol'], ['emprendedor', 'inversionista']) ? $input['rol'] : 'inversionista';

    // Validar si el correo ya existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(["status" => "error", "msg" => "El correo ya está registrado"]);
        exit;
    }

    // Guardar
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$nombre, $email, $hash, $rol]);
        echo json_encode(["status" => "success", "msg" => "Usuario registrado correctamente"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "msg" => "Error al registrar el usuario"]);
    }
    exit;
}

// LOGIN
if ($action === 'login') {
     error_log("🟢 LOGIN ATTEMPT: " . json_encode($input)); // 👈 esto
    $email = trim($input['email'] ?? '');
    $password = trim($input['password'] ?? '');

    if (!$email || !$password) {
        echo json_encode(["status" => "error", "msg" => "Faltan datos"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    error_log("🔍 USER FOUND: " . json_encode($user));


    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(["status" => "error", "msg" => "Credenciales inválidas"]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "msg" => "Inicio de sesión exitoso",
        "user" => [
            "id" => $user['id'],
            "nombre" => $user['nombre'],
            "email" => $user['email'],
            "rol" => $user['rol']
        ]
    ]);
    exit;
}

echo json_encode(["status" => "error", "msg" => "Acción desconocida"]);
?>