<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'models/Project.php';
require_once 'controllers/ProjectController.php';
require_once 'helpers.php';

$database = new Database();
$db = $database->getConnection();

$projectController = new ProjectController($db);

// Obter todos os projetos
$projects = $projectController->index();
$summary = $projectController->getSummary();
$statusReport = $projectController->getStatusReport();

// Mapear tipos de projeto
$projectTypes = [
    'site_manutencao' => 'Desenvolver Site e Manutenção',
    'site_apenas' => 'Desenvolver Apenas o Site',
    'manutencao' => 'Manutenção'
];

// Mapear status
$statusLabels = [
    'planejamento' => 'Planejamento',
    'em_andamento' => 'Em Andamento',
    'concluido' => 'Concluído',
    'atrasado' => 'Atrasado',
    'cancelado' => 'Cancelado'
];

require_once 'views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 text-dark fw-bold">
                    Projetos
                </h1>
                <p class="text-muted">Gerencie seus projetos, atividades e despesas</p>
            </div>
            <div>
                <a href="views/projects/create.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Projeto
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Resumo em Cards -->
<div class="row mb-4 fade-in">
    <!-- Total Geral -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card total">
            <h6>Total Geral</h6>
            <div class="value"><?php echo $summary['total_projects']; ?></div>
            <small class="text-muted">Projetos cadastrados</small>
        </div>
    </div>

    <!-- Orçamento Desenvolvimento/Site -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card budget">
            <h6>Orçamento Projetos</h6>
            <div class="value">R$
                <?php echo format_currency(isset($summary['total_budget_projects']) ? $summary['total_budget_projects'] : 0); ?>
            </div>
            <small class="text-muted"><?php echo isset($summary['count_projects']) ? $summary['count_projects'] : 0; ?>
                projetos</small>
        </div>
    </div>

    <!-- Orçamento Manutenção -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card duration">
            <h6>Orçamento Manutenção</h6>
            <div class="value">R$
                <?php echo format_currency(isset($summary['total_budget_maintenance']) ? $summary['total_budget_maintenance'] : 0); ?>
            </div>
            <small
                class="text-muted"><?php echo isset($summary['count_maintenance']) ? $summary['count_maintenance'] : 0; ?>
                manutenções</small>
        </div>
    </div>

    <!-- Total Gasto -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card spent">
            <h6>Total Gasto</h6>
            <div class="value">R$
                <?php echo format_currency(isset($summary['total_spent']) ? $summary['total_spent'] : 0); ?></div>
            <small class="text-muted">Todos os projetos</small>
        </div>
    </div>
</div>

<!-- Status Distribution -->
<div class="row mb-4 fade-in">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-chart-pie"></i> Distribuição de Status
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($statusReport as $status): ?>
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <div class="text-center p-3 rounded bg-light">
                                <span class="d-block mb-2 status-badge status-<?php echo $status['status']; ?>">
                                    <?php echo htmlspecialchars($statusLabels[$status['status']]); ?>
                                </span>
                                <h4 class="mb-0 fw-bold"><?php echo $status['count']; ?></h4>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Projetos -->
<div class="row fade-in">
    <div class="col-12">
        <h5 class="mb-3 fw-bold text-dark">
            <i class="fas fa-list-ul"></i> Meus Projetos
        </h5>

        <?php if (empty($projects)): ?>
            <div class="card shadow-sm border-0">
                <div class="card-body py-5 text-center">
                    <i class="fas fa-folder-open text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                    <h5 class="mt-3 text-muted">Nenhum projeto cadastrado</h5>
                    <p class="text-muted mb-3">Comece criando seu primeiro projeto</p>
                    <a href="views/projects/create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Criar Primeiro Projeto
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($projects as $project): ?>
                    <?php
                    $today = new DateTime();
                    $endDate = new DateTime($project['end_date']);
                    $daysLeft = $today->diff($endDate)->days;
                    $isLate = $today > $endDate && $project['status'] != 'concluido';
                    ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-sm transition-hover">
                            <!-- Header Card -->
                            <div
                                class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">
                                        <?php echo htmlspecialchars($project['project_name']); ?>
                                    </h6>
                                    <span class="badge bg-light text-secondary border">
                                        <?php echo htmlspecialchars($projectTypes[$project['project_type']]); ?>
                                    </span>
                                </div>
                                <span class="status-badge status-<?php echo $project['status']; ?>">
                                    <?php echo htmlspecialchars($statusLabels[$project['status']]); ?>
                                </span>
                            </div>

                            <!-- Body Card -->
                            <div class="card-body">
                                <p class="card-text text-muted small mb-3">
                                    <?php echo htmlspecialchars(strlen($project['description']) > 80 ? substr($project['description'], 0, 80) . '...' : $project['description']); ?>
                                </p>

                                <!-- Info Cards -->
                                <div class="bg-light p-2 rounded mb-3 d-flex justify-content-between">
                                    <div class="text-center w-50 border-end">
                                        <small class="text-muted d-block">Orçamento</small>
                                        <strong class="text-success">R$
                                            <?php echo format_currency($project['total_budget']); ?></strong>
                                    </div>
                                    <div class="text-center w-50">
                                        <small class="text-muted d-block">Gasto</small>
                                        <strong class="text-danger">R$
                                            <?php echo format_currency($project['current_spent']); ?></strong>
                                    </div>
                                </div>

                                <!-- Datas -->
                                <div class="small text-muted mb-2 d-flex justify-content-between">
                                    <span><i class="far fa-calendar-check me-1"></i> Início:
                                        <?php echo date('d/m/y', strtotime($project['start_date'])); ?></span>
                                    <span><i class="far fa-flag me-1"></i> Fim:
                                        <?php echo date('d/m/y', strtotime($project['end_date'])); ?></span>
                                </div>

                                <?php if ($isLate): ?>
                                    <div class="alert alert-danger py-1 px-2 small mb-0 text-center">
                                        <i class="fas fa-exclamation-circle"></i> Atrasado
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Footer com Botões -->
                            <div class="card-footer bg-white border-top p-3">
                                <div class="d-grid gap-2 d-flex">
                                    <a href="views/projects/view.php?id=<?php echo $project['id']; ?>"
                                        class="btn btn-outline-primary btn-sm flex-grow-1">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="views/projects/edit.php?id=<?php echo $project['id']; ?>"
                                        class="btn btn-outline-secondary btn-sm flex-grow-1">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>