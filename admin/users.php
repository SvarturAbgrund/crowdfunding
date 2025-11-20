<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../auth/helpers.php';
require_role('admin');

$errors = [];
// Create user
if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create'){
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? 'inversionista';
  if(!$name || !$email || !$password) $errors[] = 'Complete todos los campos.';
  if(empty($errors)){
    $res = User::create($name, $email, $password, $role);
    if($res){
      require_once __DIR__ . '/../models/Logger.php'; Logger::log($_SESSION['user']['id'], 'user_create', ['user_id'=>$res,'email'=>$email]);
    } else {
      $errors[] = 'No se pudo crear el usuario (¿email ya existe?).';
    }
  }
}

// Delete user
if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete'){
  $uid = intval($_POST['id'] ?? 0);
  if($uid && $uid !== $_SESSION['user']['id']){
    User::delete($uid);
    require_once __DIR__ . '/../models/Logger.php'; Logger::log($_SESSION['user']['id'], 'user_delete', ['user_id'=>$uid]);
  } else {
    $errors[] = 'No puede eliminar el usuario actual.';
  }
}

$users = User::listAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<h2>Gestión de Usuarios</h2>
<?php foreach($errors as $e): ?><p style="color:red"><?=htmlspecialchars($e)?></p><?php endforeach; ?>
<h3>Crear usuario</h3>
<form method="post">
  <input type="hidden" name="action" value="create">
  <label>Nombre: <input name="name" required></label><br>
  <label>Email: <input type="email" name="email" required></label><br>
  <label>Password: <input type="password" name="password" required></label><br>
  <label>Rol: <select name="role"><option value="inversionista">Inversionista</option><option value="emprendedor">Emprendedor</option><option value="admin">Admin</option></select></label><br>
  <button>Crear</button>
</form>

<h3>Usuarios existentes</h3>
<table border="1" cellpadding="6">
  <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th></tr>
  <?php foreach($users as $u): ?>
    <tr>
      <td><?=$u['id']?></td>
      <td><?=htmlspecialchars($u['name'])?></td>
      <td><?=htmlspecialchars($u['email'])?></td>
      <td><?=htmlspecialchars($u['role'])?></td>
      <td>
        <?php if($u['id'] != $_SESSION['user']['id']): ?>
        <form method="post" style="display:inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$u['id']?>"><button onclick="return confirm('Eliminar usuario?')">Eliminar</button></form>
        <?php else: ?>
          (usted)
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
