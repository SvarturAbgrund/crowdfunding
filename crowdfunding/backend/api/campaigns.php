<?php
header("Content-Type: application/json; charset=UTF-8");
require_once("../db/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? 'list';

    switch ($action) {
        case 'list':
            getAllCampaigns();
            break;
        case 'getUserCampaigns':
            getUserCampaigns();
            break;
        default:
            sendError('Acción no válida');
    }
} else {
    sendError('Método no permitido');
}

function getUserCampaigns() {
    global $pdo;
    
    if (!isset($_GET['userId'])) {
        sendError('ID de usuario no proporcionado');
        return;
    }

    $userId = intval($_GET['userId']);

    try {
        $stmt = $pdo->prepare("
            SELECT id, titulo, descripcion, meta, recaudado, estado, fecha_creacion
            FROM campaigns 
            WHERE user_id = ?
            ORDER BY fecha_creacion DESC
        ");
        
        $stmt->execute([$userId]);
        sendSuccess($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        sendError('Error al obtener las campañas: ' . $e->getMessage());
    }
}

function getAllCampaigns() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, u.nombre AS creador 
            FROM campaigns c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.estado = 'aprobada' 
            ORDER BY c.fecha_creacion DESC
        ");
        $stmt->execute();
        sendSuccess($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(PDOException $e) {
        sendError('Error al cargar las campañas');
    }
}

function sendSuccess($data) {
    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
    exit;
}

function sendError($message) {
    echo json_encode([
        "status" => "error",
        "message" => $message
    ]);
    exit;
}
?>