<?php
session_start();
require_once 'config/database.php';
require_once 'models/Expense.php';
require_once 'models/Project.php';

$database = new Database();
$db = $database->getConnection();

$expenseModel = new Expense($db);
$projectModel = new Project($db);

$filterType = isset($_GET['type']) ? $_GET['type'] : '';
$filterMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Ordenação
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'cost_date';
$sortOrder = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'asc' : 'desc';

// Validar colunas para evitar SQL Injection
$allowedSortColumns = ['cost_date', 'project_name', 'cost_type', 'amount'];
if (!in_array($sortColumn, $allowedSortColumns)) {
    $sortColumn = 'cost_date';
}

// Obter relatório de gastos
$query = "SELECT 
            e.*,
            p.project_name,
            p.total_budget
        FROM project_costs e
        JOIN projects p ON e.project_id = p.id
        WHERE 1=1";

if (!empty($filterType)) {
    $query .= " AND e.cost_type = '" . htmlspecialchars($filterType) . "'";
}

if (!empty($filterMonth)) {
    $query .= " AND DATE_FORMAT(e.cost_date, '%Y-%m') = '" . htmlspecialchars($filterMonth) . "'";
}

$query .= " ORDER BY " . $sortColumn . " " . strtoupper($sortOrder);

$stmt = $db->prepare($query);
$stmt->execute();
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totais por tipo
$expensesByType = [];
$totalAmount = 0;

foreach ($expenses as $expense) {
    $type = $expense['cost_type'];
    if (!isset($expensesByType[$type])) {
        $expensesByType[$type] = ['count' => 0, 'total' => 0];
    }
    $expensesByType[$type]['count']++;
    $expensesByType[$type]['total'] += $expense['amount'];
    $totalAmount += $expense['amount'];
}

// Calcular totais por projeto
$expensesByProject = [];
foreach ($expenses as $expense) {
    $project = $expense['project_name'];
    if (!isset($expensesByProject[$project])) {
        $expensesByProject[$project] = ['count' => 0, 'total' => 0, 'budget' => $expense['total_budget']];
    }
    $expensesByProject[$project]['count']++;
    $expensesByProject[$project]['total'] += $expense['amount'];
}

// Função helper para gerar link de ordenação
function sortLink($column, $label, $currentSort, $currentOrder)
{
    $newOrder = ($currentSort == $column && $currentOrder == 'desc') ? 'asc' : 'desc';
    $icon = '';
    if ($currentSort == $column) {
        $icon = $currentOrder == 'asc' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>';
    } else {
        $icon = ' <i class="fas fa-sort text-muted opacity-25"></i>';
    }

    // Manter filtros na URL
    $params = $_GET;
    $params['sort'] = $column;
    $params['order'] = $newOrder;
    $url = '?' . http_build_query($params);

    return '<a href="' . $url . '" class="text-dark text-decoration-none fw-bold">' . $label . $icon . '</a>';
}

require_once 'views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <h1 class="h3 mb-1 text-dark fw-bold">
            <i class="fas fa-chart-line"></i> Relatórios Financeiros
        </h1>
        <p class="text-muted">Análise de gastos e despesas por período</p>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4 border-0 shadow-sm fade-in">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-bold text-primary">Filtros de Pesquisa</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortColumn); ?>">
            <input type="hidden" name="order" value="<?php echo htmlspecialchars($sortOrder); ?>">

            <div class="col-md-4">
                <label for="month" class="form-label small text-muted text-uppercase fw-bold">Período</label>
                <input type="month" class="form-control" id="month" name="month"
                    value="<?php echo htmlspecialchars($filterMonth); ?>">
            </div>
            <div class="col-md-4">
                <label for="type" class="form-label small text-muted text-uppercase fw-bold">Tipo de Gasto</label>
                <select class="form-control" id="type" name="type">
                    <option value="">Todos</option>
                    <option value="material" <?php echo $filterType == 'material' ? 'selected' : ''; ?>>Material</option>
                    <option value="mao_de_obra" <?php echo $filterType == 'mao_de_obra' ? 'selected' : ''; ?>>Mão de Obra
                    </option>
                    <option value="software" <?php echo $filterType == 'software' ? 'selected' : ''; ?>>Software</option>
                    <option value="infraestrutura" <?php echo $filterType == 'infraestrutura' ? 'selected' : ''; ?>>
                        Infraestrutura</option>
                    <option value="outro" <?php echo $filterType == 'outro' ? 'selected' : ''; ?>>Outro</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filtrar Relatório
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resumo -->
<div class="row mb-4 fade-in">
    <div class="col-md-3 mb-3">
        <div class="stat-card total text-center">
            <h6>Total de Gastos</h6>
            <div class="value text-primary">R$ <?php echo number_format($totalAmount, 2, ',', '.'); ?></div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card budget text-center">
            <h6>Registros</h6>
            <div class="value text-info"><?php echo count($expenses); ?></div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card duration text-center">
            <h6>Tipos de Gasto</h6>
            <div class="value text-warning"><?php echo count($expensesByType); ?></div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card spent text-center">
            <h6>Projetos</h6>
            <div class="value text-success"><?php echo count($expensesByProject); ?></div>
        </div>
    </div>
</div>

<!-- Tabela Detalhada -->
<div class="card shadow-sm border-0 mb-4 fade-in">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Detalhamento de Gastos</h6>
        <span class="text-muted small">Clique nos cabeçalhos para ordenar</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3 py-3"><?php echo sortLink('cost_date', 'Data', $sortColumn, $sortOrder); ?></th>
                        <th class="py-3"><?php echo sortLink('project_name', 'Projeto', $sortColumn, $sortOrder); ?>
                        </th>
                        <th class="py-3"><?php echo sortLink('cost_type', 'Tipo', $sortColumn, $sortOrder); ?></th>
                        <th class="py-3">Descrição</th>
                        <th class="text-end pe-3 py-3">
                            <?php echo sortLink('amount', 'Valor', $sortColumn, $sortOrder); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($expenses) > 0): ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td class="ps-3"><?php echo date('d/m/Y', strtotime($expense['cost_date'])); ?></td>
                                <td class="fw-600"><?php echo htmlspecialchars($expense['project_name']); ?></td>
                                <td>
                                    <span
                                        class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3">
                                        <?php echo ucfirst(str_replace('_', ' ', $expense['cost_type'])); ?>
                                    </span>
                                </td>
                                <td class="text-muted small"><?php echo htmlspecialchars($expense['description'] ?? '—'); ?>
                                </td>
                                <td class="text-end pe-3">
                                    <strong>R$ <?php echo number_format($expense['amount'], 2, ',', '.'); ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Nenhum registro encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Resumo por Tipo - Tabela -->
<div class="row mt-4 fade-in">
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold">Por Tipo de Gasto</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-3">Tipo</th>
                                <th class="text-center py-3">Qtde</th>
                                <th class="text-end pe-3 py-3">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expensesByType as $type => $data): ?>
                                <tr>
                                    <td class="ps-3"><?php echo ucfirst(str_replace('_', ' ', $type)); ?></td>
                                    <td class="text-center"><?php echo $data['count']; ?></td>
                                    <td class="text-end pe-3">R$ <?php echo number_format($data['total'], 2, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold">Por Projeto</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-3">Projeto</th>
                                <th class="text-center py-3">Qtde</th>
                                <th class="text-end pe-3 py-3">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ordenar por total gasto decrescente para ser mais útil
                            uasort($expensesByProject, function ($a, $b) {
                                return $b['total'] <=> $a['total']; });
                            foreach ($expensesByProject as $project => $data):
                                ?>
                                <tr>
                                    <td class="ps-3 fw-500"><?php echo htmlspecialchars($project); ?></td>
                                    <td class="text-center"><?php echo $data['count']; ?></td>
                                    <td class="text-end pe-3">R$ <?php echo number_format($data['total'], 2, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>