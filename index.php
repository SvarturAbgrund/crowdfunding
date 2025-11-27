<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="hero">
    <h2>Impulsa el Talento Escolar</h2>
    <p>Apoya proyectos innovadores de estudiantes y sé parte del futuro de la educación. Cada aporte cuenta para hacer realidad una idea.</p>
    
    <div style="display: flex; gap: 15px; justify-content: center;">
        <a href="/crowdfunding1/campaigns/list.php" class="btn">Explorar Proyectos</a>
        <?php if(empty($_SESSION['user'])): ?>
            <a href="/crowdfunding1/auth/register.php" class="btn btn-secondary">Crear Cuenta</a>
        <?php elseif($_SESSION['user']['role'] === 'emprendedor'): ?>
            <a href="/crowdfunding1/campaigns/create.php" class="btn btn-secondary">Iniciar Campaña</a>
        <?php endif; ?>
    </div>
</section>

<div class="info-section" style="margin-top: 40px; text-align: center;">
    <h3 style="margin-bottom: 10px; color: var(--text-main);">¿Cómo funciona?</h3>
    <p style="color: var(--text-muted);">Publica tus ideas, alguien elige a cuáles apoyar y juntos logramos la meta.</p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>