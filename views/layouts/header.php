<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine base path for assets/links
$script_name = $_SERVER['SCRIPT_NAME']; // e.g., /dashboard.php or /views/projects/index.php
$in_views = strpos($script_name, '/views/') !== false;
$base_path = $in_views ? '../../' : './';

// Get current page for active state
$current_page = basename($script_name);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRP Gestão</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo $base_path; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="d-flex wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <a href="<?php echo $base_path; ?>dashboard.php" class="sidebar-brand">
                <i class="fas fa-layer-group"></i>
                <span>MRP Gestão</span>
            </a>
        </div>

        <ul class="list-unstyled components">
            <li>
                <a href="<?php echo $base_path; ?>dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $base_path; ?>projects.php" class="<?php echo ($current_page == 'projects.php' || strpos($script_name, '/projects/') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projetos</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $base_path; ?>views/customers/index.php" class="<?php echo (strpos($script_name, '/customers/') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Clientes</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $base_path; ?>reports.php" class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>Relatórios</span>
                </a>
            </li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <li>
                <a href="<?php echo $base_path; ?>views/users/index.php" class="<?php echo (strpos($script_name, '/users/') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Usuários</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="mt-4 border-top pt-3">
                <a href="<?php echo $base_path; ?>settings.php" class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                </a>
            </li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <li>
                <a href="<?php echo $base_path; ?>views/auth/logout.php" class="text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </li>
            <?php else: ?>
             <li>
                <a href="<?php echo $base_path; ?>views/auth/login.php">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <!-- Topbar mobile toggler could go here if needed, or inside content -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light d-md-none mb-4">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-primary">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
