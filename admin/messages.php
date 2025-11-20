<?php
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../auth/helpers.php';
require_role('admin');

$msgs = Message::listAll();
// handle response or delete
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if(isset($_POST['delete'])){
    $mid = intval($_POST['id'] ?? 0);
    if($mid) {
      Message::delete($mid);
      require_once __DIR__ . '/../models/Logger.php'; Logger::log($_SESSION['user']['id'], 'message_delete', ['message_id'=>$mid]);
    }
    header('Location: /crowdfunding1/admin/messages.php'); exit;
  }
  if(isset($_POST['respond'])){
    $mid = intval($_POST['id'] ?? 0);
    $response = trim($_POST['response'] ?? '');
    if($mid && $response !== ''){
      Message::respond($mid, $response);
      require_once __DIR__ . '/../models/Logger.php'; Logger::log($_SESSION['user']['id'], 'message_respond', ['message_id'=>$mid]);
      header('Location: /crowdfunding1/admin/messages.php'); exit;
    }
  }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<h2>Mensajes recibidos</h2>
<?php foreach($msgs as $m): ?>
  <div style="border:1px solid #ddd;padding:8px;margin:8px 0">
    <p><strong><?=htmlspecialchars($m['subject']?:'(sin asunto)')?></strong> — <?=htmlspecialchars($m['name'])?> (<?=htmlspecialchars($m['email'])?>) <em><?=htmlspecialchars($m['created_at'])?></em></p>
    <p><?=nl2br(htmlspecialchars($m['message']))?></p>
    <?php if(!empty($m['response'])): ?>
      <p><strong>Respuesta:</strong> <?=nl2br(htmlspecialchars($m['response']))?> <?php if(!empty($m['responded_at'])): ?><em>(<?=htmlspecialchars($m['responded_at'])?>)</em><?php endif; ?></p>
    <?php else: ?>
      <form method="post">
        <input type="hidden" name="id" value="<?=intval($m['id'])?>">
        <label>Responder:<br><textarea name="response" rows="3" cols="60"></textarea></label><br>
        <button name="respond">Enviar respuesta (guardada)</button>
      </form>
    <?php endif; ?>
    <form method="post" style="margin-top:6px"><input type="hidden" name="id" value="<?=intval($m['id'])?>"><button name="delete" onclick="return confirm('Eliminar mensaje?')">Eliminar</button></form>
  </div>
<?php endforeach; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
