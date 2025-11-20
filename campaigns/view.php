<?php
session_start();
require_once __DIR__ . '/../models/Campaign.php';
require_once __DIR__ . '/../models/Reward.php';

$id = intval($_GET['id'] ?? 0);
$camp = Campaign::get($id);
if(!$camp) die('Campaña no encontrada');
$rewards = Reward::findByCampaign($id);
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php
// Mostrar mensajes flash (si los hubiera)
if(!empty($_SESSION['error'])){ echo '<p style="color:red">'.htmlspecialchars($_SESSION['error']).'</p>'; unset($_SESSION['error']); }
if(!empty($_SESSION['success'])){ echo '<p style="color:green">'.htmlspecialchars($_SESSION['success']).'</p>'; unset($_SESSION['success']); }
?>
<h2><?=htmlspecialchars($camp['title'])?></h2>
<p><?=nl2br(htmlspecialchars($camp['description']))?></p>
<p><strong>Meta:</strong> <?=number_format($camp['goal_amount'],2)?> | <strong>Recaudado:</strong> <?=number_format($camp['pledged_amount'],2)?></p>
<h3>Recompensas</h3>
<?php if(empty($rewards)): ?><p>No hay recompensas para esta campaña.</p><?php else: ?>
  <ul>
  <?php foreach($rewards as $r): ?>
    <li>
      <strong><?=htmlspecialchars($r['title'])?></strong> — <?=number_format($r['amount'],2)?>
      <?php if($r['description']): ?><br><small><?=htmlspecialchars($r['description'])?></small><?php endif; ?>
      <?php $qty = array_key_exists('quantity', $r) ? $r['quantity'] : null; ?>
      <?php if(!is_null($qty)): ?>
        <br><em>Disponibles: <?=intval($qty)?> <?=intval($qty)<=0?'<strong style="color:red">(AGOTADO)</strong>':''?></em>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php if(!empty($_SESSION['user'])): ?>
  <h3>Donar</h3>
  <form method="post" action="/crowdfunding1/donate.php">
    <input type="hidden" name="campaign_id" value="<?=$camp['id']?>">
    <label>Monto: <input name="amount" type="number" step="0.01" required></label><br>
    <label>Recompensa (opcional): <select name="reward_id"><option value="">Ninguna</option><?php foreach($rewards as $r): ?><?php $qty = array_key_exists('quantity', $r) ? $r['quantity'] : null; ?><option value="<?=$r['id']?>" <?=(($qty !== null && intval($qty)<=0)?'disabled':'')?>><?=htmlspecialchars($r['title'])?> - <?=number_format($r['amount'],2)?><?=($qty !== null)?' ('.intval($qty).' left)':''?></option><?php endforeach; ?></select></label><br>
    <button>Donar</button>
  </form>
<?php else: ?>
  <p>Debes <a href="/crowdfunding1/auth/login.php">iniciar sesión</a> para donar.</p>
<?php endif; ?>
<?php if(!empty($_SESSION['user']) && ($_SESSION['user']['role']==='admin' || $_SESSION['user']['id']==$camp['user_id'])): ?>
  <p>
    <a href="/crowdfunding1/campaigns/edit.php?id=<?=$camp['id']?>">Editar</a>
    |
    <a href="/crowdfunding1/campaigns/rewards.php?campaign_id=<?=$camp['id']?>">Gestionar Recompensas</a>
    <form method="post" action="/crowdfunding1/campaigns/delete.php" style="display:inline;margin-left:8px"><input type="hidden" name="id" value="<?=$camp['id']?>"><button onclick="return confirm('Eliminar campaña?')">Eliminar</button></form>
  </p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>