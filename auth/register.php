<?php
require_once __DIR__ . '/../models/Db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Logger.php';
require_once __DIR__ . '/helpers.php';

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'inversionista';
    if(!$name || !$email || !$password) $errors[] = 'Complete todos los campos.';
    if(empty($errors)){
        $res = User::create($name, $email, $password, $role);
        if($res){
            // Auto-login después de registro
            $_SESSION['user'] = [
                'id' => (int)$res,
                'name' => $name,
                'email' => $email,
                'role' => $role
            ];
            Logger::log($_SESSION['user']['id'], 'user_register', ['email'=>$email]);
            header('Location: /crowdfunding1/'); exit;
        }
    }
}
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="register-container">
  <div class="register-card">
    <h2>Crear Cuenta</h2>
    <?php foreach($errors as $e): ?>
      <div class="error-message"><?=htmlspecialchars($e)?></div>
    <?php endforeach; ?>
    <form method="post">
      <div class="form-group">
        <label for="name">Nombre Completo</label>
        <input type="text" id="name" name="name" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="role">Tipo de Usuario</label>
        <select id="role" name="role">
          <option value="inversionista">Inversionista</option>
          <option value="emprendedor">Emprendedor</option>
        </select>
      </div>
      <button type="submit" class="btn-primary">Registrar</button>
    </form>
    <p class="register-footer">¿Ya tienes cuenta? <a href="/crowdfunding1/auth/login.php">Inicia sesión aquí</a></p>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>