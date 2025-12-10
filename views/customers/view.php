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
        $redirect_url = $_POST['referrer'] ?? $_SERVER['HTTP_REFERER'] ?? 'index.php';

        if (strpos($redirect_url, '://') === false) {
            $redirect_url = ltrim($redirect_url, '/');
        } else {
            $redirect_url = 'index.php';
        }

        header("Location: index.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente - MRP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../../includes/menu.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-edit"></i> Editar Cliente
                            </h4>
                            <a href="index.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Nome da Empresa ou Cliente *</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($customer->name); ?>" required autofocus>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($customer->phone ?? ''); ?>" placeholder="(11) 99999-9999">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($customer->email ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" name="observations" rows="4"><?php echo htmlspecialchars($customer->observations ?? ''); ?></textarea>
                            </div>

                            <!-- Campo oculto para referrer -->
                            <input type="hidden" name="referrer" value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'index.php'); ?>">

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="alert alert-info">
                            <strong>Informações do Cliente:</strong><br>
                            Criado em: <?php echo date('d/m/Y H:i', strtotime($customer->created_at)); ?><br>
                            Última atualização: <?php echo date('d/m/Y H:i', strtotime($customer->updated_at)); ?><br>
                            Projetos vinculados: <strong><?php echo $customerController->getProjectsCount($customer->id); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>