<?php
header("Content-Type: application/json; charset=UTF-8");
require_once("../db/connection.php");

// Mostrar errores (para depuración)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Detectar el método HTTP
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

// --- RUTAS PRINCIPALES ---
if ($method === 'GET') {
    switch ($action) {
        case 'list':
            getAllCampaigns();
            break;
        case 'getUserCampaigns':
            getUserCampaigns();
            break;
        default:
            sendError('Acción GET no válida');
    }
} elseif ($method === 'POST') {
    switch ($action) {
        case 'create':
            createCampaign();
            break;
        default:
            sendError('Acción POST no válida');
    }
} else {
    sendError('Método no permitido');
}

// --- FUNCIONES ---

function createCampaign() {
    global $pdo;

    // Leer cuerpo JSON
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        sendError("Datos no válidos o cuerpo vacío.");
    }

    $titulo = trim($data['titulo'] ?? '');
    $categoria = trim($data['categoria'] ?? '');
    $descripcion = trim($data['descripcion'] ?? '');
    $meta = floatval($data['meta'] ?? 0);
    $user_id = intval($data['user_id'] ?? 0);

    if (!$titulo || !$categoria || !$descripcion || $meta <= 0 || $user_id <= 0) {
        sendError("Todos los campos son obligatorios.");
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO campaigns (user_id, titulo, categoria, descripcion, meta, recaudado, estado)
            VALUES (?, ?, ?, ?, ?, 0, 'pendiente')
        ");
        $stmt->execute([$user_id, $titulo, $categoria, $descripcion, $meta]);

        sendSuccess(["message" => "Campaña creada correctamente"]);
    } catch (PDOException $e) {
        sendError("Error al crear la campaña: " . $e->getMessage());
    }
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
        sendError('Error al cargar las campañas: ' . $e->getMessage());
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
