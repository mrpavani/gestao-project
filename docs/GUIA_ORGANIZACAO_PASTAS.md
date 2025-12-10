# 📁 GUIA DE ORGANIZAÇÃO DE PASTAS

## Estrutura Atual

```
/gestao-projetos/
├── pages/               ← Nova pasta para páginas públicas
├── admin/               ← Nova pasta para páginas administrativas
├── assets/
│   ├── css/
│   │   └── style.css    ← Estilos centralizados
│   └── js/
│       └── script.js
├── config/
│   └── database.php
├── controllers/
├── models/
├── views/
├── includes/
│   └── menu.php         ← Menu compartilhado
├── dashboard.php
├── projects.php
├── reports.php
├── settings.php
├── index.php
├── helpers.php
└── database.sql
```

---

## 📂 Como Organizar (Próximas Etapas)

### Passo 1: Mover Páginas Públicas para `/pages/`

**De:**
```
gestao-projetos/
├── dashboard.php
├── projects.php
└── reports.php
```

**Para:**
```
gestao-projetos/pages/
├── dashboard.php
├── projects.php
├── reports.php
└── settings.php
```

### Passo 2: Mover Páginas Administrativas para `/admin/`

**De:**
```
gestao-projetos/views/
├── projects/
│   ├── create.php
│   ├── edit.php
│   ├── view.php
│   └── index.php
└── activities/
    ├── create.php
    ├── edit.php
    └── index.php
```

**Para:**
```
gestao-projetos/admin/
├── projects/
│   ├── create.php
│   ├── edit.php
│   ├── view.php
│   └── index.php
└── activities/
    ├── create.php
    ├── edit.php
    └── index.php
```

### Passo 3: Mover Views para `/admin/`

**De:**
```
gestao-projetos/views/
├── credentials/
├── expenses/
└── projects/
```

**Para:**
```
gestao-projetos/admin/views/
├── credentials/
├── expenses/
└── projects/
```

---

## 🔗 Atualizações de Rotas Necessárias

### Exemplo 1: Dashboard
```php
// Antes (raiz)
// index.php -> redirect dashboard.php

// Depois (em /pages/)
// index.php -> redirect pages/dashboard.php
header("Location: pages/dashboard.php");
```

### Exemplo 2: Menu de Navegação
```php
// Antes (raiz)
<a href="./dashboard.php">

// Depois (/pages/)
<a href="../../pages/dashboard.php">

// Ou criar função helper
<a href="<?php echo get_url('pages/dashboard.php'); ?>">
```

### Exemplo 3: Views
```php
// Antes
href="views/projects/create.php"

// Depois (de /pages/)
href="<?php get_relative_url('admin/projects/create.php'); ?>"
```

---

## 📝 Helper Functions Sugeridas

Adicionar em `helpers.php`:

```php
<?php
/**
 * Retorna URL relativa baseada na localização atual
 */
function get_url($path) {
    $levels = substr_count($_SERVER['PHP_SELF'], '/') - 2;
    $prefix = str_repeat('../', max(0, $levels - 2));
    return $prefix . $path;
}

/**
 * Retorna caminho de redirect
 */
function redirect($path) {
    header("Location: " . get_url($path));
    exit;
}

/**
 * Verifica se está em subdiretório
 */
function in_subdir() {
    return substr_count($_SERVER['PHP_SELF'], '/') > 2;
}

/**
 * Retorna nível de profundidade
 */
function get_depth_level() {
    return max(0, substr_count($_SERVER['PHP_SELF'], '/') - 3);
}
?>
```

---

## ✅ Checklist de Migração

### Fase 1: Preparação
- [ ] Criar pasta `/pages/`
- [ ] Criar pasta `/admin/`
- [ ] Backup de todos os arquivos
- [ ] Adicionar funções helpers

### Fase 2: Migração
- [ ] Mover `dashboard.php` para `/pages/`
- [ ] Mover `projects.php` para `/pages/`
- [ ] Mover `reports.php` para `/pages/`
- [ ] Mover `settings.php` para `/pages/`
- [ ] Mover `/views/` para `/admin/views/`

### Fase 3: Atualização de Rotas
- [ ] Atualizar `index.php`
- [ ] Atualizar `includes/menu.php`
- [ ] Atualizar todos os links relativos
- [ ] Atualizar `require_once` paths
- [ ] Testar todos os links

### Fase 4: Testes
- [ ] Testar navegação entre páginas
- [ ] Testar links de ação
- [ ] Testar formulários
- [ ] Testar responsividade
- [ ] Validar PHP syntax

---

## 🎯 Benefícios da Organização

### Antes
```
Muitos arquivos na raiz
Difícil localizar páginas
Views misturadas
```

### Depois
```
Estrutura clara e organizada
Páginas públicas em /pages/
Administração em /admin/
Views bem separadas
```

---

## 📋 Exemplo de Índice Novo

```html
<?php
session_start();

// Determinar se está em produção
$isProd = false; // Mudar para true em produção

// Redirecionar para página home/dashboard
if ($isProd) {
    header("Location: pages/dashboard.php");
} else {
    // Em desenvolvimento, mostrar opções
    header("Location: pages/dashboard.php");
}
exit;
?>
```

---

## 🔐 Estrutura de Segurança

Após organizar pastas, você pode:

```
1. Colocar arquivos confidenciais fora de /public/
2. Proteger /admin/ com autenticação
3. Restringir acesso direto a /admin/ no .htaccess
4. Criar rotas seguras para ações administrativas
```

---

## 📚 Arquivos de Configuração Sugeridos

### .htaccess (root)
```apache
# Redirecionar raiz para pages/dashboard.php
RewriteEngine On
RewriteRule ^$ pages/dashboard.php [L]
```

### .htaccess (/admin)
```apache
# Proteger acesso admin
<FilesMatch "^[^.]">
    Order Deny,Allow
    Deny from all
</FilesMatch>

<FilesMatch "\.(php)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>
```

---

## 🚀 Próximo Passo

Quando estiver pronto para migrar:

1. Execute o checklist acima
2. Use as funções helpers
3. Teste tudo em staging
4. Faça backup antes de aplicar

---

## 📞 Suporte

Se encontrar erros de caminho:

1. Verifique o nível de profundidade: `get_depth_level()`
2. Use a função `get_url()` para paths relativos
3. Teste os links em diferentes páginas
4. Valide a sintaxe PHP

---

*Guia de Organização - 9 de dezembro de 2025*
