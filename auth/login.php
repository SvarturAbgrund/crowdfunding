<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Logger.php';
require_once __DIR__ . '/helpers.php';

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    $user = User::findByEmail($email);
    if($user){
        if(password_verify($pass, $user['password'])){
            login_user_from_db(DB::get(), $user);
            Logger::log($user['id'], 'user_login', ['email'=>$email]);
            header('Location: /crowdfunding1/'); exit;
        }
    }
    Logger::log(null, 'user_login_failed', ['email'=>$email]);
    $error = 'Credenciales inválidas';
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="login-container">
  <div class="login-card">
    <h2>Inicia Sesión</h2>
    <?php if($error): ?>
      <div class="error-message"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn-primary">Entrar</button>
    </form>
    <p class="login-footer">¿No tienes cuenta? <a href="/crowdfunding1/auth/register.php">Regístrate aquí</a></p>
    <p class="login-footer"><a href="/crowdfunding1/auth/password_request.php">¿Olvidaste tu contraseña?</a></p>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>