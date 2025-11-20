<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/Campaign.php';
require_once __DIR__ . '/../models/Logger.php';
require_once __DIR__ . '/../auth/helpers.php';
require_login();
require_role('emprendedor');

// Obtener campañas del usuario
$camps = Campaign::listByOwner($_SESSION['user']['id']);
Logger::log($_SESSION['user']['id'], 'view_dashboard', ['count'=>count($camps)]);
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<h2>Dashboard - Mis Campañas</h2>
<div id="dashboard">
<?php foreach($camps as $c): ?>
  <div class="campaign" data-id="<?=$c['id']?>">
    <h3><?=htmlspecialchars($c['title'])?></h3>
    <p>Meta: <?=number_format($c['goal_amount'],2)?> | Recaudado: <span class="pledged"><?=number_format($c['pledged_amount'],2)?></span></p>
    <p>Backers: <span class="backers">–</span> | Estado: <span class="status"><?=htmlspecialchars($c['status'])?></span></p>
    <p>Fin: <?=htmlspecialchars($c['end_date'])?></p>
    <p><a href="/crowdfunding1/campaigns/view.php?id=<?=$c['id']?>">Ver</a> | <a href="/crowdfunding1/campaigns/edit.php?id=<?=$c['id']?>">Editar</a></p>
  </div>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
