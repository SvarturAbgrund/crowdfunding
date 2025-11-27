<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth/helpers.php';
require_once __DIR__ . '/models/Message.php';

$success = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $subject = trim($_POST['subject'] ?? '');
  $message = trim($_POST['message'] ?? '');
  if($name && $email && $message){
    if(Message::create($name,$email,$subject,$message)){
      $success = 'Mensaje enviado. El admin lo revisará pronto.';
    }
  }
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<h2>Contactar al Administrador</h2>
<?php if($success): ?><p style="color:green"><?=htmlspecialchars($success)?></p><?php endif; ?>
<form method="post">
  <label>Nombre: <input name="name" required></label><br>
  <label>Email: <input name="email" type="email" required></label><br>
  <label>Asunto: <input name="subject"></label><br>
  <label>Mensaje:<br><textarea name="message" rows="6" required></textarea></label><br>
  <button>Enviar</button>
</form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
