<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../auth/helpers.php';
// `auth/helpers.php` already starts the session when needed; avoid calling session_start() again
// Ensure JSON responses
header('Content-Type: application/json; charset=utf-8');
if($_SERVER['REQUEST_METHOD'] !== 'POST'){ header('HTTP/1.1 405 Method Not Allowed'); exit; }
$user = $_SESSION['user'] ?? null;
if(empty($user)){ http_response_code(403); echo json_encode(['ok'=>false,'msg'=>'Debes iniciar sesión']); exit; }
// Use direct POST values safely
$campaign_id = intval($_POST['campaign_id'] ?? 0);
$content = trim((string)($_POST['content'] ?? ''));
if(!$campaign_id || !$content){ echo json_encode(['ok'=>false,'msg'=>'Datos inválidos']); exit; }
 $id = Comment::create($campaign_id, $user['id'], $content);
 if($id){
    echo json_encode(['ok'=>true,'id'=>$id,'author'=>$user['name'],'content'=>htmlspecialchars($content),'created_at'=>date('Y-m-d H:i:s')]);
 } else {
    echo json_encode(['ok'=>false,'msg'=>'Error al guardar comentario']);
 }
exit;
