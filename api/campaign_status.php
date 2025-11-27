<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/Campaign.php';
header('Content-Type: application/json');
$id = intval($_GET['id'] ?? 0);
if(!$id){ echo json_encode(['ok'=>false,'msg'=>'id requerido']); exit; }
$camp = Campaign::get($id);
if(!$camp){ echo json_encode(['ok'=>false,'msg'=>'no encontrado']); exit; }
echo json_encode(['ok'=>true,'id'=>$camp['id'],'pledged_amount'=>floatval($camp['pledged_amount']),'goal_amount'=>floatval($camp['goal_amount'])]);
exit;
