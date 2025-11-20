<?php
session_start();
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
        } else {
            $errors[] = 'Error al registrar (¿email ya usado?).';
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Registro</title></head><body>
<h2>Registro</h2>
<?php foreach($errors as $e): ?><p style="color:red"><?=htmlspecialchars($e)?></p><?php endforeach; ?>
<form method="post">
  <label>Nombre: <input type="text" name="name" required></label><br>
  <label>Email: <input type="email" name="email" required></label><br>
  <label>Password: <input type="password" name="password" required></label><br>
  <label>Rol: <select name="role"><option value="inversionista">Inversionista</option><option value="emprendedor">Emprendedor</option></select></label><br>
  <button>Registrar</button>
</form>
</body></html>