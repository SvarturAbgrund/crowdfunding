<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/helpers.php';

$message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email'] ?? '');
    $user = User::findByEmail($email);
    if($user){
        $token = bin2hex(random_bytes(24));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        PasswordReset::create($user['id'], $token, $expires);
        // For development: show the reset link. In production send email.
        $resetLink = '/crowdfunding1/auth/password_reset.php?token=' . urlencode($token);
        $message = 'Se generó un enlace de recuperación: <a href="' . htmlspecialchars($resetLink) . '">' . htmlspecialchars($resetLink) . '</a> (válido 1 hora)';
    } else {
        $message = 'Si el email existe, se enviará un enlace de recuperación.';
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<div class="container">
  <h2>Recuperar contraseña</h2>
  <?php if($message): ?><div class="info"><?= $message ?></div><?php endif; ?>
  <form method="post">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>
    <button type="submit" class="btn-primary">Solicitar enlace</button>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
