<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Project.php';
require_once '../../models/Expense.php';
require_once '../../controllers/ProjectController.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);

$projectId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$project = null;
$totalExpenses = 0;

if ($projectId > 0) {
    $project = $projectController->show($projectId);

    // Calcular total de gastos
    $expenseModel = new Expense($db);
    $expenseModel->project_id = $projectId;
    $totalExpenses = $expenseModel->getTotal();
} else {
    header('Location: ../../projects.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project->project_name); ?> - MRP</title>
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

    <div class="container mt-5 mb-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="fas fa-folder"></i> <?php echo htmlspecialchars($project->project_name); ?></h2>
                <p class="text-muted">Tipo: <strong><?php echo $project->project_type; ?></strong></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="edit.php?id=<?php echo $project->id; ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="../../projects.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <!-- Informações do Projeto -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informações Gerais</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Descrição:</strong><br><?php echo htmlspecialchars($project->description ?? 'Sem descrição'); ?></p>
                        <p><strong>Status:</strong><br>
                            <span class="badge bg-<?php
                                                    $statusColors = ['planejamento' => 'info', 'em_andamento' => 'warning', 'concluido' => 'success', 'atrasado' => 'danger', 'cancelado' => 'secondary'];
                                                    echo $statusColors[$project->status] ?? 'secondary';
                                                    ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $project->status)); ?>
                            </span>
                        </p>
                        <p><strong>Data Criação:</strong><br><?php echo date('d/m/Y', strtotime($project->created_at)); ?></p>
                        <p><strong>Última Atualização:</strong><br><?php echo date('d/m/Y H:i', strtotime($project->updated_at)); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-calendar"></i> Datas do Projeto</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Data de Início:</strong><br><?php echo date('d/m/Y', strtotime($project->start_date)); ?></p>
                        <p><strong>Data de Término:</strong><br><?php echo date('d/m/Y', strtotime($project->end_date)); ?></p>
                        <p><strong>Duração Prevista:</strong><br>
                            <?php
                            $start = new DateTime($project->start_date);
                            $end = new DateTime($project->end_date);
                            $interval = $start->diff($end);
                            echo $interval->days . ' dias';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações Financeiras -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-money-bill"></i> Informações Financeiras</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h6 class="text-muted">Orçamento Total</h6>
                                <h3 class="text-primary">R$ <?php echo number_format($project->total_budget, 2, ',', '.'); ?></h3>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Gastos Registrados</h6>
                                <h3 class="text-danger">R$ <?php echo number_format($totalExpenses, 2, ',', '.'); ?></h3>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Saldo Disponível</h6>
                                <h3 class="text-success">R$ <?php echo number_format($project->total_budget - $totalExpenses, 2, ',', '.'); ?></h3>
                            </div>
                        </div>

                        <!-- Barra de Progresso -->
                        <div class="mt-4">
                            <h6 class="mb-2">Utilização do Orçamento</h6>
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

                        <div class="mt-3">
                            <a href="../expenses/index.php?project_id=<?php echo $project->id; ?>" class="btn btn-primary">
                                <i class="fas fa-money-bill-wave"></i> Gerenciar Gastos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>