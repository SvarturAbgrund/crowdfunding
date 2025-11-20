<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/Campaign.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Logger.php';
session_start();
if(empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'emprendedor'){
    die('Acceso denegado. Solo emprendedores.');
}
$cats = Category::listAll();
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $cat = $_POST['category'] ?: null;
    $goal = floatval($_POST['goal'] ?? 0);
    if(!$title || !$desc || $goal<=0) $errors[] = 'Complete campos obligatorios.';
    if(empty($errors)){
        $end = date('Y-m-d', strtotime('+30 days'));
        $cid = Campaign::create($_SESSION['user']['id'], $title, $desc, $cat, $goal, $end);
        if($cid){ Logger::log($_SESSION['user']['id'], 'campaign_create', ['campaign_id'=>$cid]); }
        header('Location: /crowdfunding1/campaigns/list.php'); exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Crear Campaña</title></head><body>
<h2>Crear Campaña</h2>
<?php foreach($errors as $e): ?><p style="color:red"><?=htmlspecialchars($e)?></p><?php endforeach; ?>
<form method="post">
  <label>Título: <input name="title" required></label><br>
  <label>Descripción:<br><textarea name="description" rows="6" required></textarea></label><br>
  <label>Categoría: <select name="category"><option value="">--</option><?php foreach($cats as $c): ?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option><?php endforeach; ?></select></label><br>
  <label>Meta: <input name="goal" type="number" step="0.01" required></label><br>
  <button>Crear (se creará como pending)</button>
</form>
</body></html>