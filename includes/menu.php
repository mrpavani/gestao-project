<?php

/**
 * Navbar compartilhada - Reutilizável em todas as páginas
 */

// Determinar página atual para destaque no menu
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

function is_active($page)
{
    global $current_page;
    return $current_page === $page ? 'active' : '';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../dashboard.php' : './dashboard.php'; ?>">
            <i class="fas fa-chart-line"></i> MRP Project Manager
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active('dashboard.php'); ?>" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../dashboard.php' : './dashboard.php'; ?>">
                        <i class="fas fa-home"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo is_active('projects.php'); ?>" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../projects.php' : './projects.php'; ?>">
                        <i class="fas fa-project-diagram"></i> Projetos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../views/customers/index.php' : './views/customers/index.php'; ?>">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../views/users/index.php' : './views/users/index.php'; ?>">
                            <i class="fas fa-user-shield"></i> Usuários
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../reports.php' : './reports.php'; ?>">
                        <i class="fas fa-chart-bar"></i> Relatórios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../settings.php' : './settings.php'; ?>">
                        <i class="fas fa-cog"></i> Configurações
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../views/auth/logout.php' : './views/auth/logout.php'; ?>">
                            <i class="fas fa-sign-out-alt"></i> Sair (<?php echo htmlspecialchars($_SESSION['username']); ?>)
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/views/') !== false) ? '../../views/auth/login.php' : './views/auth/login.php'; ?>">
                            <i class="fas fa-sign-in-alt"></i> Entrar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>