# 💻 GUIA DE USO - NOVAS FUNCIONALIDADES E DESIGN

## 9 de dezembro de 2025

---

## 🎨 Usando os Novos Botões

### Botão Primário (Ação Principal)
```html
<!-- Grande -->
<a href="views/projects/create.php" class="btn btn-primary btn-lg">
    <i class="fas fa-plus"></i> Novo Projeto
</a>

<!-- Padrão -->
<button type="submit" class="btn btn-primary">
    <i class="fas fa-save"></i> Salvar
</button>

<!-- Pequeno -->
<a href="#" class="btn btn-primary btn-sm">
    <i class="fas fa-edit"></i> Editar
</a>
```

### Botão Outline (Ação Secundária)
```html
<a href="view.php?id=1" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-eye"></i> Ver
</a>

<a href="edit.php?id=1" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-edit"></i> Editar
</a>
```

### Botão Sucesso (Confirmar)
```html
<button type="submit" class="btn btn-success">
    <i class="fas fa-check"></i> Confirmar
</button>
```

### Botão Perigo (Deletar)
```html
<a href="#" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza?')">
    <i class="fas fa-trash"></i> Deletar
</a>
```

### Botão Aviso (Alerta)
```html
<button class="btn btn-warning">
    <i class="fas fa-exclamation"></i> Atenção
</button>
```

---

## 📱 Container com Limite de 1300px

### Correto
```html
<!-- Em qualquer página -->
<div class="container mt-5 mb-5">
    <!-- Conteúdo automaticamente limitado a 1300px -->
</div>
```

### Com Linhas e Colunas
```html
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-6">
            <!-- Conteúdo da coluna -->
        </div>
        <div class="col-md-6">
            <!-- Conteúdo da coluna -->
        </div>
    </div>
</div>
```

### Grid de Cards
```html
<div class="container mt-5 mb-5">
    <div class="row g-4">  <!-- g-4 para gap/espaçamento -->
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <!-- Card content -->
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <!-- Card content -->
            </div>
        </div>
    </div>
</div>
```

---

## 🎯 Criando um Card de Projeto

```html
<div class="card h-100 shadow-sm border-0 transition">
    <!-- Header -->
    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-start">
        <div>
            <h6 class="mb-2 fw-bold">Nome do Projeto</h6>
            <span class="badge bg-primary">Tipo de Projeto</span>
        </div>
        <span class="badge status-em_andamento">Em Andamento</span>
    </div>

    <!-- Body -->
    <div class="card-body flex-grow-1">
        <p class="card-text text-muted small mb-3">
            Descrição do projeto...
        </p>

        <!-- Info Cards -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="bg-light p-2 rounded text-center small">
                    <p class="text-muted mb-1" style="font-size: 11px;">Cliente</p>
                    <strong>Nome Cliente</strong>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light p-2 rounded text-center small">
                    <p class="text-muted mb-1" style="font-size: 11px;">Orçamento</p>
                    <strong class="text-success">R$ 5.000,00</strong>
                </div>
            </div>
        </div>

        <!-- Datas -->
        <div class="small text-muted mb-3">
            <p class="mb-1">
                <i class="fas fa-calendar-alt"></i>
                Início: <strong>01/01/2025</strong>
            </p>
            <p class="mb-0">
                <i class="fas fa-flag-checkered"></i>
                Conclusão: <strong>31/01/2025</strong>
            </p>
        </div>

        <!-- Progress -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted">Progresso</small>
                <small class="fw-bold">50%</small>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar" style="width: 50%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);"></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="card-footer bg-light border-top d-flex gap-2">
        <a href="#" class="btn btn-outline-primary btn-sm flex-grow-1">
            <i class="fas fa-eye"></i> Ver
        </a>
        <a href="#" class="btn btn-outline-secondary btn-sm flex-grow-1">
            <i class="fas fa-edit"></i> Editar
        </a>
    </div>
</div>
```

---

## 📊 Cards de Resumo

### Simples
```html
<div class="card shadow-sm border-0">
    <div class="card-body">
        <p class="text-muted small mb-2">Total de Projetos</p>
        <h3 class="mb-0 text-primary fw-bold">15</h3>
    </div>
</div>
```

### Com Ícone
```html
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <p class="text-muted small mb-2">Orçamento Total</p>
                <h3 class="mb-0 text-success fw-bold">R$ 50.000,00</h3>
            </div>
            <div class="text-success opacity-25">
                <i class="fas fa-wallet fa-3x"></i>
            </div>
        </div>
    </div>
</div>
```

---

## 🎨 Cores Disponíveis

### Badges de Status
```html
<span class="badge status-planejamento">Planejamento</span>
<span class="badge status-em_andamento">Em Andamento</span>
<span class="badge status-concluido">Concluído</span>
<span class="badge status-atrasado">Atrasado</span>
<span class="badge status-cancelado">Cancelado</span>
```

### Texto
```html
<p class="text-primary">Texto em Azul</p>
<p class="text-success">Texto em Verde</p>
<p class="text-danger">Texto em Vermelho</p>
<p class="text-warning">Texto em Laranja</p>
<p class="text-muted">Texto Cinza</p>
```

### Background
```html
<div class="bg-light">Fundo Claro</div>
<div class="bg-dark text-white">Fundo Escuro</div>
```

---

## 🔄 Fluxo de Navegação Atual

```
index.php (raiz)
    ↓
    ├─→ dashboard.php (Home)
    │   ├─→ projects.php (Listar)
    │   │   ├─→ views/projects/create.php
    │   │   ├─→ views/projects/view.php
    │   │   └─→ views/projects/edit.php
    │   ├─→ reports.php (Relatórios)
    │   └─→ settings.php (Configurações)
    │
    └─→ Menu (includes/menu.php)
        ├─→ Início → dashboard.php
        ├─→ Projetos → projects.php
        ├─→ Relatórios → reports.php
        └─→ Configurações → settings.php
```

---

## 📝 Exemplos Práticos

### Exemplo 1: Criar Página Nova

```php
<?php
session_start();
require_once 'config/database.php';
require_once 'helpers.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Título - MRP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php require_once 'includes/menu.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="mb-1"><i class="fas fa-icon"></i> Título</h1>
                <p class="text-muted">Subtítulo ou descrição</p>
            </div>
        </div>

        <!-- Conteúdo aqui -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### Exemplo 2: Criar um Formulário

```html
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0">Novo Item</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
```

### Exemplo 3: Listar Itens

```html
<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>
                                <span class="badge status-<?php echo $item['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $item['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($item['date'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="view.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deletar?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

---

## 🎯 Dicas e Truques

### 1. Espaçamento
```html
<!-- Margem superior -->
<div class="mt-5">Margem de 3rem (48px)</div>

<!-- Margem inferior -->
<div class="mb-5">Margem de 3rem (48px)</div>

<!-- Gap entre itens (flex) -->
<div class="d-flex gap-2">Espaçamento 0.5rem</div>
```

### 2. Alinhamento
```html
<!-- Alinhar itens horizontalmente -->
<div class="d-flex justify-content-between">
    <div>Esquerda</div>
    <div>Direita</div>
</div>

<!-- Alinhar itens verticalmente -->
<div class="d-flex align-items-center">
    <div>Verticalmente centralizado</div>
</div>
```

### 3. Responsividade
```html
<!-- Diferentes tamanhos em diferentes telas -->
<div class="col-12 col-md-6 col-lg-4">
    <!-- 100% mobile, 50% tablet, 33% desktop -->
</div>
```

### 4. Flexibilidade
```html
<!-- Grow para ocupar espaço disponível -->
<div class="flex-grow-1">Ocupa espaço restante</div>

<!-- Cards com altura igual -->
<div class="h-100">Altura 100% do pai</div>
```

---

## 🚀 Próximas Melhorias

- [ ] Adicionar animações de página
- [ ] Implementar loader/spinner
- [ ] Adicionar toasts de notificação
- [ ] Criar tema dark/light
- [ ] Adicionar filtros avançados
- [ ] Implementar paginação
- [ ] Adicionar busca em tempo real

---

*Guia de Uso - 9 de dezembro de 2025*
