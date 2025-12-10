<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/UserController.php';

$database = new Database();
$db = $database->getConnection();
$userController = new UserController($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $user = $userController->authenticate($username, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: ../../dashboard.php');
        exit();
    } else {
        $message = 'Usuário ou senha inválidos.';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - MRP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="login-wrapper d-flex align-items-center justify-content-center min-vh-100">
        <div class="login-card shadow-lg row g-0 bg-white rounded overflow-hidden" style="max-width:900px;width:100%">
            <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center"
                style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); color: white;">
                <div class="text-center px-4 py-5">
                    <!-- Inline SVG logo -->
                    <div class="mb-4">
                        <svg width="86" height="86" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="1" y="1" width="22" height="22" rx="4" fill="rgba(255,255,255,0.06)" />
                            <path d="M4 12h16" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"
                                stroke-linecap="round" />
                            <path d="M12 4v16" stroke="rgba(255,255,255,0.7)" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </div>
                    <h3 class="fw-bold">MRP Gestão</h3>
                    <p class="opacity-75 mb-0">Organize projetos, orçamento e sustentação em um só lugar.</p>
                </div>
            </div>
            <div class="col-md-6 col-12 p-4">
                <div class="p-3">
                    <div class="text-center mb-4">
                        <h4 class="mb-0">Entrar</h4>
                        <small class="text-muted">Acesse sua conta</small>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="mb-3">
                        <div class="mb-3">
                            <label class="form-label visually-hidden">Usuário</label>
                            <input type="text" name="username" class="form-control form-control-lg"
                                placeholder="Usuário ou e-mail" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label visually-hidden">Senha</label>
                            <input type="password" name="password" class="form-control form-control-lg"
                                placeholder="Senha" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="remember_me"
                                    name="remember_me">
                                <label class="form-check-label small" for="remember_me">Lembrar-me</label>
                            </div>
                            <div>
                                <a href="#" class="small">Esqueceu a senha?</a>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg">Entrar</button>
                        </div>
                    </form>

                    <div class="text-center small text-muted">
                        <span>Contato do administrador para criar usuários internos.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>