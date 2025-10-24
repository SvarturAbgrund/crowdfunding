<?php
header("Content-Type: application/json; charset=UTF-8");
require_once("../db/connection.php");

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getAllData':
        getAllData();
        break;
    case 'updateCampaign':
        updateCampaign();
        break;
    case 'deleteCampaign':
        deleteCampaign();
        break;
    case 'deleteUser':
        deleteUser();
        break;
    default:
        echo json_encode(["status" => "error", "msg" => "Acción no válida"]);
        break;
}

// ✅ Traer campañas y usuarios
function getAllData() {
    global $pdo;

    try {
        $campaigns = $pdo->query("
            SELECT c.id, c.titulo, c.estado, u.nombre AS creador
            FROM campaigns c
            JOIN users u ON c.user_id = u.id
            ORDER BY c.fecha_creacion DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $users = $pdo->query("
            SELECT id, nombre, email, rol
            FROM users
            ORDER BY id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success",
            "campaigns" => $campaigns,
            "users" => $users
        ]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "msg" => "Error al obtener datos: " . $e->getMessage()]);
    }
}

// ✅ Actualizar estado de campaña
function updateCampaign() {
    global $pdo;
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id']) || !isset($input['estado'])) {
        echo json_encode(["status" => "error", "msg" => "Datos incompletos"]);
        return;
    }

    $stmt = $pdo->prepare("UPDATE campaigns SET estado = ? WHERE id = ?");
    $stmt->execute([$input['estado'], $input['id']]);
    echo json_encode(["status" => "success", "msg" => "Campaña actualizada"]);
}

// ✅ Eliminar campaña
function deleteCampaign() {
    global $pdo;
    $input = json_decode(file_get_contents("php://input"), true);

    $stmt = $pdo->prepare("DELETE FROM campaigns WHERE id = ?");
    $stmt->execute([$input['id']]);
    echo json_encode(["status" => "success", "msg" => "Campaña eliminada"]);
}

// ✅ Eliminar usuario
function deleteUser() {
    global $pdo;
    $input = json_decode(file_get_contents("php://input"), true);

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$input['id']]);
    echo json_encode(["status" => "success", "msg" => "Usuario eliminado"]);
}
?>
