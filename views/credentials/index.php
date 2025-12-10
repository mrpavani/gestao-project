<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Project.php';
require_once '../../models/Credential.php';
require_once '../../controllers/ProjectController.php';
require_once '../../controllers/CredentialController.php';

$database = new Database();
$db = $database->getConnection();
$projectController = new ProjectController($db);
$credentialController = new CredentialController($db);

$projectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
$project = null;
$credentials = [];
$message = '';
$success = false;

if ($projectId > 0) {
    $project = $projectController->show($projectId);
    $credentials = $credentialController->getByProjectId($projectId);
}

// Processar POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create') {
            $data = [
                'project_id' => $projectId,
                'access_type' => $_POST['access_type'],
                'server_url' => $_POST['server_url'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'port' => $_POST['port'] ?? '',
                'notes' => $_POST['notes'] ?? ''
            ];
            $result = $credentialController->create($data);
            $success = $result['success'];
            $message = $result['message'];
            if ($success) {
                $credentials = $credentialController->getByProjectId($projectId);
            }
        } elseif ($_POST['action'] == 'update') {
            $data = [
                'id' => intval($_POST['credential_id']),
                'access_type' => $_POST['access_type'],
                'server_url' => $_POST['server_url'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'port' => $_POST['port'] ?? '',
                'notes' => $_POST['notes'] ?? ''
            ];
            $result = $credentialController->update($data);
            $success = $result['success'];
            $message = $result['message'];
            if ($success) {
                $credentials = $credentialController->getByProjectId($projectId);
            }
        } elseif ($_POST['action'] == 'delete') {
            $credentialId = intval($_POST['credential_id']);
            $result = $credentialController->delete($credentialId);
            $success = $result['success'];
            $message = $result['message'];
            if ($success) {
                $credentials = $credentialController->getByProjectId($projectId);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciais de Acesso - MRP</title>
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
        <?php if ($projectId == 0): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Projeto não especificado. <a href="../../projects.php">Voltar para projetos</a>
            </div>
        <?php else: ?>
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2><i class="fas fa-key"></i> Credenciais de Acesso - <?php echo htmlspecialchars($project->project_name); ?></h2>
                    <p class="text-muted">Registre e gerencie os dados de acesso do projeto</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="../../projects.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $success ? 'check-circle' : 'times-circle'; ?>"></i> <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Formulário de Nova Credencial -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Registrar Nova Credencial</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="create">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="access_type" class="form-label">Tipo de Acesso *</label>
                                <select class="form-control" id="access_type" name="access_type" required>
                                    <option value="">Selecione...</option>
                                    <option value="FTP">FTP</option>
                                    <option value="SSH">SSH</option>
                                    <option value="Database">Database</option>
                                    <option value="cPanel">cPanel</option>
                                    <option value="Plesk">Plesk</option>
                                    <option value="AWS">AWS</option>
                                    <option value="Google Cloud">Google Cloud</option>
                                    <option value="Azure">Azure</option>
                                    <option value="CMS">CMS (WordPress, Joomla, etc)</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="server_url" class="form-label">Servidor/URL *</label>
                                <input type="text" class="form-control" id="server_url" name="server_url" placeholder="ftp.exemplo.com" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="port" class="form-label">Porta</label>
                                <input type="text" class="form-control" id="port" name="port" placeholder="21, 22, 3306, etc">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Usuário/Email *</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Senha *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Anotações</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Informações adicionais..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar Credencial
                        </button>
                    </form>
                </div>
            </div>

            <!-- Listagem de Credenciais -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Credenciais Registradas</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($credentials)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhuma credencial registrada ainda.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Servidor</th>
                                        <th>Usuário</th>
                                        <th>Porta</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($credentials as $cred): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($cred['access_type']); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($cred['server_url']); ?></td>
                                            <td><?php echo htmlspecialchars($cred['username']); ?></td>
                                            <td><?php echo !empty($cred['port']) ? htmlspecialchars($cred['port']) : '—'; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?php echo $cred['id']; ?>">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <form method="POST" style="display:inline;"
                                                    onsubmit="return confirm('Tem certeza que deseja remover?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="credential_id" value="<?php echo $cred['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Remover
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal de Edição -->
                                        <div class="modal fade" id="editModal<?php echo $cred['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Editar Credencial</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="update">
                                                            <input type="hidden" name="credential_id" value="<?php echo $cred['id']; ?>">

                                                            <div class="mb-3">
                                                                <label class="form-label">Tipo de Acesso</label>
                                                                <select class="form-control" name="access_type" required>
                                                                    <option value="FTP" <?php echo $cred['access_type'] == 'FTP' ? 'selected' : ''; ?>>FTP</option>
                                                                    <option value="SSH" <?php echo $cred['access_type'] == 'SSH' ? 'selected' : ''; ?>>SSH</option>
                                                                    <option value="Database" <?php echo $cred['access_type'] == 'Database' ? 'selected' : ''; ?>>Database</option>
                                                                    <option value="cPanel" <?php echo $cred['access_type'] == 'cPanel' ? 'selected' : ''; ?>>cPanel</option>
                                                                    <option value="Plesk" <?php echo $cred['access_type'] == 'Plesk' ? 'selected' : ''; ?>>Plesk</option>
                                                                    <option value="AWS" <?php echo $cred['access_type'] == 'AWS' ? 'selected' : ''; ?>>AWS</option>
                                                                    <option value="Google Cloud" <?php echo $cred['access_type'] == 'Google Cloud' ? 'selected' : ''; ?>>Google Cloud</option>
                                                                    <option value="Azure" <?php echo $cred['access_type'] == 'Azure' ? 'selected' : ''; ?>>Azure</option>
                                                                    <option value="CMS" <?php echo $cred['access_type'] == 'CMS' ? 'selected' : ''; ?>>CMS</option>
                                                                    <option value="Admin" <?php echo $cred['access_type'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                                                    <option value="Outro" <?php echo $cred['access_type'] == 'Outro' ? 'selected' : ''; ?>>Outro</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Servidor/URL</label>
                                                                <input type="text" class="form-control" name="server_url" value="<?php echo htmlspecialchars($cred['server_url']); ?>" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Porta</label>
                                                                <input type="text" class="form-control" name="port" value="<?php echo htmlspecialchars($cred['port']); ?>">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Usuário/Email</label>
                                                                <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($cred['username']); ?>" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Senha</label>
                                                                <input type="password" class="form-control" name="password" value="<?php echo htmlspecialchars($cred['password']); ?>" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Anotações</label>
                                                                <textarea class="form-control" name="notes" rows="3"><?php echo htmlspecialchars($cred['notes']); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>