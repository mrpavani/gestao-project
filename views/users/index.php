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

$users = $userController->index();

require_once '../../views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark fw-bold">
                    <i class="fas fa-user-shield"></i> Usuários
                </h1>
                <p class="text-muted">Gerenciamento de usuários do sistema</p>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Usuário
            </a>
        </div>
    </div>
</div>

<div class="row fade-in">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-list me-1"></i> Usuários Cadastrados
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-3">Usuário</th>
                            <th>Perfil</th>
                            <th>Criado em</th>
                            <th class="text-end pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-3 fw-500">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 text-primary"
                                            style="width:32px;height:32px;">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                        <?php echo htmlspecialchars($u['username']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3">
                                        <?php echo htmlspecialchars($u['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                                <td class="text-end pe-3">
                                    <form method="POST" action="" style="display:inline;"
                                        onsubmit="return confirm('Tem certeza que deseja remover este usuário?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt me-1"></i> Remover
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>