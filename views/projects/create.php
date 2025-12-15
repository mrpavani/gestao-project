<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Project.php';
require_once '../../controllers/ProjectController.php';
require_once '../../controllers/CustomerController.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);
$customerController = new CustomerController($db);
$customers = $customerController->index();

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Processar credenciais
    $credentials = [];
    if (isset($_POST['credentials']) && is_array($_POST['credentials'])) {
        foreach ($_POST['credentials'] as $cred) {
            if (!empty($cred['access_type'])) {  // Apenas adicionar se houver tipo de acesso
                $credentials[] = $cred;
            }
        }
    }

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
        'status' => $_POST['status'],
        'credentials' => $credentials
    ];

    $result = $projectController->create($data);
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

require_once '../../views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1 text-dark fw-bold">
                    <i class="fas fa-folder-plus"></i> Novo Projeto
                </h1>
                <p class="text-muted">Cadastrar um novo projeto ou contrato</p>
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
                <h6 class="mb-0 fw-bold text-primary">Formulário de Cadastro</h6>
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
                            <input type="text" class="form-control" name="project_name" required
                                placeholder="Ex: E-commerce Loja X">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tipo do Projeto *</label>
                            <select class="form-select" id="project_type" name="project_type" required
                                onchange="toggleDates()">
                                <option value="">Selecione...</option>
                                <option value="desenvolvimento_sustentacao">Desenvolvimento + Sustentação</option>
                                <option value="desenvolvimento">Desenvolvimento</option>
                                <option value="sustentacao">Sustentação</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Descrição</label>
                            <textarea class="form-control" name="description" rows="3"
                                placeholder="Detalhes sobre o escopo do projeto..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Cliente</label>
                            <select class="form-select" name="customer_id">
                                <option value="">-- Selecionar cliente (opcional) --</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Início <span
                                            class="date-required-hint text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                    <div class="invalid-feedback" id="error_start_date"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Término <span
                                            class="date-required-hint text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
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
                                id="maintenance_start_date">
                            <div class="invalid-feedback" id="error_maintenance_start_date"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Fim Sustentação</label>
                            <input type="date" class="form-control" name="maintenance_end_date"
                                id="maintenance_end_date">
                            <div class="invalid-feedback" id="error_maintenance_end_date"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Valor Mensal (R$)</label>
                            <input type="text" class="form-control currency-input" name="maintenance_monthly_value"
                                id="maintenance_monthly_value" placeholder="R$ 0,00">
                            <div class="invalid-feedback" id="error_maintenance_monthly_value"></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Recebimento
                                Orçamento</label>
                            <input type="date" class="form-control" name="payment_received_date"
                                id="payment_received_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Orçamento Total (R$)
                                *</label>
                            <input type="text" class="form-control currency-input" name="total_budget" required
                                placeholder="R$ 0,00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Status *</label>
                            <select class="form-select" name="status" id="status_select" required
                                onchange="toggleDateRequirement()">
                                <option value="planejamento">Planejamento (Orçamento)</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="concluido">Concluído</option>
                                <option value="atrasado">Atrasado</option>
                                <option value="cancelado">Cancelado</option>
                                <option value="nao_aprovado">Não Aprovado</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="fas fa-key text-muted me-2"></i> Credenciais de Acesso <small
                                class="text-muted fw-normal">(Opcional)</small>
                        </h6>
                        <div id="credentials_container">
                            <div class="credential-item mb-3 p-3 bg-light rounded border border-light">
                                <div class="col-md-3">
                                    <label class="form-label small">Tipo de Acesso</label>
                                    <input type="text" class="form-control form-control-sm"
                                        name="credentials[0][access_type]" placeholder="FTP, SSH, Admin...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">URL/Host</label>
                                    <input type="text" class="form-control form-control-sm"
                                        name="credentials[0][server_url]" placeholder="ex: ftp.site.com">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Usuário</label>
                                    <input type="text" class="form-control form-control-sm"
                                        name="credentials[0][username]">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Senha</label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" class="form-control" name="credentials[0][password]">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <label class="form-label small">Observações</label>
                                    <textarea class="form-control form-control-sm" name="credentials[0][notes]" rows="1"
                                        placeholder="Detalhes adicionais..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCredential()">
                        <i class="fas fa-plus"></i> Adicionar Acesso
                    </button>
            </div>

            <!-- Campo oculto para referrer -->
            <input type="hidden" name="referrer"
                value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'projects.php'); ?>">

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="../../projects.php" class="btn btn-light border">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-1"></i> Salvar Projeto
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
    // Contador de credenciais
    let credentialCount = 1;

    // Adicionar novo campo de credencial
    function addCredential() {
        const container = document.getElementById('credentials_container');
        const newCredential = document.createElement('div');
        newCredential.className = 'credential-item mb-3 p-3 bg-light rounded border border-light fade-in';
        newCredential.innerHTML = `
            <div class="d-flex justify-content-between mb-2">
                <span class="badge bg-secondary">Acesso #${credentialCount + 1}</span>
                <button type="button" class="btn btn-xs btn-link text-danger p-0 text-decoration-none" onclick="removeCredential(this)">
                    <i class="fas fa-times"></i> Remover
                </button>
            </div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small">Tipo de Acesso</label>
                    <input type="text" class="form-control form-control-sm" name="credentials[${credentialCount}][access_type]" placeholder="FTP, SSH, Admin...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">URL/Host</label>
                    <input type="text" class="form-control form-control-sm" name="credentials[${credentialCount}][server_url]" placeholder="ex: ftp.site.com">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Usuário</label>
                    <input type="text" class="form-control form-control-sm" name="credentials[${credentialCount}][username]">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Senha</label>
                    <div class="input-group input-group-sm">
                        <input type="password" class="form-control" name="credentials[${credentialCount}][password]">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label small">Observações</label>
                    <textarea class="form-control form-control-sm" name="credentials[${credentialCount}][notes]" rows="1" placeholder="Detalhes adicionais..."></textarea>
                </div>
            </div>
        `;
        container.appendChild(newCredential);
        credentialCount++;
    }

    // Remover campo de credencial
    function removeCredential(button) {
        button.closest('.credential-item').remove();
    }

    // Validação de sustentação no cliente
    function monthsBetweenInclusive(startStr, endStr) {
        const start = new Date(startStr);
        const end = new Date(endStr);
        if (isNaN(start) || isNaN(end) || end < start) return -1;
        let months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
        // contar mês completo se dia do fim >= dia do início
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
        // Antes de enviar, transformar campos com máscara de moeda para valor numérico (ponto decimal)
        function unformatCurrency(masked) {
            if (!masked) return '';
            // remover tudo que não seja dígito
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

    // Máscaras de entrada
    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2);
        input.value = 'R$ ' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    const totalBudgetInput = document.querySelector('input[name="total_budget"]');
    const maintenanceMonthlyInput = document.querySelector('input[name="maintenance_monthly_value"]');

    if (totalBudgetInput) {
        totalBudgetInput.addEventListener('input', function () {
            formatCurrency(this);
        });
        totalBudgetInput.addEventListener('blur', function () {
            if (this.value && !this.value.includes('R$')) formatCurrency(this);
        });
    }

    if (maintenanceMonthlyInput) {
        maintenanceMonthlyInput.addEventListener('input', function () {
            formatCurrency(this);
        });
        maintenanceMonthlyInput.addEventListener('blur', function () {
            if (this.value && !this.value.includes('R$')) formatCurrency(this);
        });
    }

    // Exibir erros inline (campo específico)
    function clearFieldErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.innerText = '';
            el.style.display = 'none';
        });
        const serverMsg = document.getElementById('serverMessage');
        if (serverMsg) {
            serverMsg.style.display = 'none';
            serverMsg.innerHTML = '';
        }
    }

    function showFieldError(selectorOrEl, message) {
        clearFieldErrors();
        let el = null;
        if (typeof selectorOrEl === 'string') el = document.querySelector(selectorOrEl);
        else el = selectorOrEl;
        if (!el) {
            // fallback para mensagem no topo
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
            return;
        }
        el.classList.add('is-invalid');
        // procurar feedback associado por id convention
        const id = el.id || el.getAttribute('name');
        const feedback = document.getElementById('error_' + id) || el.nextElementSibling && el.nextElementSibling.classList.contains('invalid-feedback') ? el.nextElementSibling : null;
        if (feedback) {
            feedback.innerText = message;
            feedback.style.display = 'block';
        }
        el.focus();
        el.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    // Toggle password visibility
    function togglePassword(btn) {
        const input = btn.previousElementSibling;
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Toggle maintenance fields based on project type
    function toggleMaintenanceFields(select) {
        // Handle direct call or event
        if (!select) select = document.getElementById('project_type');
        if (!select) return;

        const maintenanceFieldsContainer = document.getElementById('maintenance-fields');
        // Logic for showing/hiding container if it exists (assuming it wraps the fields)
        if (maintenanceFieldsContainer) {
            if (select.value === 'sustentacao' || select.value === 'desenvolvimento_sustentacao') {
                maintenanceFieldsContainer.style.display = 'block';
            } else {
                maintenanceFieldsContainer.style.display = 'none';
            }
        }

        const projectType = select.value;
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
                    field.value = '';
                    field.classList.add('bg-light');
                } else {
                    field.classList.remove('bg-light');
                }
            }
        });

        toggleDates();
    }

    function toggleDates() {
        const select = document.getElementById('project_type');
        if (!select) return;

        const type = select.value;
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        if (type === 'sustentacao') {
            startDate.required = false;
            endDate.required = false;
            // Visual feedback (optional)
            document.querySelectorAll('.date-required-hint').forEach(el => el.style.display = 'none');
        } else {
            startDate.required = true;
            endDate.required = true;
            document.querySelectorAll('.date-required-hint').forEach(el => el.style.display = 'inline');
        }
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('project_type');
        if (select) {
            toggleMaintenanceFields(select);
        }
        setupAutoCalculation();
    });

    function setupAutoCalculation() {
        // Listeners for auto-calc
        const inputs = [
            document.getElementById('maintenance_start_date'),
            document.getElementById('maintenance_end_date'),
            document.getElementById('maintenance_monthly_value')
        ];

        inputs.forEach(input => {
            if (input) {
                input.addEventListener('change', calculateTotalBudget);
                input.addEventListener('blur', calculateTotalBudget);
            }
        });
    }

    function calculateTotalBudget() {
        const projectTypeSelect = document.getElementById('project_type');
        if (!projectTypeSelect) return;
        // Only for Sustentação (pure)
        if (projectTypeSelect.value !== 'sustentacao') return;

        const startStr = document.getElementById('maintenance_start_date').value;
        const endStr = document.getElementById('maintenance_end_date').value;
        const monthlyValStr = document.getElementById('maintenance_monthly_value').value;
        const totalBudgetInput = document.querySelector('input[name="total_budget"]');

        if (!startStr || !endStr || !monthlyValStr) return;

        // Calculate months
        const startDate = new Date(startStr);
        const endDate = new Date(endStr);

        if (startDate > endDate) return;

        let months = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth());

        if (endDate.getDate() >= startDate.getDate()) {
            months += 1;
        }

        if (months <= 0) months = 0;

        // Parse currency
        let cleanVal = monthlyValStr.replace(/[^\d,.-]/g, '').replace('.', '').replace(',', '.');
        let monthlyVal = parseFloat(cleanVal);

        if (isNaN(monthlyVal)) monthlyVal = 0;

        const total = monthlyVal * months;

        // Format back to PT-BR for display
        const formattedTotal = total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (totalBudgetInput)
            totalBudgetInput.value = 'R$ ' + formattedTotal;
    }

    // Override form submit to validate dates conditionally
    // Removing old submit handler if attached? 
    // Just attaching a new one that checks logic correctly.

    document.querySelector('form').addEventListener('submit', function (e) {
        // Check if dates are required based on TYPE, not status
        // Actually, the user requirements were about TYPE=sustentacao making dates optional.
        // Status checks might be old logic.

        const type = document.getElementById('project_type').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (type !== 'sustentacao') {
            if (!startDate) {
                showFieldError('#start_date', 'Data de início é obrigatória.');
                e.preventDefault();
                return false;
            }
            if (!endDate) {
                showFieldError('#end_date', 'Data de término é obrigatória.');
                e.preventDefault();
                return false;
            }
        }
    }, true); 
</script>

<?php require_once '../../views/layouts/footer.php'; ?>