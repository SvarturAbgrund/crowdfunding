<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../auth/helpers.php';
require_once __DIR__ . '/../models/Logger.php';
// Log page view
Logger::logPageView();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crowdfunding Escolar</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="/crowdfunding1/assets/style.css">
  <?php
  // Incluir CSS personalizado por usuario si existe
  if(!empty($_SESSION['user']) && is_numeric($_SESSION['user']['id'])){
      $userCss = __DIR__ . '/../assets/users/' . intval($_SESSION['user']['id']) . '.css';
      if(file_exists($userCss)){
          echo '<link rel="stylesheet" href="/crowdfunding1/assets/users/' . intval($_SESSION['user']['id']) . '.css">';
      }
  }

    // Incluir CSS específico de la página si existe (ruta espejo en assets/pages)
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePrefix = '/crowdfunding1';
    $rel = $script;
    if (!empty($script) && strpos($script, $basePrefix) === 0) {
      $rel = substr($script, strlen($basePrefix));
    }
    // Construir rutas locales y públicas
    $pageCssLocal = __DIR__ . '/../assets/pages' . $rel;
    $pageCssLocal = preg_replace('/\.php$/', '.css', $pageCssLocal);
    if (file_exists($pageCssLocal)) {
      $pageCssHref = $basePrefix . '/assets/pages' . $rel;
      $pageCssHref = preg_replace('/\.php$/', '.css', $pageCssHref);
      echo '<link rel="stylesheet" href="' . htmlspecialchars($pageCssHref) . '">';
    }
  ?>
</head>
<body>
  <header>
    <div class="container header-content">
      <a href="/crowdfunding1/" style="text-decoration:none;">
        <h1>Crowdfunding Escolar</h1>
      </a>
      
      <nav>
        <a href="/crowdfunding1/">Inicio</a>
        <a href="/crowdfunding1/campaigns/list.php">Ver Proyectos</a>

        <?php if(!empty($_SESSION['user'])): ?>
          <span class="user-welcome">Hola, <?=htmlspecialchars($_SESSION['user']['name'])?></span>
          
          <?php if($_SESSION['user']['role'] === 'emprendedor'): ?>
            <a href="/crowdfunding1/campaigns/dashboard.php">Dashboard</a>
          <?php endif; ?>

          <?php if($_SESSION['user']['role'] === 'admin'): ?>
            <a href="/crowdfunding1/admin/moderation.php">Admin</a>
          <?php endif; ?>

          <a href="/crowdfunding1/auth/logout.php" class="btn-nav">Salir</a>

        <?php else: ?>
          <a href="/crowdfunding1/auth/login.php">Login</a>
          <a href="/crowdfunding1/auth/register.php" class="btn-nav">Registrarse</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  
  <main> 
    <div class="container">