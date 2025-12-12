<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Project.php';
require_once '../../controllers/ProjectController.php';
require_once '../../controllers/CustomerController.php';
require_once '../../helpers.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);
$customerController = new CustomerController($db);
$customers = $customerController->index();

$message = '';
$success = false;
$project = null;

// Verificar se ID foi passado
if (!isset($_GET['id'])) {
    die('Projeto não encontrado.');
}

$project_id = $_GET['id'];
$projectController->show($project_id);
$project = $projectController->show($project_id);

// Se não encontrou o projeto
if (!$project || !isset($project->id)) {
    die('Projeto não encontrado.');
}

// Processar POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'project_name' => $_POST['project_name'],
        'project_type' => $_POST['project_type'],
        'description' => $_POST['description'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'customer_id' => $_POST['customer_id'] ?? null,
        'maintenance_start_date' => $_POST['maintenance_start_date'] ?? null,
        'maintenance_end_date' => $_POST['maintenance_end_date'] ?? null,
        'maintenance_monthly_value' => $_POST['maintenance_monthly_value'] ?? null,
        'payment_received_date' => $_POST['payment_received_date'] ?? null,
        'total_budget' => $_POST['total_budget'],
        'status' => $_POST['status']
    ];

    $result = $projectController->update($project_id, $data);
    $success = $result['success'];
    $message = $result['message'];

    if ($success) {
        // Redirecionar para página anterior ou projects.php
        $redirect_url = $_POST['referrer'] ?? $_SERVER['HTTP_REFERER'] ?? '../../projects.php';

        // Sanitizar URL para segurança
        if (strpos($redirect_url, '://') === false) {
            // URL relativa - aceitar
            $redirect_url = '../../' . ltrim($redirect_url, '/');
        } else {
            // URL absoluta - validar se é do mesmo domínio
            $redirect_url = '../../projects.php';
        }

        header("Location: " . $redirect_url . (strpos($redirect_url, '?') === false ? '?success=1' : '&success=1'));
        exit();
    }
}

// Mapear tipos de projeto
$projectTypes = [
    'desenvolvimento_sustentacao' => 'Desenvolvimento + Sustentação',
    'desenvolvimento' => 'Desenvolvimento',
    'sustentacao' => 'Sustentação'
];

$statusOptions = [
    'planejamento' => 'Planejamento (Orçamento)',
    'em_andamento' => 'Em Andamento',
    'concluido' => 'Concluído',
    'atrasado' => 'Atrasado',
    'cancelado' => 'Cancelado',
    'nao_aprovado' => 'Não Aprovado'
];

require_once '../../views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark fw-bold">
                    <i class="fas fa-edit"></i> Editar Projeto
                </h1>
                <p class="text-muted">Atualizar informações do projeto</p>
            </div>
            <a href="../../projects.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center fade-in">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">#<?php echo $project->id; ?> -
                        <?php echo htmlspecialchars($project->project_name); ?>
                    </h6>
                    <span class="badge bg-light text-dark border">
                        <?php echo ($project->created_at ? 'Criado em ' . date('d/m/Y', strtotime($project->created_at)) : ''); ?>
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if ($message): ?>
                    <div id="serverMessage"
                        class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show"
                        role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php else: ?>
                    <div id="serverMessage" style="display:none;"></div>
                <?php endif; ?>

                <form method="POST" action="" class="needs-validation">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nome do Projeto *</label>
                            <input type="text" class="form-control" name="project_name"
                                value="<?php echo htmlspecialchars($project->project_name); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tipo do Projeto *</label>
                            <select class="form-select" name="project_type" id="project_type_select" required
                                onchange="toggleMaintenanceFields()">
                                <option value="">Selecione...</option>
                                <?php foreach ($projectTypes as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $project->project_type === $key ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Descrição</label>
                            <textarea class="form-control" name="description"
                                rows="3"><?php echo htmlspecialchars($project->description); ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Cliente</label>
                            <select class="form-select" name="customer_id">
                                <option value="">-- Selecionar cliente (opcional) --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo ($project->customer_id == $c['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Início <span
                                            class="date-required-hint text-danger">*</span></label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        value="<?php echo $project->start_date; ?>">
                                    <div class="invalid-feedback" id="error_start_date"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Término <span
                                            class="date-required-hint text-danger">*</span></label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        value="<?php echo $project->end_date; ?>">
                                    <div class="invalid-feedback" id="error_end_date"></div>
                                </div>
                            </div>
                            <small class="text-muted" id="date_hint" style="display:none;"><i
                                    class="fas fa-info-circle"></i> Datas são opcionais para Planejamento (orçamento
                                pendente)</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-tools text-muted me-2"></i>Sustentação e Valores
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Início Sustentação</label>
                            <input type="date" class="form-control" name="maintenance_start_date"
                                id="maintenance_start_date" value="<?php echo $project->maintenance_start_date; ?>">
                            <div class="invalid-feedback" id="error_maintenance_start_date"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Fim Sustentação</label>
                            <input type="date" class="form-control" name="maintenance_end_date"
                                id="maintenance_end_date" value="<?php echo $project->maintenance_end_date; ?>">
                            <div class="invalid-feedback" id="error_maintenance_end_date"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Valor Mensal (R$)</label>
                            <input type="text" class="form-control currency-input" name="maintenance_monthly_value"
                                id="maintenance_monthly_value"
                                value="<?php echo $project->maintenance_monthly_value; ?>">
                            <div class="invalid-feedback" id="error_maintenance_monthly_value"></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Recebimento
                                Orçamento</label>
                            <input type="date" class="form-control" name="payment_received_date"
                                id="payment_received_date" value="<?php echo $project->payment_received_date; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Orçamento Total (R$)
                                *</label>
                            <input type="text" class="form-control currency-input" name="total_budget"
                                value="<?php echo $project->total_budget; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Status *</label>
                            <select class="form-select" name="status" id="status_select" required
                                onchange="toggleDateRequirement()">
                                <?php foreach ($statusOptions as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $project->status === $key ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <?php
                    // Contador de credenciais
                    echo '<script>let credentialCount = (typeof initialCredentialCount !== \'undefined\') ? initialCredentialCount : 1;</script>';
                    $credentials = $projectController->getCredentials($project->id);
                    ?>
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="fas fa-key text-muted me-2"></i> Credenciais de Acesso <small
                                class="text-muted fw-normal">(Opcional)</small>
                        </h6>
                        <div id="credentials_container">
                            <?php
                            $credCount = 0;
                            if (!empty($credentials)):
                                foreach ($credentials as $cred):
                                    ?>
                                    <div class="credential-item mb-3 p-3 bg-light rounded border border-light">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="badge bg-secondary">Acesso #<?php echo $credCount + 1; ?></span>
                                            <button type="button"
                                                class="btn btn-xs btn-link text-danger p-0 text-decoration-none"
                                                onclick="removeCredential(this)">
                                                <i class="fas fa-times"></i> Remover
                                            </button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <label class="form-label small">Tipo de Acesso</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="credentials[<?php echo $credCount; ?>][access_type]"
                                                    value="<?php echo htmlspecialchars($cred['access_type']); ?>"
                                                    placeholder="FTP, SSH, Admin...">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">URL/Host</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="credentials[<?php echo $credCount; ?>][server_url]"
                                                    value="<?php echo htmlspecialchars($cred['server_url']); ?>"
                                                    placeholder="ex: ftp.site.com">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Usuário</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="credentials[<?php echo $credCount; ?>][username]"
                                                    value="<?php echo htmlspecialchars($cred['username']); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">Senha</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="password" class="form-control"
                                                        name="credentials[<?php echo $credCount; ?>][password]"
                                                        value="<?php echo htmlspecialchars($cred['password']); ?>">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePassword(this)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <label class="form-label small">Observações</label>
                                                <textarea class="form-control form-control-sm"
                                                    name="credentials[<?php echo $credCount; ?>][notes]" rows="1"
                                                    placeholder="Detalhes adicionais..."><?php echo htmlspecialchars($cred['notes']); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $credCount++;
                                endforeach;
                            endif;
                            ?>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCredential()">
                            <i class="fas fa-plus"></i> Adicionar Acesso
                        </button>
                    </div>

                    <!-- Passar contagem para JS -->
                    <script>let initialCredentialCount = <?php echo $credCount; ?>;</script>

                    <!-- Campo oculto para referrer -->
                    <input type="hidden" name="referrer"
                        value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'projects.php'); ?>">

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <a href="../../projects.php" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Validação de sustentação no cliente
    function monthsBetweenInclusive(startStr, endStr) {
        const start = new Date(startStr);
        const end = new Date(endStr);
        if (isNaN(start) || isNaN(end) || end < start) return -1;
        let months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
        if (end.getDate() >= start.getDate()) months += 1;
        return months;
    }

    document.querySelector('form').addEventListener('submit', function (e) {
        const mStart = document.getElementById('maintenance_start_date').value;
        const mEnd = document.getElementById('maintenance_end_date').value;
        const mValue = document.getElementById('maintenance_monthly_value').value;
        const endDate = document.querySelector('input[name="end_date"]').value;

        if (mStart) {
            if (!endDate) {
                showFieldError('input[name="end_date"]', 'Informe a data de término do projeto antes de cadastrar sustentação.');
                e.preventDefault();
                return false;
            }
            if (new Date(mStart) <= new Date(endDate)) {
                showFieldError('#maintenance_start_date', 'A sustentação deve começar após o término do projeto.');
                e.preventDefault();
                return false;
            }
            if (!mValue || parseFloat(mValue) <= 0) {
                showFieldError('#maintenance_monthly_value', 'Informe um valor mensal válido para a sustentação.');
                e.preventDefault();
                return false;
            }
            if (mEnd) {
                const months = monthsBetweenInclusive(mStart, mEnd);
                if (months < 6 || months > 12) {
                    showFieldError('#maintenance_end_date', 'A sustentação deve ter duração mínima de 6 meses e máxima de 12 meses.');
                    e.preventDefault();
                    return false;
                }
            }
        }
        // converter campos de moeda (mascarados) para número antes do envio
        function unformatCurrency(masked) {
            if (!masked) return '';
            const digits = masked.replace(/\D/g, '');
            if (digits === '') return '';
            const num = (parseInt(digits, 10) / 100).toFixed(2);
            return num;
        }

        const tb = document.querySelector('input[name="total_budget"]');
        const mm = document.querySelector('input[name="maintenance_monthly_value"]');
        if (tb && tb.value) tb.value = unformatCurrency(tb.value);
        if (mm && mm.value) mm.value = unformatCurrency(mm.value);

        // limpar mensagem anterior
        const serverMsg = document.getElementById('serverMessage');
        if (serverMsg) {
            serverMsg.style.display = 'none';
            serverMsg.innerHTML = '';
        }

        return true;
    });

    // máscara de moeda para edição (mesma lógica do create)
    function formatCurrencyInput(e) {
        const el = e.target;
        el.value = formatCurrency(el.value);
    }

    function formatCurrency(value) {
        if (!value) return '';
        // remover não dígitos
        let digits = String(value).replace(/\D/g, '');
        if (digits === '') return '';
        while (digits.length < 3) {
            digits = '0' + digits;
        }
        const cents = digits.slice(-2);
        let integer = digits.slice(0, -2);
        integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'R$ ' + integer + ',' + cents;
    }

    const tbField = document.querySelector('input[name="total_budget"]');
    const mmField = document.querySelector('input[name="maintenance_monthly_value"]');
    if (tbField) tbField.addEventListener('input', formatCurrencyInput);
    if (mmField) mmField.addEventListener('input', formatCurrencyInput);

    // Format initial values if they exist (and aren't already formatted)
    // Note: PHP might return 5000.00, we want to show R$ 5.000,00
    // But formatCurrency expects simple digits or existing R$.
    // If PHP echoes '5000.00', we should convert to R$ 5.000,00 on load.
    // However, the helpers.php might have `formatCurrency` function too.
    // Simplifying: we'll trigger the input event if value exists to format it.

    if (tbField && tbField.value && !tbField.value.includes('R$')) {
        // Assume format is 0.00 from DB
        let val = tbField.value.replace('.', '').replace(',', ''); // 500000
        // Wait, DB is decimal(10,2)? 5000.00 -> 500000 for our logic
        // Our logic expects digits as cents. 
        // 5000.00 -> "500000" -> R$ 5.000,00
        // So we remove dots and interpret as cents
        // Need to handle if DB returns 5000 (no decimals)
        if (tbField.value.indexOf('.') !== -1) {
            const parts = tbField.value.split('.');
            if (parts[1].length == 1) parts[1] += '0';
            val = parts[0] + parts[1];
        } else {
            val = tbField.value + '00';
        }
        tbField.value = formatCurrency(val);
    }

    if (mmField && mmField.value && !mmField.value.includes('R$')) {
        let val = mmField.value.replace('.', ''); // simple cleanup
        if (mmField.value.indexOf('.') !== -1) {
            const parts = mmField.value.split('.');
            if (parts[1].length == 1) parts[1] += '0';
            val = parts[0] + parts[1];
        } else {
            val = mmField.value + '00';
        }
        mmField.value = formatCurrency(val);
    }


    // Exibir erros inline
    function showInlineError(message) {
        const serverMsg = document.getElementById('serverMessage');
        if (serverMsg) {
            serverMsg.className = 'alert alert-danger';
            serverMsg.style.display = 'block';
            serverMsg.innerText = message;
            window.scrollTo({
                top: serverMsg.getBoundingClientRect().top + window.scrollY - 20,
                behavior: 'smooth'
            });
        } else {
            alert(message);
        }
    }

    function showFieldError(selectorOrEl, message) {
        // Reuse logic from create
        let el = null;
        if (typeof selectorOrEl === 'string') el = document.querySelector(selectorOrEl);
        else el = selectorOrEl;
        if (el) {
            el.classList.add('is-invalid');
            const id = el.id || el.getAttribute('name');
            const feedback = document.getElementById('error_' + id);
            if (feedback) {
                feedback.innerText = message;
                feedback.style.display = 'block';
            }
        } else {
            alert(message);
        }
    }

    // Toggle date requirement based on status
    function toggleDateRequirement() {
        const status = document.getElementById('status_select').value;
        const dateHints = document.querySelectorAll('.date-required-hint');
        const dateHint = document.getElementById('date_hint');
        const isPlanejamento = (status === 'planejamento' || status === 'nao_aprovado');

        dateHints.forEach(h => h.style.display = isPlanejamento ? 'none' : 'inline');
        if (dateHint) dateHint.style.display = isPlanejamento ? 'block' : 'none';
    }

    // Toggle maintenance fields based on project type
    function toggleMaintenanceFields() {
        const projectTypeSelect = document.getElementById('project_type_select');
        if (!projectTypeSelect) return;

        const projectType = projectTypeSelect.value;
        const maintenanceFields = [
            document.getElementById('maintenance_start_date'),
            document.getElementById('maintenance_end_date'),
            document.getElementById('maintenance_monthly_value')
        ];

        // Disable for "desenvolvimento" type, enable for others
        const shouldDisable = (projectType === 'desenvolvimento');

        maintenanceFields.forEach(field => {
            if (field) {
                field.disabled = shouldDisable;
                if (shouldDisable) {
                    // Only clear if desired, or keep existing value but disabled
                    // field.value = ''; 
                    field.classList.add('bg-light');
                } else {
                    field.classList.remove('bg-light');
                }
            }
        });
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', function () {
        toggleDateRequirement();
        toggleMaintenanceFields();
    });

    // Date validation on submit
    document.querySelector('form').addEventListener('submit', function (e) {
        const status = document.getElementById('status_select').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (status !== 'planejamento' && status !== 'nao_aprovado') {
            if (!startDate) {
                showFieldError('#start_date', 'Data de início é obrigatória para este status.');
                e.preventDefault();
                return false;
            }
            if (!endDate) {
                showFieldError('#end_date', 'Data de término é obrigatória para este status.');
                e.preventDefault();
                return false;
            }
        }
    }, true);
</script>

<?php require_once '../../views/layouts/footer.php'; ?>