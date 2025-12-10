<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Customer.php';
require_once '../../controllers/CustomerController.php';
require_once '../../helpers.php';

$database = new Database();
$db = $database->getConnection();
$customerController = new CustomerController($db);

// Processar exclusão
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $result = $customerController->delete($_POST['id']);
    header('Location: index.php' . ($result['success'] ? '?success=1' : '?error=1'));
    exit();
}

$customers = $customerController->index();
$message = '';
$success = false;

if (isset($_GET['success'])) {
    $success = true;
    $message = 'Operação realizada com sucesso!';
}
if (isset($_GET['error'])) {
    $success = false;
    $message = 'Erro na operação.';
}

require_once '../../views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark fw-bold">
                    <i class="fas fa-users"></i> Clientes
                </h1>
                <p class="text-muted">Gerenciamento de clientes e empresas</p>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Cliente
            </a>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show shadow-sm border-0"
        role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row fade-in">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-list me-1"></i> Lista de Clientes
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-3">Nome</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Projetos</th>
                            <th>Data Cadastro</th>
                            <th class="text-end pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($customers) > 0): ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td class="ps-3 fw-500">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 text-primary"
                                                style="width:32px;height:32px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <?php echo htmlspecialchars($customer['name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($customer['email']): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($customer['email']); ?>"
                                                class="text-decoration-none">
                                                <?php echo htmlspecialchars($customer['email']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3">
                                            <?php echo $customerController->getProjectsCount($customer['id']); ?> projetos
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($customer['created_at'])); ?></td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="view.php?id=<?php echo $customer['id']; ?>"
                                                class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $customer['id']; ?>"
                                                class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Deletar"
                                                onclick="if(confirm('Tem certeza?')) { document.getElementById('delete-form-<?php echo $customer['id']; ?>').submit(); }">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-<?php echo $customer['id']; ?>" method="POST"
                                            style="display:none;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-3 opacity-25"></i>
                                    <p class="mb-0">Nenhum cliente cadastrado</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../views/layouts/footer.php'; ?>