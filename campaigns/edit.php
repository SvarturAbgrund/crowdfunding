<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/Campaign.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Logger.php';
require_once __DIR__ . '/../auth/helpers.php';
require_login();

$id = intval($_GET['id'] ?? 0);
if($id <= 0) die('ID inválido');

// Obtener campaña
$camp = Campaign::get($id);
if(!$camp) die('Campaña no encontrada');

// Permisos: el propietario o admin
if($_SESSION['user']['role'] !== 'admin' && !owns_campaign(DB::get(), $id)){
  http_response_code(403); die('Acceso denegado.');
}

$cats = Category::listAll();
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $category = $_POST['category'] ?: null;
  $goal = floatval($_POST['goal'] ?? 0);
  $end = $_POST['end_date'] ?: null;
  $status = $camp['status'];
  if($_SESSION['user']['role'] === 'admin' && in_array($_POST['status'] ?? '', ['draft','pending','approved','rejected'])){
    $status = $_POST['status'];
  }
  if(!$title || !$description || $goal <= 0) $errors[] = 'Complete campos obligatorios.';
  if(empty($errors)){
    Campaign::update($id, $title, $description, $category, $goal, $end, $status);
    Logger::log(!empty($_SESSION['user']['id'])?$_SESSION['user']['id']:null, 'campaign_update', ['campaign_id'=>$id]);
    header('Location: /crowdfunding1/campaigns/view.php?id='.$id); exit;
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Editar Campaña</title></head><body>
<h2>Editar Campaña</h2>
<?php foreach($errors as $e): ?><p style="color:red"><?=htmlspecialchars($e)?></p><?php endforeach; ?>
<form method="post">
  <label>Título: <input name="title" required value="<?=htmlspecialchars($camp['title'])?>"></label><br>
  <label>Descripción:<br><textarea name="description" rows="6" required><?=htmlspecialchars($camp['description'])?></textarea></label><br>
  <label>Categoría: <select name="category"><option value="">--</option><?php foreach($cats as $c): ?><option value="<?=$c['id']?>" <?=($camp['category_id']==$c['id'])?'selected':''?>><?=htmlspecialchars($c['name'])?></option><?php endforeach; ?></select></label><br>
  <label>Meta: <input name="goal" type="number" step="0.01" required value="<?=number_format($camp['goal_amount'],2,'.','')?>"></label><br>
  <label>Fin (YYYY-MM-DD): <input name="end_date" type="date" value="<?=htmlspecialchars($camp['end_date'])?>"></label><br>
  <?php if($_SESSION['user']['role'] === 'admin'): ?>
    <label>Estado: <select name="status"><option value="draft" <?=($camp['status']=='draft')?'selected':''?>>Draft</option><option value="pending" <?=($camp['status']=='pending')?'selected':''?>>Pending</option><option value="approved" <?=($camp['status']=='approved')?'selected':''?>>Approved</option><option value="rejected" <?=($camp['status']=='rejected')?'selected':''?>>Rejected</option></select></label><br>
  <?php else: ?>
    <p>Estado actual: <?=htmlspecialchars($camp['status'])?></p>
  <?php endif; ?>
  <button>Guardar</button>
</form>
</body></html>