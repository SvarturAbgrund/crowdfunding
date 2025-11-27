<?php
session_start();
require_once __DIR__ . '/../models/Campaign.php';
require_once __DIR__ . '/../models/Reward.php';
require_once __DIR__ . '/../auth/helpers.php';

$id = intval($_GET['id'] ?? 0);
$camp = Campaign::get($id);
if(!$camp) die('Campaña no encontrada');

// Validar permisos de visibilidad
$is_owner = !empty($_SESSION['user']) && (int)$_SESSION['user']['id'] === (int)$camp['user_id'];
$is_admin = !empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';

// Solo owner, admin, o si está approved pueden verla
if($camp['status'] !== 'approved' && !$is_owner && !$is_admin) {
    die('Acceso denegado. Esta campaña no es pública.');
}

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
<!-- Progress bar -->
<div class="progress-wrapper" data-campaign-id="<?=$camp['id']?>">
  <div class="progress-track" style="background:#f1f5f9;border-radius:8px;height:18px;overflow:hidden;">
    <?php $pct = $camp['goal_amount']>0 ? min(100,($camp['pledged_amount']/$camp['goal_amount'])*100) : 0; ?>
    <div class="progress-fill" style="width:<?=intval($pct)?>%;background:linear-gradient(90deg,#0052CC,#E74C3C);height:100%;transition:width 600ms ease;"></div>
  </div>
  <div class="progress-meta"><small><span class="pledged-display"><?=number_format($camp['pledged_amount'],2)?></span> recaudado de <span class="goal-display"><?=number_format($camp['goal_amount'],2)?></span> (<span class="pct-display"><?=intval($pct)?></span>%)</small></div>
</div>
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
<!-- Comments section -->
<section id="comments">
  <h3>Comentarios</h3>
  <div id="comments-list">
    <?php require_once __DIR__ . '/../models/Comment.php';
    $comments = Comment::listByCampaign($id);
    foreach($comments as $cm): ?>
      <div class="comment"><strong><?=htmlspecialchars($cm['author'])?></strong> <small><?=htmlspecialchars($cm['created_at'])?></small><p><?=nl2br(htmlspecialchars($cm['content']))?></p></div>
    <?php endforeach; ?>
  </div>
  <?php if(!empty($_SESSION['user'])): ?>
    <form id="comment-form">
      <input type="hidden" name="campaign_id" value="<?=$id?>">
      <textarea name="content" rows="3" placeholder="Escribe tu comentario..." required></textarea>
      <button type="submit" class="btn-primary">Comentar</button>
    </form>
  <?php else: ?>
    <p>Debes <a href="/crowdfunding1/auth/login.php">iniciar sesión</a> para comentar.</p>
  <?php endif; ?>
</section>

<script>
// Poll campaign status every 5s and update progress
(function(){
  const wrapper = document.querySelector('.progress-wrapper');
  if(!wrapper) return;
  const id = wrapper.getAttribute('data-campaign-id');
  function refresh(){
    fetch('/crowdfunding1/api/campaign_status.php?id='+encodeURIComponent(id))
      .then(r=>r.json()).then(j=>{
        if(!j.ok) return;
        const pledged = parseFloat(j.pledged_amount);
        const goal = parseFloat(j.goal_amount);
        const pct = goal>0?Math.min(100,Math.round((pledged/goal)*100)):0;
        const fill = wrapper.querySelector('.progress-fill');
        const pctEl = wrapper.querySelector('.pct-display');
        const pledgedEl = wrapper.querySelector('.pledged-display');
        if(fill) fill.style.width = pct + '%';
        if(pctEl) pctEl.textContent = pct;
        if(pledgedEl) pledgedEl.textContent = pledged.toFixed(2);
      }).catch(()=>{});
  }
  setInterval(refresh,5000);
  document.addEventListener('DOMContentLoaded', refresh);

  // Comment form submit via AJAX
  const cform = document.getElementById('comment-form');
  if(cform){
    cform.addEventListener('submit', function(e){
      e.preventDefault();
      const data = new FormData(cform);
      fetch('/crowdfunding1/campaigns/add_comment.php',{method:'POST',body:data,credentials:'same-origin'})
        .then(r=>r.json()).then(j=>{
          if(!j.ok){ alert(j.msg || 'Error'); return; }
          const list = document.getElementById('comments-list');
          const div = document.createElement('div'); div.className='comment';
          div.innerHTML = '<strong>'+ (j.author||'') +'</strong> <small>'+j.created_at+'</small><p>'+ (j.content||'') +'</p>';
          list.insertBefore(div, list.firstChild);
          cform.querySelector('textarea').value='';
        }).catch(()=>{ alert('Error al enviar comentario'); });
    });
  }
})();
</script>
<?php if(!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'inversionista' && !$is_owner): ?>
  <h3>Donar</h3>
  <form method="post" action="/crowdfunding1/donate.php">
    <input type="hidden" name="campaign_id" value="<?=$camp['id']?>">
    <label>Monto: <input name="amount" type="number" step="0.01" required></label><br>
    <label>Recompensa (opcional): <select name="reward_id"><option value="">Ninguna</option><?php foreach($rewards as $r): ?><?php $qty = array_key_exists('quantity', $r) ? $r['quantity'] : null; ?><option value="<?=$r['id']?>" <?=(($qty !== null && intval($qty)<=0)?'disabled':'')?>><?=htmlspecialchars($r['title'])?> - <?=number_format($r['amount'],2)?><?=($qty !== null)?' ('.intval($qty).' left)':''?></option><?php endforeach; ?></select></label><br>
    <button>Donar</button>
  </form>
<?php elseif(empty($_SESSION['user'])): ?>
  <p>Debes <a href="/crowdfunding1/auth/login.php">iniciar sesión</a> para donar.</p>
<?php elseif($is_owner): ?>
  <p><em>Solo inversionistas pueden donar.</em></p>
<?php elseif($_SESSION['user']['role'] === 'emprendedor'): ?>
  <p><em>Solo los inversores pueden donar. Cambia tu tipo de cuenta para hacerlo.</em></p>
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