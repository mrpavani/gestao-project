<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Project.php';
require_once '../../models/Expense.php';
require_once '../../controllers/ProjectController.php';
require_once '../../controllers/DocumentController.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);
$documentController = new DocumentController($db);

$projectId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$project = null;
$totalExpenses = 0;
$message = '';

if ($projectId > 0) {
    // Handle Document Actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
        $result = $documentController->upload($projectId, $_FILES['document']);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }

    if (isset($_GET['delete_doc'])) {
        $result = $documentController->delete($_GET['delete_doc']);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
        // Remove query param to avoid re-action on refresh (basic way)
        // Ideally redirect, but keeping simple for now
    }

    $project = $projectController->show($projectId);
    $documents = $documentController->getByProject($projectId);

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
            <a class="navbar-brand" href="../../index.php"><i class="fas fa-chart-line"></i> MRP - Gestão de
                Projetos</a>
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
                        <p><strong>Descrição:</strong><br><?php echo htmlspecialchars($project->description ?? 'Sem descrição'); ?>
                        </p>
                        <p><strong>Status:</strong><br>
                            <span class="badge bg-<?php
                            $statusColors = ['planejamento' => 'info', 'em_andamento' => 'warning', 'concluido' => 'success', 'atrasado' => 'danger', 'cancelado' => 'secondary'];
                            echo $statusColors[$project->status] ?? 'secondary';
                            ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $project->status)); ?>
                            </span>
                        </p>
                        <p><strong>Data
                                Criação:</strong><br><?php echo date('d/m/Y', strtotime($project->created_at)); ?></p>
                        <p><strong>Última
                                Atualização:</strong><br><?php echo date('d/m/Y H:i', strtotime($project->updated_at)); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-calendar"></i> Datas do Projeto</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Data de
                                Início:</strong><br><?php echo date('d/m/Y', strtotime($project->start_date)); ?></p>
                        <p><strong>Data de
                                Término:</strong><br><?php echo date('d/m/Y', strtotime($project->end_date)); ?></p>
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
                                <h3 class="text-primary">R$
                                    <?php echo number_format($project->total_budget, 2, ',', '.'); ?>
                                </h3>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Gastos Registrados</h6>
                                <h3 class="text-danger">R$ <?php echo number_format($totalExpenses, 2, ',', '.'); ?>
                                </h3>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Saldo Disponível</h6>
                                <h3 class="text-success">R$
                                    <?php echo number_format($project->total_budget - $totalExpenses, 2, ',', '.'); ?>
                                </h3>
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
                            <a href="../expenses/index.php?project_id=<?php echo $project->id; ?>"
                                class="btn btn-primary">
                                <i class="fas fa-money-bill-wave"></i> Gerenciar Gastos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-file-pdf"></i> Documentos do Projeto</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Form de Upload -->
                        <form action="" method="POST" enctype="multipart/form-data"
                            class="mb-4 p-3 bg-light rounded border">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <label for="document" class="form-label fw-bold">Novo Documento (PDF)</label>
                                    <input type="file" class="form-control" id="document" name="document"
                                        accept="application/pdf" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-upload"></i> Enviar
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Lista de Documentos -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome do Arquivo</th>
                                        <th>Data de Envio</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $hasDocs = false;
                                    while ($doc = $documents->fetch(PDO::FETCH_ASSOC)):
                                        $hasDocs = true;
                                        ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                <?php echo htmlspecialchars($doc['file_name']); ?>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($doc['uploaded_at'])); ?></td>
                                            <td class="text-end">
                                                <a href="../../<?php echo $doc['file_path']; ?>" target="_blank"
                                                    class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="?id=<?php echo $project->id; ?>&delete_doc=<?php echo $doc['id']; ?>"
                                                    onclick="return confirm('Tem certeza que deseja excluir este documento?')"
                                                    class="btn btn-sm btn-outline-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>

                                    <?php if (!$hasDocs): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                <small>Nenhum documento anexado.</small>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>