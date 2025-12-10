<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Project.php';
require_once '../../models/Expense.php';
require_once '../../controllers/ProjectController.php';
require_once '../../controllers/ExpenseController.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);
$expenseController = new ExpenseController($db);

$projectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
$project = null;
$expenses = [];
$totalExpenses = 0;
$message = '';
$success = false;

if ($projectId > 0) {
    $project = $projectController->show($projectId);
    $expenses = $expenseController->getByProjectId($projectId);
    $totalExpenses = $expenseController->getTotalByProjectId($projectId);
}

// Processar POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create') {
            $data = [
                'project_id' => $projectId,
                'cost_type' => $_POST['cost_type'],
                'description' => $_POST['description'],
                'amount' => $_POST['amount'],
                'cost_date' => $_POST['cost_date']
            ];
            $result = $expenseController->create($data);
            $success = $result['success'];
            $message = $result['message'];
            if ($success) {
                $expenses = $expenseController->getByProjectId($projectId);
                $totalExpenses = $expenseController->getTotalByProjectId($projectId);
            }
        } elseif ($_POST['action'] == 'delete') {
            $expenseId = intval($_POST['expense_id']);
            $result = $expenseController->delete($expenseId, $projectId);
            $success = $result['success'];
            $message = $result['message'];
            if ($success) {
                $expenses = $expenseController->getByProjectId($projectId);
                $totalExpenses = $expenseController->getTotalByProjectId($projectId);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Gastos - MRP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../../index.php"><i class="fas fa-chart-line"></i> MRP - Gestão de Projetos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../projects.php">Projetos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if ($projectId == 0): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Projeto não especificado. <a href="../../projects.php">Voltar para projetos</a>
            </div>
        <?php else: ?>
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2><i class="fas fa-money-bill"></i> Gerenciar Gastos - <?php echo htmlspecialchars($project->project_name); ?></h2>
                    <p class="text-muted">Registre todos os gastos associados ao projeto</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="../../projects.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $success ? 'check-circle' : 'times-circle'; ?>"></i> <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Card de Resumo -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Orçamento Total</h6>
                            <h3 class="text-primary">R$ <?php echo number_format($project->total_budget, 2, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Gastos Registrados</h6>
                            <h3 class="text-danger">R$ <?php echo number_format($totalExpenses, 2, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Saldo Disponível</h6>
                            <h3 class="text-success">R$ <?php echo number_format($project->total_budget - $totalExpenses, 2, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de Progresso -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="mb-2">Percentual do Orçamento Utilizado</h6>
                    <div class="progress" style="height: 25px;">
                        <?php
                        $percentage = $project->total_budget > 0 ? ($totalExpenses / $project->total_budget) * 100 : 0;
                        $barColor = $percentage <= 50 ? 'bg-success' : ($percentage <= 80 ? 'bg-warning' : 'bg-danger');
                        ?>
                        <div class="progress-bar <?php echo $barColor; ?>" role="progressbar"
                            style="width: <?php echo min($percentage, 100); ?>%"
                            aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                            <?php echo number_format($percentage, 1); ?>%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Novo Gasto -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Registrar Novo Gasto</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="create">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cost_type" class="form-label">Tipo de Gasto *</label>
                                <select class="form-control" id="cost_type" name="cost_type" required>
                                    <option value="">Selecione...</option>
                                    <option value="material">Material</option>
                                    <option value="mao_de_obra">Mão de Obra</option>
                                    <option value="software">Software/Licença</option>
                                    <option value="infraestrutura">Infraestrutura</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="amount" class="form-label">Valor (R$) *</label>
                                <input type="text" class="form-control currency-input" id="amount" name="amount" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cost_date" class="form-label">Data *</label>
                                <input type="date" class="form-control" id="cost_date" name="cost_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Detalhes do gasto..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar Gasto
                        </button>
                    </form>
                </div>
            </div>

            <!-- Listagem de Gastos -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Gastos Registrados</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($expenses)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhum gasto registrado ainda.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data</th>
                                        <th>Tipo</th>
                                        <th>Descrição</th>
                                        <th class="text-end">Valor</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expenses as $expense): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($expense['cost_date'])); ?></td>
                                            <td>
                                                <?php
                                                $types = [
                                                    'material' => 'Material',
                                                    'mao_de_obra' => 'Mão de Obra',
                                                    'software' => 'Software',
                                                    'infraestrutura' => 'Infraestrutura',
                                                    'outro' => 'Outro'
                                                ];
                                                echo $types[$expense['cost_type']] ?? $expense['cost_type'];
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($expense['description'] ?? '-'); ?></td>
                                            <td class="text-end">
                                                <strong>R$ <?php echo number_format($expense['amount'], 2, ',', '.'); ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja remover este gasto?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="expense_id" value="<?php echo $expense['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // máscara simples de moeda para o campo de gastos
        function formatCurrencyField(input) {
            let v = input.value.replace(/\D/g, '');
            if (v === '') {
                input.value = '';
                return;
            }
            v = (parseInt(v, 10) / 100).toFixed(2);
            input.value = 'R$ ' + v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function unformatCurrencyString(masked) {
            if (!masked) return 0;
            const digits = masked.replace(/\D/g, '');
            if (digits === '') return 0;
            return (parseInt(digits, 10) / 100).toFixed(2);
        }

        const expenseForm = document.querySelector('form[action=""]');
        const amountField = document.getElementById('amount');
        if (amountField) {
            amountField.addEventListener('input', function() {
                formatCurrencyField(this);
            });
            amountField.addEventListener('blur', function() {
                if (this.value && !this.value.includes('R$')) formatCurrencyField(this);
            });
        }

        if (expenseForm) {
            expenseForm.addEventListener('submit', function(e) {
                if (amountField && amountField.value) {
                    amountField.value = unformatCurrencyString(amountField.value);
                }
            });
        }
    </script>
</body>

</html>