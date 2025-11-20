<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/Campaign.php';
require_once __DIR__ . '/../models/Logger.php';
require_once __DIR__ . '/../auth/helpers.php';
require_login();

if($_SERVER['REQUEST_METHOD'] !== 'POST') die('Método inválido');
$id = intval($_POST['id'] ?? 0);
if($id <= 0) die('ID inválido');

// Permisos
if($_SESSION['user']['role'] !== 'admin' && !owns_campaign(DB::get(), $id)){
    http_response_code(403); die('Acceso denegado.');
}

if(Campaign::delete($id)){
    Logger::log(!empty($_SESSION['user']['id'])?$_SESSION['user']['id']:null, 'campaign_delete', ['campaign_id'=>$id]);
}
header('Location: /crowdfunding1/campaigns/list.php');
exit;
?>