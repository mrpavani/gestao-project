<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Customer.php';
require_once '../../controllers/CustomerController.php';

$database = new Database();
$db = $database->getConnection();
$customerController = new CustomerController($db);

$message = '';
$success = false;
$customer = null;

// Verificar se ID foi passado
if (!isset($_GET['id'])) {
    die('Cliente não encontrado.');
}

$customer_id = $_GET['id'];
$customer = $customerController->show($customer_id);

// Se não encontrou o cliente
if (!$customer || !isset($customer->id)) {
    die('Cliente não encontrado.');
}

// Processar POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'name' => $_POST['name'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email'],
        'observations' => $_POST['observations']
    ];

    $result = $customerController->update($customer_id, $data);
    $success = $result['success'];
    $message = $result['message'];

    if ($success) {
        header("Location: index.php?success=1");
        exit();
    }
}

require_once '../../views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark fw-bold">
                    <i class="fas fa-edit"></i> Editar Cliente
                </h1>
                <p class="text-muted">Atualizar dados cadastrais</p>
            </div>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center fade-in">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">#<?php echo $customer->id; ?> -
                    <?php echo htmlspecialchars($customer->name); ?></h6>
                <span class="badge bg-light text-dark border">
                    Desde <?php echo date('d/m/Y', strtotime($customer->created_at)); ?>
                </span>
            </div>
            <div class="card-body p-4">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show"
                        role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Nome da Empresa ou Cliente
                            *</label>
                        <input type="text" class="form-control" name="name"
                            value="<?php echo htmlspecialchars($customer->name); ?>" required autofocus>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Telefone</label>
                            <input type="tel" class="form-control" name="phone"
                                value="<?php echo htmlspecialchars($customer->phone ?? ''); ?>"
                                placeholder="(11) 99999-9999">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="<?php echo htmlspecialchars($customer->email ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Observações</label>
                        <textarea class="form-control" name="observations"
                            rows="4"><?php echo htmlspecialchars($customer->observations ?? ''); ?></textarea>
                    </div>

                    <!-- Campo oculto para referrer -->
                    <input type="hidden" name="referrer"
                        value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'index.php'); ?>">

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <a href="index.php" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Salvar Alterações
                        </button>
                    </div>
                </form>

                <div class="alert alert-light border mt-4 mb-0 text-muted small">
                    <i class="fas fa-info-circle me-1"></i> Projetos vinculados a este cliente:
                    <strong><?php echo $customerController->getProjectsCount($customer->id); ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // máscara de telefone
    function maskPhone(e) {
        const el = e.target;
        let v = el.value.replace(/\D/g, '');
        if (v.length > 11) v = v.slice(0, 11);
        if (v.length > 10) {
            // formato (99) 99999-9999
            el.value = '(' + v.slice(0, 2) + ') ' + v.slice(2, 7) + '-' + v.slice(7);
        } else if (v.length > 6) {
            // formato (99) 9999-9999
            el.value = '(' + v.slice(0, 2) + ') ' + v.slice(2, 6) + '-' + v.slice(6);
        } else if (v.length > 2) {
            el.value = '(' + v.slice(0, 2) + ') ' + v.slice(2);
        } else {
            el.value = v;
        }
    }

    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) phoneInput.addEventListener('input', maskPhone);
</script>

<?php require_once '../../views/layouts/footer.php'; ?>