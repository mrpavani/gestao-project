<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Project.php';
require_once '../../controllers/ProjectController.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'project_name' => $_POST['project_name'],
        'project_type' => $_POST['project_type'],
        'description' => $_POST['description'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'total_budget' => $_POST['total_budget'],
        'status' => $_POST['status'],
    ];

    $result = $projectController->create($data);
    $success = $result['success'];
    $message = $result['message'];

    if ($success) {
        header("Location: ../../index.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Projeto - MRP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Cadastrar Novo Projeto</h4>
                        <a href="../../index.php" class="btn btn-sm btn-outline-secondary float-end">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nome do Projeto *</label>
                                    <input type="text" class="form-control" name="project_name" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo do Projeto *</label>
                                    <select class="form-select" name="project_type" required>
                                        <option value="">Selecione...</option>
                                        <option value="site_manutencao">Desenvolver Site e Sustentação</option>
                                        <option value="site_apenas">Desenvolver Apenas o Site</option>
                                        <option value="manutencao">Sustentação</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Data de Início *</label>
                                    <input type="date" class="form-control" name="start_date" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Data de Término *</label>
                                    <input type="date" class="form-control" name="end_date" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Orçamento Total (R$) *</label>
                                    <input type="text" class="form-control currency-input" name="total_budget" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status *</label>
                                    <select class="form-select" name="status" required>
                                        <option value="planejamento">Planejamento</option>
                                        <option value="em_andamento">Em Andamento</option>
                                        <option value="concluido">Concluído</option>
                                        <option value="atrasado">Atrasado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Projeto
                                </button>
                                <a href="../../index.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // máscara de moeda simples para total_budget
        function formatCurrencyInputField(input) {
            let v = input.value.replace(/\D/g, '');
            if (v === '') {
                input.value = '';
                return;
            }
            v = (parseInt(v, 10) / 100).toFixed(2);
            input.value = 'R$ ' + v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function unformatCurrencyString(masked) {
            if (!masked) return '';
            const digits = masked.replace(/\D/g, '');
            if (digits === '') return '';
            return (parseInt(digits, 10) / 100).toFixed(2);
        }

        const activityForm = document.querySelector('form[action=""]');
        const tb = document.querySelector('input[name="total_budget"]');
        if (tb) {
            tb.addEventListener('input', function() {
                formatCurrencyInputField(this);
            });
            tb.addEventListener('blur', function() {
                if (this.value && !this.value.includes('R$')) formatCurrencyInputField(this);
            });
        }
        if (activityForm) {
            activityForm.addEventListener('submit', function(e) {
                if (tb && tb.value) tb.value = unformatCurrencyString(tb.value);
            });
        }
    </script>
</body>

</html>