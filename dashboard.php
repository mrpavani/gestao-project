<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'models/Project.php';
require_once 'controllers/ProjectController.php';
require_once 'helpers.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);

// Obter dados
$projects = $projectController->index();
$summary = $projectController->getSummary();

// Mapear status para cores (para referência rápida se necessário, mas agora usamos CSS classes)
// As classes são .status-planejamento, .status-em_andamento, etc.

require_once 'views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <h1 class="h3 mb-1 text-dark fw-bold">Dashboard</h1>
        <p class="text-muted">Visão geral de seus projetos e finanças</p>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4 fade-in">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card total">
            <h6>Projetos em Planejamento</h6>
            <div class="value"><?php echo $summary['count_planejamento']; ?></div>
            <small class="text-muted">Propostas enviadas</small>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card budget">
            <h6>Orçamento (Propostas)</h6>
            <div class="value">R$ <?php echo format_currency($summary['total_orcamento_propostas']); ?></div>
            <small class="text-muted">Aguardando aprovação</small>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card spent">
            <h6>Recebíveis</h6>
            <div class="value">R$ <?php echo format_currency($summary['total_recebiveis']); ?></div>
            <small class="text-muted">Aprovados</small>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card duration">
            <h6>Sustentação Mensal</h6>
            <div class="value">R$ <?php echo format_currency($summary['total_sustentacao_mensal']); ?></div>
            <small class="text-muted">Recorrente</small>
        </div>
    </div>
</div>

<!-- Meus Projetos -->
<div class="row fade-in">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Projetos Recentes</h6>
                <a href="projects.php" class="btn btn-sm btn-primary">Ver Todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Projeto</th>
                                <th>Tipo</th>
                                <th>Orçamento</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($projects) > 0): ?>
                                <?php foreach (array_slice($projects, 0, 8) as $project): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-600 text-dark">
                                                <?php echo htmlspecialchars($project['project_name']); ?></div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-light text-dark border"><?php echo ucfirst($project['project_type']); ?></span>
                                        </td>
                                        <td>
                                            R$ <?php echo format_currency($project['total_budget']); ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $project['status']; ?>">
                                                <?php echo str_replace('_', ' ', ucfirst($project['status'])); ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="views/projects/view.php?id=<?php echo $project['id']; ?>"
                                                class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="views/projects/edit.php?id=<?php echo $project['id']; ?>"
                                                class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Nenhum projeto encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>