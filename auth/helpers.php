<?php
// auth/helpers.php - utilidades de sesión y control de roles
if (session_status() === PHP_SESSION_NONE) session_start();

function current_user(){
    return !empty($_SESSION['user']) ? $_SESSION['user'] : null;
}

function is_logged_in(){
    return !empty($_SESSION['user']);
}

function require_login(){
    if(!is_logged_in()){
        header('Location: /crowdfunding1/auth/login.php');
        exit;
    }
}

function require_role($roles){
    if(!is_array($roles)) $roles = [$roles];
    if(!is_logged_in() || !in_array($_SESSION['user']['role'], $roles)){
        http_response_code(403);
        die('Acceso denegado.');
    }
}

function login_user_from_db($db, $user_row){
    // Establece la sesión con campos mínimos
    $_SESSION['user'] = [
        'id' => (int)$user_row['id'],
        'name' => $user_row['name'],
        'email' => $user_row['email'],
        'role' => $user_row['role']
    ];
}

function logout(){
    session_unset();
    session_destroy();
}

function owns_campaign($db, $campaign_id){
    if(!is_logged_in()) return false;
    $stmt = $db->prepare('SELECT user_id FROM campaigns WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $campaign_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()){
        return ((int)$row['user_id'] === (int)$_SESSION['user']['id']);
    }
    return false;
}
?>