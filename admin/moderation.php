<?php
session_start();
require_once __DIR__ . '/../models/Campaign.php';
require_once __DIR__ . '/../auth/helpers.php';
require_role('admin');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $id = intval($_POST['id']);
  $action = $_POST['action'];
  if(in_array($action,['approved','rejected'])){
    Campaign::setStatus($id, $action);
  }
}

$camps = Campaign::pending();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<h2>Moderación de Proyectos</h2>
<?php foreach($camps as $c): ?>
  <div class="campaign">
    <h3><?=htmlspecialchars($c['title'])?></h3>
    <p><?=nl2br(htmlspecialchars($c['description']))?></p>
    <p>Por: <?=htmlspecialchars($c['name'])?></p>
    <form method="post" style="display:inline">
      <input type="hidden" name="id" value="<?=$c['id']?>">
      <button name="action" value="approved">Aprobar</button>
      <button name="action" value="rejected">Rechazar</button>
    </form>
  </div>
<?php endforeach; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>