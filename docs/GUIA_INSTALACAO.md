# 🚀 Guia de Instalação e Migração

## ⚡ Quick Start

Se você já tem o banco de dados `mrp_project_manager` criado, execute os seguintes comandos SQL para adicionar os novos campos:

### Opção 1: Recriar o Banco (Limpar)
```sql
-- Execute o arquivo database.sql completamente
-- Isso vai recriar todas as tabelas com os novos campos
mysql -u seu_usuario -p mrp_project_manager < database.sql
```

### Opção 2: Adicionar Campos Incrementalmente
Se você já tem dados que deseja preservar:

```sql
-- 1. Adicionar campos na tabela projects
ALTER TABLE projects ADD COLUMN observation TEXT;
ALTER TABLE projects ADD COLUMN maintenance_start_date DATE;
ALTER TABLE projects ADD COLUMN maintenance_end_date DATE;

-- 2. Adicionar campo na tabela activities
ALTER TABLE activities ADD COLUMN observation TEXT;

-- 3. Criar nova tabela de credenciais
CREATE TABLE project_access_credentials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    access_type VARCHAR(100) NOT NULL COMMENT 'ex: FTP, SSH, Database, CMS, etc',
    server_url VARCHAR(255),
    username VARCHAR(255),
    password VARCHAR(255),
    port INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);
```

---

## 📋 Verificação Pós-Instalação

### 1. Verificar Estrutura do Banco
```sql
-- Verificar colunas da tabela projects
DESCRIBE projects;

-- Resultado esperado - deve incluir:
-- - observation (TEXT)
-- - maintenance_start_date (DATE)
-- - maintenance_end_date (DATE)
-- - created_at (TIMESTAMP)
-- - updated_at (TIMESTAMP)

-- Verificar se tabela de credenciais existe
SHOW TABLES LIKE 'project_access_credentials';
```

### 2. Verificar Integridade de Arquivos
```bash
# Validar PHP
php -l models/Project.php
php -l controllers/ProjectController.php
php -l views/projects/create.php
php -l views/projects/edit.php

# Resultado esperado: "No syntax errors detected"
```

### 3. Testar Server
```bash
# Iniciar servidor PHP
php -S localhost:8000

# Testar endpoints
curl -I http://localhost:8000/views/projects/create.php
curl -I http://localhost:8000/projects.php

# Resultado esperado: "HTTP/1.1 200 OK"
```

---

## 🔄 Processo de Atualização

### Se você tinha versão anterior:

#### 1. Backup
```bash
# Backup do banco de dados
mysqldump -u seu_usuario -p mrp_project_manager > backup_$(date +%Y%m%d).sql

# Backup dos arquivos
cp -r /caminho/para/gestao-projetos /caminho/para/gestao-projetos.backup
```

#### 2. Atualizar Código
```bash
# Atualizar arquivos do projeto
# Substituir:
# - views/projects/create.php
# - views/projects/edit.php
# - models/Project.php
# - controllers/ProjectController.php
# - database.sql (referência)
```

#### 3. Migrar Banco (Opção Segura)
```sql
-- Execute os comandos de ALTER TABLE acima
-- Verifique que os dados existentes foram preservados
SELECT COUNT(*) FROM projects;  -- Deve retornar mesmo número
```

#### 4. Testar
```bash
# Criar novo projeto com manutenção
# Testar edição
# Testar adição de credenciais
```

---

## 📝 Estrutura de Pastas (Referência)

```
gestao-projetos/
├── config/
│   └── database.php
├── controllers/
│   ├── ProjectController.php          ✏️ ATUALIZADO
│   └── ActivityController.php
├── models/
│   ├── Project.php                    ✏️ ATUALIZADO
│   └── Activity.php
├── views/
│   ├── projects/
│   │   ├── create.php                 ✏️ ATUALIZADO
│   │   ├── edit.php                   ✏️ ATUALIZADO
│   │   ├── view.php
│   │   └── index.php
│   └── activities/
│       ├── create.php
│       ├── edit.php
│       └── index.php
├── includes/
│   └── menu.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── helpers.php
├── dashboard.php
├── projects.php
├── index.php
├── database.sql                       ✏️ ATUALIZADO
├── CHANGELOG.md                       ✨ NOVO
├── RESUMO_IMPLEMENTACAO.md           ✨ NOVO
└── GUIA_INSTALACAO.md                ✨ NOVO (este arquivo)
```

---

## 🐛 Troubleshooting

### Erro: "Column 'observation' doesn't exist"
**Causa**: Campo não foi adicionado ao banco
**Solução**:
```sql
ALTER TABLE projects ADD COLUMN observation TEXT;
ALTER TABLE activities ADD COLUMN observation TEXT;
```

### Erro: "Unknown table 'project_access_credentials'"
**Causa**: Tabela de credenciais não foi criada
**Solução**:
```sql
CREATE TABLE project_access_credentials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    access_type VARCHAR(100) NOT NULL,
    server_url VARCHAR(255),
    username VARCHAR(255),
    password VARCHAR(255),
    port INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);
```

### Formulário de Manutenção não aparece
**Causa**: JavaScript não está funcionando ou cache do navegador
**Solução**:
1. Limpar cache do navegador (Ctrl+Shift+Delete)
2. Recarregar página (Ctrl+F5)
3. Verificar console do navegador (F12) para erros JS
4. Verificar que projeto tipo está sendo alterado corretamente

### Dados antigos desapareceram
**Causa**: Você executou database.sql que recria as tabelas
**Solução**:
1. Restaurar backup: `mysql -u user -p db < backup.sql`
2. Executar apenas os ALTERs TABLE acima (não DROP)

---

## 🔒 Segurança - Implementar Após Instalação

### CRÍTICO: Criptografia de Senhas
Senhas em `project_access_credentials` estão em texto plano!

```php
// Exemplo de criptografia ao salvar
$password_hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Exemplo de descriptografia ao exibir
// Use hash_equals() para comparação segura
```

### Recomendações
1. **Usar HTTPS** em produção
2. **Hash de senhas** com `password_hash()`
3. **Validação de entrada** com prepared statements (já implementado)
4. **Controle de acesso** por role (admin, gerente, etc)
5. **Auditoria** de quem acessou as credenciais

---

## ✅ Checklist de Instalação

- [ ] Backup do banco de dados feito
- [ ] Novos campos adicionados via ALTER TABLE
- [ ] Nova tabela `project_access_credentials` criada
- [ ] Validação PHP com php -l realizada
- [ ] Server iniciado (php -S localhost:8000)
- [ ] Página create.php carrega sem erros
- [ ] Página edit.php carrega sem erros
- [ ] Testar criar projeto tipo "manutencao"
- [ ] Verificar que campos de manutenção aparecem
- [ ] Testar adicionar credenciais
- [ ] Testar editar projeto existente
- [ ] Verificar timestamps created_at/updated_at
- [ ] Documentação CHANGELOG.md lida

---

## 📞 Suporte

Se encontrar problemas:

1. **Verificar logs** do PHP: `/var/log/php-errors.log`
2. **Verificar console do navegador** (F12)
3. **Verificar erro MySQL**: `SHOW ENGINE INNODB STATUS;`
4. **Validar estrutura**: `DESCRIBE projects;`

---

## 📚 Documentação Relacionada

- [CHANGELOG.md](./CHANGELOG.md) - Histórico de alterações
- [RESUMO_IMPLEMENTACAO.md](./RESUMO_IMPLEMENTACAO.md) - Resumo visual das funcionalidades
- [database.sql](./database.sql) - Schema completo do banco

---

**Última atualização**: 8 de dezembro de 2025  
**Versão**: 1.0  
**Status**: ✅ Pronto para produção (com ressalvas de segurança)
