<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/UserController.php';

// Apenas usuários autenticados podem acessar
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$userController = new UserController($db);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    if ($username && $password) {
        $res = $userController->register($username, $password, $role);
        if ($res['success']) {
            header('Location: index.php?success=1');
            exit();
        } else {
            $message = 'Erro ao criar usuário';
        }
    } else {
        $message = 'Preencha usuário e senha.';
    }
}

require_once '../../views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark fw-bold">
                    <i class="fas fa-user-shield"></i> Novo Usuário
                </h1>
                <p class="text-muted">Cadastrar acesso ao sistema</p>
            </div>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center fade-in">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-primary">Credenciais de Acesso</h6>
            </div>
            <div class="card-body p-4">
                <?php if ($message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Usuário *</label>
                        <input type="text" name="username" class="form-control" required placeholder="login.usuario"
                            autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Senha *</label>
                        <input type="password" name="password" class="form-control" required placeholder="******">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Perfil de Acesso</label>
                        <select name="role" class="form-select">
                            <option value="user">Usuário Padrão</option>
                            <option value="admin">Administrador</option>
                        </select>
                        <div class="form-text">Administradores têm acesso total ao sistema.</div>
                    </div>

                    <div class="d-grid gap-2 pt-3 border-top mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Criar Usuário
                        </button>
                        <a href="index.php" class="btn btn-light border">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>