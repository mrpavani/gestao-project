<?php
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'models/Project.php';
require_once 'controllers/ProjectController.php';
require_once 'helpers.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);

// Mês selecionado (padrão: mês atual)
$selectedYear = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');
$selectedMonth = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');

// Obter dados do dashboard para o mês selecionado
$dashboardData = getDashboardSummaryByMonth($db, $selectedYear, $selectedMonth);
$projects = $projectController->index();

// Função para calcular resumo mensal
function getDashboardSummaryByMonth($db, $year, $month)
{
    $startOfMonth = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
    $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

    // Orçamentos pendentes (planejamento) - sem data definida
    $sqlOrcamentos = "SELECT COUNT(*) as count, COALESCE(SUM(total_budget), 0) as total
                      FROM projects 
                      WHERE status = 'planejamento'";
    $stmt = $db->prepare($sqlOrcamentos);
    $stmt->execute();
    $orcamentos = $stmt->fetch(PDO::FETCH_ASSOC);

    // Receita de Desenvolvimento (projetos em andamento no mês selecionado)
    // Projeto está em andamento se: start_date <= fim do mês E end_date >= início do mês
    // E status IN ('em_andamento', 'atrasado')
    $sqlDev = "SELECT COUNT(*) as count, COALESCE(SUM(total_budget), 0) as total
               FROM projects 
               WHERE status IN ('em_andamento', 'atrasado')
               AND project_type IN ('desenvolvimento', 'desenvolvimento_sustentacao')
               AND start_date IS NOT NULL AND end_date IS NOT NULL
               AND start_date <= :endOfMonth AND end_date >= :startOfMonth";
    $stmt = $db->prepare($sqlDev);
    $stmt->bindParam(':startOfMonth', $startOfMonth);
    $stmt->bindParam(':endOfMonth', $endOfMonth);
    $stmt->execute();
    $desenvolvimento = $stmt->fetch(PDO::FETCH_ASSOC);

    // Receita de Sustentação (sustentações ativas no mês selecionado)
    // Sustentação ativa se: maintenance_start_date <= fim do mês E maintenance_end_date >= início do mês
    // E status NOT IN ('cancelado', 'nao_aprovado')
    $sqlSust = "SELECT COUNT(*) as count, COALESCE(SUM(maintenance_monthly_value), 0) as total
                FROM projects 
                WHERE status NOT IN ('cancelado', 'nao_aprovado', 'planejamento')
                AND project_type IN ('sustentacao', 'desenvolvimento_sustentacao')
                AND maintenance_start_date IS NOT NULL AND maintenance_end_date IS NOT NULL
                AND maintenance_start_date <= :endOfMonth AND maintenance_end_date >= :startOfMonth";
    $stmt = $db->prepare($sqlSust);
    $stmt->bindParam(':startOfMonth', $startOfMonth);
    $stmt->bindParam(':endOfMonth', $endOfMonth);
    $stmt->execute();
    $sustentacao = $stmt->fetch(PDO::FETCH_ASSOC);

    return [
        'orcamentos_count' => (int) $orcamentos['count'],
        'orcamentos_total' => (float) $orcamentos['total'],
        'desenvolvimento_count' => (int) $desenvolvimento['count'],
        'desenvolvimento_total' => (float) $desenvolvimento['total'],
        'sustentacao_count' => (int) $sustentacao['count'],
        'sustentacao_total' => (float) $sustentacao['total']
    ];
}

// Meses para select
$meses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];

require_once 'views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark fw-bold">Dashboard</h1>
                <p class="text-muted">Visão geral de seus projetos e finanças</p>
            </div>
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="month" class="form-select form-select-sm" style="width: 130px;"
                    onchange="this.form.submit()">
                    <?php foreach ($meses as $num => $nome): ?>
                        <option value="<?php echo $num; ?>" <?php echo $selectedMonth == $num ? 'selected' : ''; ?>>
                            <?php echo $nome; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="year" class="form-select form-select-sm" style="width: 90px;"
                    onchange="this.form.submit()">
                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo $selectedYear == $y ? 'selected' : ''; ?>>
                            <?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>
    </div>
</div>

<!-- Estatísticas Mensais -->
<div class="row mb-4 fade-in">
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="stat-card total">
            <h6><i class="fas fa-file-invoice text-muted me-2"></i>Orçamentos Pendentes</h6>
            <div class="value">R$ <?php echo format_currency($dashboardData['orcamentos_total']); ?></div>
            <small class="text-muted"><?php echo $dashboardData['orcamentos_count']; ?> propostas aguardando
                aprovação</small>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="stat-card budget">
            <h6><i class="fas fa-code text-muted me-2"></i>Receita Desenvolvimento</h6>
            <div class="value">R$ <?php echo format_currency($dashboardData['desenvolvimento_total']); ?></div>
            <small class="text-muted"><?php echo $dashboardData['desenvolvimento_count']; ?> projetos ativos em
                <?php echo $meses[$selectedMonth]; ?></small>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="stat-card spent">
            <h6><i class="fas fa-tools text-muted me-2"></i>Receita Sustentação</h6>
            <div class="value">R$ <?php echo format_currency($dashboardData['sustentacao_total']); ?></div>
            <small class="text-muted"><?php echo $dashboardData['sustentacao_count']; ?> sustentações ativas em
                <?php echo $meses[$selectedMonth]; ?></small>
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
                            <?php
                            $projectTypes = [
                                'desenvolvimento_sustentacao' => 'Dev + Sustentação',
                                'desenvolvimento' => 'Desenvolvimento',
                                'sustentacao' => 'Sustentação'
                            ];
                            $statusLabels = [
                                'planejamento' => 'Orçamento',
                                'em_andamento' => 'Em Andamento',
                                'concluido' => 'Concluído',
                                'atrasado' => 'Atrasado',
                                'cancelado' => 'Cancelado',
                                'nao_aprovado' => 'Não Aprovado'
                            ];
                            if (count($projects) > 0): ?>
                                <?php foreach (array_slice($projects, 0, 8) as $project): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-600 text-dark">
                                                <?php echo htmlspecialchars($project['project_name']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-light text-dark border"><?php echo $projectTypes[$project['project_type']] ?? ucfirst($project['project_type']); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            // Mostrar valor conforme tipo e status
                                            if (in_array($project['status'], ['cancelado', 'nao_aprovado', 'concluido'])) {
                                                echo '<span class="text-muted">-</span>';
                                            } else {
                                                echo 'R$ ' . format_currency($project['total_budget']);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $project['status']; ?>">
                                                <?php echo $statusLabels[$project['status']] ?? str_replace('_', ' ', ucfirst($project['status'])); ?>
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