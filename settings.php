<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Carregar configurações (simuladas)
$settings = [
    'company_name' => 'Sua Empresa',
    'company_email' => 'contato@empresa.com',
    'company_phone' => '(11) 9999-9999',
    'currency' => 'BRL',
    'date_format' => 'dd/mm/yyyy',
    'items_per_page' => '10',
    'theme' => 'light'
];

// Processar POST
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'update_settings') {
        // Aqui seria atualizado as configurações no banco
        $settings = [
            'company_name' => $_POST['company_name'] ?? $settings['company_name'],
            'company_email' => $_POST['company_email'] ?? $settings['company_email'],
            'company_phone' => $_POST['company_phone'] ?? $settings['company_phone'],
            'currency' => $_POST['currency'] ?? $settings['currency'],
            'date_format' => $_POST['date_format'] ?? $settings['date_format'],
            'items_per_page' => $_POST['items_per_page'] ?? $settings['items_per_page'],
            'theme' => $_POST['theme'] ?? $settings['theme']
        ];

        $message = 'Configurações salvas com sucesso!';
        $success = true;
    }
}

require_once 'views/layouts/header.php';
?>

<div class="row mb-4 fade-in">
    <div class="col-12">
        <h1 class="h3 mb-1 text-dark fw-bold">
            <i class="fas fa-cog"></i> Configurações
        </h1>
        <p class="text-muted">Personalize as configurações do seu sistema</p>
    </div>
</div>

<div class="row fade-in">
    <div class="col-lg-8">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show shadow-sm border-0"
                role="alert">
                <i class="fas fa-<?php echo $success ? 'check-circle' : 'times-circle'; ?>"></i> <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Abas de Configuração -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pt-3 px-3">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="empresa-tab" data-bs-toggle="tab" data-bs-target="#empresa"
                            type="button" role="tab">
                            <i class="fas fa-building me-1"></i> Empresa
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sistema-tab" data-bs-toggle="tab" data-bs-target="#sistema"
                            type="button" role="tab">
                            <i class="fas fa-sliders-h me-1"></i> Sistema
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sobre-tab" data-bs-toggle="tab" data-bs-target="#sobre"
                            type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i> Sobre
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <!-- Conteúdo das Abas -->
                <div class="tab-content">
                    <!-- Aba Empresa -->
                    <div class="tab-pane fade show active" id="empresa" role="tabpanel">
                        <h6 class="mb-4 fw-bold text-primary">Informações da Empresa</h6>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_settings">

                            <div class="mb-3">
                                <label for="company_name"
                                    class="form-label small text-muted text-uppercase fw-bold">Nome da Empresa</label>
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                    value="<?php echo htmlspecialchars($settings['company_name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="company_email"
                                    class="form-label small text-muted text-uppercase fw-bold">Email</label>
                                <input type="email" class="form-control" id="company_email" name="company_email"
                                    value="<?php echo htmlspecialchars($settings['company_email']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="company_phone"
                                    class="form-label small text-muted text-uppercase fw-bold">Telefone</label>
                                <input type="tel" class="form-control" id="company_phone" name="company_phone"
                                    value="<?php echo htmlspecialchars($settings['company_phone']); ?>">
                            </div>

                            <div class="mb-4">
                                <label for="currency"
                                    class="form-label small text-muted text-uppercase fw-bold">Moeda</label>
                                <select class="form-select" id="currency" name="currency">
                                    <option value="BRL" <?php echo $settings['currency'] == 'BRL' ? 'selected' : ''; ?>>
                                        Real Brasileiro (R$)</option>
                                    <option value="USD" <?php echo $settings['currency'] == 'USD' ? 'selected' : ''; ?>>
                                        Dólar Americano ($)</option>
                                    <option value="EUR" <?php echo $settings['currency'] == 'EUR' ? 'selected' : ''; ?>>
                                        Euro (€)</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Aba Sistema -->
                    <div class="tab-pane fade" id="sistema" role="tabpanel">
                        <h6 class="mb-4 fw-bold text-primary">Preferências do Sistema</h6>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_settings">

                            <div class="mb-3">
                                <label for="date_format"
                                    class="form-label small text-muted text-uppercase fw-bold">Formato de Data</label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="dd/mm/yyyy" <?php echo $settings['date_format'] == 'dd/mm/yyyy' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                                    <option value="mm/dd/yyyy" <?php echo $settings['date_format'] == 'mm/dd/yyyy' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                                    <option value="yyyy-mm-dd" <?php echo $settings['date_format'] == 'yyyy-mm-dd' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="items_per_page"
                                    class="form-label small text-muted text-uppercase fw-bold">Itens por Página</label>
                                <input type="number" class="form-control" id="items_per_page" name="items_per_page"
                                    value="<?php echo htmlspecialchars($settings['items_per_page']); ?>" min="5"
                                    max="100">
                            </div>

                            <div class="mb-4">
                                <label for="theme"
                                    class="form-label small text-muted text-uppercase fw-bold">Tema</label>
                                <select class="form-select" id="theme" name="theme">
                                    <option value="light" <?php echo $settings['theme'] == 'light' ? 'selected' : ''; ?>>
                                        Claro (Light)</option>
                                    <option value="dark" <?php echo $settings['theme'] == 'dark' ? 'selected' : ''; ?>>
                                        Escuro (Dark)</option>
                                </select>
                            </div>

                            <div class="alert alert-light border d-flex align-items-center mb-4">
                                <i class="fas fa-info-circle text-info me-3 fa-2x"></i>
                                <div>
                                    Estas configurações afetam a forma como os dados são exibidos em todo o sistema.
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Configurações
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Aba Sobre -->
                    <div class="tab-pane fade" id="sobre" role="tabpanel">
                        <div class="text-center mb-5">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-layer-group fa-2x"></i>
                            </div>
                            <h4 class="fw-bold">MRP Gestão</h4>
                            <p class="text-muted">Versão 1.0.0</p>
                            <span
                                class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3">Sistema
                                Ativo</span>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold border-bottom pb-2">Sobre</h6>
                                <p class="text-muted small">
                                    Sistema completo para gestão de projetos, controle financeiro e administração de
                                    clientes.
                                    Desenvolvido para otimizar fluxos de trabalho e aumentar a produtividade.
                                </p>
                                <p class="text-muted small">Criado em: <strong>08/12/2025</strong></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold border-bottom pb-2">Funcionalidades</h6>
                                <ul class="list-unstyled small text-muted">
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Gestão de Projetos
                                    </li>
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Controle Financeiro
                                    </li>
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Relatórios
                                        Analíticos</li>
                                    <li><i class="fas fa-check text-success me-2"></i> Gestão de Usuários</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-light border mt-4 text-center">
                            <small>Para suporte técnico, contate:
                                <strong><?php echo htmlspecialchars($settings['company_email']); ?></strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Card Lateral -->
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="card shadow-sm border-0 mb-3 bg-primary text-white overflow-hidden">
            <div class="card-body p-4 position-relative">
                <i class="fas fa-cog position-absolute"
                    style="font-size: 10rem; right: -2rem; bottom: -2rem; opacity: 0.1;"></i>
                <h5 class="fw-bold mb-3">Dica Rápida</h5>
                <p class="opacity-75 mb-0">
                    Mantenha as informações da sua empresa sempre atualizadas para garantir que os relatórios e
                    documentos gerados contenham os dados corretos.
                </p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Segurança</h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0 bg-light rounded p-2 text-dark">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="ms-3">
                        <small class="d-block text-muted">Último backup</small>
                        <strong>Hoje, 10:30</strong>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-light rounded p-2 text-dark">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="ms-3">
                        <small class="d-block text-muted">Status do Sistema</small>
                        <strong class="text-success">Seguro e Protegido</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>