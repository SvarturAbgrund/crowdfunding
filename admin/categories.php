<?php
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../auth/helpers.php';
require_role('admin');

$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['add'])){
        $name = trim($_POST['name'] ?? '');
        if($name){
            if(Category::create($name)){
                require_once __DIR__ . '/../models/Logger.php'; Logger::log($_SESSION['user']['id'], 'category_create', ['name'=>$name]);
            } else {
                $errors[] = 'No se pudo crear la categoría.';
            }
        }
    }
    if(isset($_POST['delete'])){
        $id = intval($_POST['id'] ?? 0);
        if($id){
            Category::delete($id);
            require_once __DIR__ . '/../models/Logger.php'; Logger::log($_SESSION['user']['id'], 'category_delete', ['id'=>$id]);
        }
    }
}

$cats = Category::listAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<h2>Gestión de Categorías</h2>
<?php foreach($errors as $e): ?><p style="color:red"><?=htmlspecialchars($e)?></p><?php endforeach; ?>
<form method="post">
  <label>Nueva categoría: <input name="name" required></label>
  <button name="add">Agregar</button>
</form>
<ul>
<?php foreach($cats as $c): ?>
  <li><?=htmlspecialchars($c['name'])?> <form method="post" style="display:inline"><input type="hidden" name="id" value="<?=$c['id']?>"><button name="delete" onclick="return confirm('Eliminar categoría?')">Eliminar</button></form></li>
<?php endforeach; ?>
</ul>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
