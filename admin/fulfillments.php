<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/Donation.php';
require_once __DIR__ . '/../auth/helpers.php';
require_login();

// Asegurar columnas delivered en donations
$db = DB::get();
$col = $db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='donations' AND COLUMN_NAME='delivered'")->fetch_assoc();
if(!$col){ @ $db->query('ALTER TABLE donations ADD COLUMN delivered TINYINT(1) DEFAULT 0, ADD COLUMN delivered_at DATETIME DEFAULT NULL'); }

// Mostrar y marcar entregas. Admin puede ver todo; emprendedor ve solo sus campañas' donaciones
if($_SESSION['user']['role'] === 'admin'){
  $donations = Donation::listAll();
} else if($_SESSION['user']['role'] === 'emprendedor'){
  $donations = Donation::listForOwner($_SESSION['user']['id']);
} else {
  http_response_code(403); die('Acceso denegado.');
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark'])){
  $did = intval($_POST['id']);
  if($did){
    Donation::markDelivered($did);
    require_once __DIR__ . '/../models/Logger.php'; Logger::log($_SESSION['user']['id'], 'donation_mark_delivered', ['donation_id'=>$did]);
    header('Location: /crowdfunding1/admin/fulfillments.php'); exit;
  }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<h2>Fulfillments / Entregas</h2>
<?php if(empty($donations)): ?><p>No hay donaciones con recompensas registradas.</p><?php endif; ?>
<?php foreach($donations as $d): ?>
  <div style="border:1px solid #ddd;padding:8px;margin:8px 0">
    <p><strong><?=htmlspecialchars($d['campaign'])?></strong> — Donante: <?=htmlspecialchars($d['donor'])?> — Monto: <?=number_format($d['amount'],2)?> — Recompensa: <?=htmlspecialchars($d['reward']?:'(sin)')?></p>
    <p>Fecha: <?=htmlspecialchars($d['created_at'])?> | Entregado: <?=($d['delivered']?'Sí ('.htmlspecialchars($d['delivered_at']).')':'No')?></p>
    <?php if(!$d['delivered']): ?>
      <form method="post"><input type="hidden" name="id" value="<?=$d['id']?>"><button name="mark">Marcar como entregado</button></form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
