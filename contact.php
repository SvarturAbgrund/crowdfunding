<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth/helpers.php';

// Ensure messages table exists (safe to run multiple times)
$db->query("CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150),
  email VARCHAR(150),
  subject VARCHAR(200),
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$success = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if($name && $email && $message){
        $stmt = $db->prepare('INSERT INTO messages (name,email,subject,message) VALUES (?,?,?,?)');
        $stmt->bind_param('ssss',$name,$email,$subject,$message);
        if($stmt->execute()) $success = 'Mensaje enviado. El admin lo revisará pronto.';
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
