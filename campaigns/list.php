<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/Campaign.php';
session_start();

$q = trim($_GET['q'] ?? '');
$cat = $_GET['category'] ?? '';
$res = Campaign::search($q, $cat);
$cats = DB::get()->query('SELECT * FROM categories')->fetch_all(MYSQLI_ASSOC);
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<h2>Proyectos</h2>
<form method="get">
  <input name="q" placeholder="Buscar" value="<?=htmlspecialchars($_GET['q'] ?? '')?>">
  <select name="category"><option value="">Todas</option><?php foreach($cats as $c): ?><option value="<?=$c['id']?>" <?=(isset($_GET['category']) && $_GET['category']==$c['id'])?'selected':''?>><?=htmlspecialchars($c['name'])?></option><?php endforeach; ?></select>
  <button>Buscar</button>
</form>
<?php while($row = $res->fetch_assoc()): ?>
  <div class="campaign">
    <h3><?=htmlspecialchars($row['title'])?></h3>
    <p><?=nl2br(htmlspecialchars(substr($row['description'],0,200)))?>...</p>
    <p><strong>Categoría:</strong> <?=htmlspecialchars($row['category'])?> | <strong>Meta:</strong> <?=number_format($row['goal_amount'],2)?></p>
    <a href="view.php?id=<?=$row['id']?>">Ver</a>
    <?php if(!empty($_SESSION['user']) && ($_SESSION['user']['role']==='admin' || $_SESSION['user']['id']==$row['user_id'])): ?>
      | <a href="edit.php?id=<?=$row['id']?>">Editar</a>
      <form method="post" action="delete.php" style="display:inline;margin-left:6px"><input type="hidden" name="id" value="<?=$row['id']?>"><button onclick="return confirm('Eliminar campaña?')">Eliminar</button></form>
    <?php endif; ?>
  </div>
<?php endwhile; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
