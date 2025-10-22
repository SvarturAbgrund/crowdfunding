<?php
// Ensure no whitespace or BOM in the file
ini_set('display_errors', 0);
ob_start();

// Set proper headers
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-cache');

require_once("../db/connection.php");

// Function to send JSON response and exit
function sendJSON($data) {
    ob_clean();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'admin') {
        $stmt1 = $pdo->query("SELECT c.*, u.nombre AS creador, u.email 
                             FROM campaigns c 
                             JOIN users u ON c.user_id=u.id 
                             ORDER BY fecha_creacion DESC");
        $campaigns = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $pdo->query("SELECT id, nombre, email, rol 
                             FROM users 
                             ORDER BY id DESC");
        $users = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        sendJSON([
            "status" => "success",
            "campaigns" => $campaigns,
            "users" => $users
        ]);
    }

    $user_id = intval($_GET['user_id'] ?? 0);
    if (!$user_id) {
        throw new Exception("ID de usuario requerido");
    }

    $stmt = null;
    switch ($action) {
        case 'emprendedor':
            $stmt = $pdo->prepare("SELECT * FROM campaigns 
                                 WHERE user_id = ? 
                                 ORDER BY fecha_creacion DESC");
            break;

        case 'inversionista':
            $stmt = $pdo->prepare("
                SELECT d.id, d.monto, d.fecha, c.titulo AS campaña
                FROM donations d
                JOIN campaigns c ON d.campaign_id = c.id
                WHERE d.user_id = ?
                ORDER BY d.fecha DESC
            ");
            break;

        default:
            throw new Exception("Acción no válida");
    }

    if (!$stmt) {
        throw new Exception("Error preparando la consulta");
    }

    $stmt->execute([$user_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendJSON([
        "status" => "success",
        "data" => $data
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    sendJSON([
        "status" => "error",
        "msg" => "Error de base de datos: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    sendJSON([
        "status" => "error",
        "msg" => $e->getMessage()
    ]);
}