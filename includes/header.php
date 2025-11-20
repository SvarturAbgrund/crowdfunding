<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../auth/helpers.php';
require_once __DIR__ . '/../models/Logger.php';
// Log page view
Logger::logPageView();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Crowdfunding Escolar</title>
  <link rel="stylesheet" href="/crowdfunding1/assets/style.css">
  <?php
  // Incluir CSS personalizado por usuario si existe
  if(!empty($_SESSION['user']) && is_numeric($_SESSION['user']['id'])){
      $userCss = __DIR__ . '/../assets/users/' . intval($_SESSION['user']['id']) . '.css';
      if(file_exists($userCss)){
          echo '<link rel="stylesheet" href="/crowdfunding1/assets/users/' . intval($_SESSION['user']['id']) . '.css">';
      }
  }
  ?>
</head>
<body>
  <header>
    <h1>Crowdfunding Escolar</h1>
    <nav>
      <a href="/crowdfunding1/">Inicio</a>
      <?php if(!empty($_SESSION['user'])): ?>
        <span>Bienvenido, <?=htmlspecialchars($_SESSION['user']['name'])?></span>
        <a href="/crowdfunding1/auth/logout.php">Logout</a>
        <?php if($_SESSION['user']['role'] === 'emprendedor'): ?>
          <a href="/crowdfunding1/campaigns/create.php">Crear Campaña</a>
          <a href="/crowdfunding1/campaigns/dashboard.php">Dashboard</a>
        <?php endif; ?>
        <?php if($_SESSION['user']['role'] === 'admin'): ?>
          <a href="/crowdfunding1/admin/moderation.php">Moderación</a>
          <a href="/crowdfunding1/admin/users.php">Usuarios</a>
          <a href="/crowdfunding1/admin/categories.php">Categorías</a>
          <a href="/crowdfunding1/admin/messages.php">Mensajes</a>
        <?php endif; ?>
      <?php else: ?>
        <a href="/crowdfunding1/auth/login.php">Login</a>
        <a href="/crowdfunding1/auth/register.php">Registrarse</a>
      <?php endif; ?>
      <a href="/crowdfunding1/campaigns/list.php">Ver Proyectos</a>
      <a href="/crowdfunding1/contact.php">Contactar Admin</a>
    </nav>
  </header>
  <main>
