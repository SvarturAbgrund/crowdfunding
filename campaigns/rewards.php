<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/Reward.php';
require_once __DIR__ . '/../auth/helpers.php';
require_login();

$campaign_id = intval($_GET['campaign_id'] ?? 0);
if($campaign_id <= 0) die('ID de campaña inválido');

// Verificar que el usuario es propietario o admin
if($_SESSION['user']['role'] !== 'admin' && !owns_campaign(DB::get(), $campaign_id)){
    http_response_code(403); die('Acceso denegado.');
}

// Asegurar que la columna `quantity` exista (intentar ALTER si no existe)
$db = DB::get();
$col = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='rewards' AND COLUMN_NAME='quantity'")->fetch_assoc();
if(!$col){
    @ $db->query('ALTER TABLE rewards ADD COLUMN quantity INT DEFAULT NULL');
}

$errors = [];
// Crear/editar
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'] ?? '';
    if($action === 'create'){
        $title = trim($_POST['title'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $desc = trim($_POST['description'] ?? '');
        $quantity = is_numeric($_POST['quantity']) ? intval($_POST['quantity']) : null;
        if(!$title || $amount <= 0) $errors[] = 'Título y monto son obligatorios.';
        if(empty($errors)){
            $rid = Reward::create($campaign_id, $title, $amount, $desc, $quantity);
            require_once __DIR__ . '/../models/Logger.php';
            Logger::log($_SESSION['user']['id'] ?? null, 'reward_create', ['campaign_id'=>$campaign_id, 'reward_id'=>$rid]);
            header('Location: /crowdfunding1/campaigns/rewards.php?campaign_id='.$campaign_id); exit;
        }
    }
    if($action === 'delete'){
        $rid = intval($_POST['id'] ?? 0);
        if($rid){
            Reward::delete($rid, $campaign_id);
            require_once __DIR__ . '/../models/Logger.php';
            Logger::log($_SESSION['user']['id'] ?? null, 'reward_delete', ['campaign_id'=>$campaign_id, 'reward_id'=>$rid]);
            header('Location: /crowdfunding1/campaigns/rewards.php?campaign_id='.$campaign_id); exit;
        }
    }
    if($action === 'edit'){
        $rid = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $desc = trim($_POST['description'] ?? '');
        $quantity = is_numeric($_POST['quantity']) ? intval($_POST['quantity']) : null;
        if($rid && $title && $amount>0){
            Reward::update($rid, $campaign_id, $title, $amount, $desc, $quantity);
            require_once __DIR__ . '/../models/Logger.php';
            Logger::log($_SESSION['user']['id'] ?? null, 'reward_update', ['campaign_id'=>$campaign_id, 'reward_id'=>$rid]);
            header('Location: /crowdfunding1/campaigns/rewards.php?campaign_id='.$campaign_id); exit;
        }
    }
}

$rewards = Reward::findByCampaign($campaign_id);
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<h2>Recompensas - Gestión (Campaña #<?=$campaign_id?>)</h2>
<?php foreach($errors as $e): ?><p style="color:red"><?=htmlspecialchars($e)?></p><?php endforeach; ?>
<h3>Crear recompensa</h3>
<form method="post">
  <input type="hidden" name="action" value="create">
  <label>Título: <input name="title" required></label><br>
  <label>Monto mínimo: <input name="amount" type="number" step="0.01" required></label><br>
  <label>Descripción:<br><textarea name="description"></textarea></label><br>
  <label>Cantidad (opcional): <input name="quantity" type="number" min="0"></label><br>
  <button>Agregar</button>
</form>

<h3>Recompensas existentes</h3>
<?php if(empty($rewards)): ?><p>No hay recompensas registradas.</p><?php endif; ?>
<?php foreach($rewards as $r): ?>
  <div style="border:1px solid #ddd;padding:8px;margin:8px 0">
    <form method="post">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" value="<?=$r['id']?>">
      <label>Título: <input name="title" value="<?=htmlspecialchars($r['title'])?>"></label><br>
      <label>Monto mínimo: <input name="amount" type="number" step="0.01" value="<?=number_format($r['amount'],2,'.','')?>"></label><br>
      <label>Descripción:<br><textarea name="description"><?=htmlspecialchars($r['description'])?></textarea></label><br>
      <label>Cantidad (vacío = ilimitado): <input name="quantity" type="number" min="0" value="<?=is_null($r['quantity'])?'':intval($r['quantity'])?>"></label><br>
      <button>Guardar</button>
    </form>
    <form method="post" style="display:inline;margin-top:6px"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button onclick="return confirm('Eliminar recompensa?')">Eliminar</button></form>
  </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
