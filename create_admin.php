<?php
require_once __DIR__ . '/db.php';

function create_user($db, $name, $email, $password, $role){
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss', $name, $email, $hash, $role);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}

// Crear usuarios de ejemplo
$adminId = create_user($db, 'Admin', 'admin@example.com', 'adminpass', 'admin');
$ownerId = create_user($db, 'Emprendedor', 'emprendedor@example.com', 'ownerpass', 'emprendedor');
$investorId = create_user($db, 'Inversionista', 'inversor@example.com', 'investorpass', 'inversionista');

// Crear algunas campañas de ejemplo
$insertCamp = $db->prepare("INSERT INTO campaigns (user_id,title,description,category_id,goal_amount,pledged_amount,status,end_date) VALUES (?,?,?,?,?,?,?,?)");
$now = date('Y-m-d');
$end = date('Y-m-d', strtotime('+30 days'));
$insertCamp->bind_param('issiddss', $ownerId, $t, $d, $cat, $goal, $pledged, $status, $end_date);

// Campaña 1
$t = 'Plataforma educativa para niños';
$d = 'Una app que enseña a programar a niños mediante juegos.';
$cat = 2; // Educación
$goal = 5000.00;
$pledged = 1200.00;
$status = 'approved';
$end_date = $end;
$insertCamp->execute();
$camp1 = $insertCamp->insert_id;

// Campaña 2
$t = 'Dispositivo de salud portátil';
$d = 'Sensor de salud de bajo costo para monitoreo remoto.';
$cat = 3; // Salud
$goal = 15000.00;
$pledged = 3000.00;
$status = 'pending';
$end_date = $end;
$insertCamp->execute();
$camp2 = $insertCamp->insert_id;

$insertCamp->close();

// Recompensas
$insReward = $db->prepare("INSERT INTO rewards (campaign_id,title,amount,description) VALUES (?,?,?,?)");
$insReward->bind_param('isds', $cid, $rt, $ramt, $rdesc);
$cid = $camp1; $rt = 'Agradecimiento digital'; $ramt = 10.00; $rdesc = 'Mención en la web.'; $insReward->execute();
$cid = $camp1; $rt = 'Acceso beta'; $ramt = 50.00; $rdesc = 'Acceso a la versión beta.'; $insReward->execute();
$cid = $camp2; $rt = 'Sticker'; $ramt = 15.00; $rdesc = 'Sticker del proyecto.'; $insReward->execute();
$insReward->close();

echo "Usuarios y datos de ejemplo creados.\n";
echo "Admin: admin@example.com / adminpass\n";
echo "Emprendedor: emprendedor@example.com / ownerpass\n";
echo "Inversionista: inversor@example.com / investorpass\n";

echo "Visita index.php para empezar: http://localhost/crowdfunding1/\n";
?>