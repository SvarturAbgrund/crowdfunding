<?php
session_start();
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
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title></head><body>
<h2>Login</h2>
<?php if($error): ?><p style="color:red"><?=htmlspecialchars($error)?></p><?php endif; ?>
<form method="post">
  <label>Email: <input type="email" name="email" required></label><br>
  <label>Password: <input type="password" name="password" required></label><br>
  <button>Entrar</button>
</form>
</body></html>