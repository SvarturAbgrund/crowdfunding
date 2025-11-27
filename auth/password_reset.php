<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/helpers.php';

$token = $_GET['token'] ?? '';
$msg = '';
$showForm = false;
$pr = null;
if($token){
    $pr = PasswordReset::findByToken($token);
    if($pr && strtotime($pr['expires_at']) > time()){
        $showForm = true;
    } else {
        $msg = 'Token inválido o expirado.';
    }
} else {
    $msg = 'Token requerido.';
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $showForm){
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    if(!$password || $password !== $password2){
        $msg = 'Las contraseñas no coinciden.';
    } else {
        // Use model to update password (keeps DB access in models layer)
        if(User::updatePassword($pr['user_id'], $password)){
            PasswordReset::deleteByToken($token);
            $msg = 'Contraseña actualizada. Ya puedes iniciar sesión.';
            $showForm = false;
        } else {
            $msg = 'Error al actualizar la contraseña.';
        }
    }
}

?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<div class="container">
  <h2>Restablecer contraseña</h2>
  <?php if($msg): ?><div class="error-message"><?=htmlspecialchars($msg)?></div><?php endif; ?>
  <?php if($showForm): ?>
    <form method="post">
      <label for="password">Nueva contraseña</label>
      <input type="password" id="password" name="password" required>
      <label for="password2">Repetir contraseña</label>
      <input type="password" id="password2" name="password2" required>
      <button type="submit" class="btn-primary">Guardar</button>
    </form>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
