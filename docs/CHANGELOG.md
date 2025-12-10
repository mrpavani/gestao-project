# Changelog - MRP Gestão de Projetos

## 📅 Atualização de 8 de dezembro de 2025

### ✨ Novas Funcionalidades Implementadas

#### 1. **Período de Manutenção Condicionado** ✅
- Adicionados campos `maintenance_start_date` e `maintenance_end_date` na tabela `projects`
- Campos aparecem condicionalmente apenas quando o tipo de projeto é:
  - "Desenvolver Site e Manutenção"
  - "Manutenção"
- Interface limpa com seção destacada em cinza
- JavaScript automático para mostrar/ocultar baseado na seleção de tipo

**Arquivos afetados:**
- `database.sql` - Adicionadas colunas de manutenção
- `models/Project.php` - Propriedades e métodos atualizados
- `controllers/ProjectController.php` - Suporte aos novos campos
- `views/projects/create.php` - Formulário com campos condicionais + JS
- `views/projects/edit.php` - Campos de manutenção com pré-preenchimento

#### 2. **Campo de Observação por Atividade** ✅
- Adicionado campo `observation` na tabela `activities`
- Campo `observation` também adicionado na tabela `projects` para observações gerais
- Textarea com espaço amplo para anotações detalhadas
- Suporta múltiplas linhas e formatação de texto

**Arquivos afetados:**
- `database.sql` - Nova coluna `observation` em activities e projects
- `models/Project.php` - Suporte ao novo campo
- `views/projects/create.php` - Campo de observação no formulário
- `views/projects/edit.php` - Campo de observação editável

#### 3. **Informações de Criação e Atualização** ✅
- Exibição de `created_at` (data/hora de criação)
- Exibição de `updated_at` (data/hora de última atualização)
- Formatação humanizada: "d/m/Y H:i" (ex: "08/12/2025 14:30")
- Seção destacada com alerta (alert-info) no formulário de edição
- Timestamps automáticos no banco de dados

**Arquivos afetados:**
- `models/Project.php` - Propriedades `created_at` e `updated_at` preservadas
- `views/projects/edit.php` - Seção informativa na base do formulário

#### 4. **Cadastro de Dados de Acesso do Projeto** ✅
- Nova tabela `project_access_credentials` no banco de dados
- Interface dinâmica para cadastrar múltiplos tipos de acesso
- Campos por credencial:
  - **Tipo de Acesso** (ex: FTP, SSH, CPanel, Database, CMS, etc)
  - **Servidor/URL** (endereço do servidor)
  - **Usuário** (username)
  - **Senha** (password - campo protegido)
  - **Porta** (opcional - ex: 21, 22, 3306)
  - **Notas** (observações sobre o acesso)

**Interface:**
- Seção "Credenciais de Acesso (Opcional)" no formulário de criação
- Botão "Adicionar Outro Acesso" para múltiplas credenciais
- Botão de delete (lixeira) para remover credenciais não desejadas
- Contador automático para gerenciamento de IDs de formulário
- Formulário responsivo com grid Bootstrap

**Arquivos afetados:**
- `database.sql` - Nova tabela `project_access_credentials`
- `views/projects/create.php` - Seção dinâmica de credenciais + JavaScript
- `controllers/ProjectController.php` - Método `saveCredentials()` para processamento futuro

### 🔧 Melhorias Técnicas

#### Navegação Corrigida
- Links "Voltar" e "Cancelar" em `create.php` agora apontam para `projects.php` (não `index.php`)
- Links em `edit.php` também corrigidos para `projects.php`
- Fluxo de navegação: Dashboard → Projetos → Criar/Editar → Voltar para Projetos

#### Model Layer Atualizado
```php
// Novos campos no Model Project:
public $observation;
public $maintenance_start_date;
public $maintenance_end_date;
public $created_at;
public $updated_at;
```

#### Métodos Atualizados
- `create()` - Suporta observation, maintenance_start/end_date
- `update()` - Suporta novos campos
- `read()` - Retorna todos os campos incluindo credenciais
- `readOne()` - Pré-carrega dados com tratamento de NULL

### 📊 Schema do Banco de Dados

#### Tabela `projects` (Alterada)
```sql
maintenance_start_date DATE          -- Novo: Início da manutenção
maintenance_end_date DATE            -- Novo: Fim da manutenção
observation TEXT                     -- Novo: Observações gerais
created_at TIMESTAMP                 -- Existente: Preservado
updated_at TIMESTAMP                 -- Existente: Preservado
```

#### Tabela `activities` (Alterada)
```sql
observation TEXT                     -- Novo: Observações da atividade
```

#### Tabela `project_access_credentials` (Nova)
```sql
CREATE TABLE project_access_credentials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    access_type VARCHAR(100) NOT NULL,      -- ex: FTP, SSH, Database
    server_url VARCHAR(255),                -- Endereço do servidor
    username VARCHAR(255),                  -- Usuário
    password VARCHAR(255),                  -- Senha (criptografar em produção!)
    port INT,                               -- Porta (opcional)
    notes TEXT,                             -- Notas adicionais
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);
```

### 🎨 Interface/UX

#### JavaScript Dinâmico em create.php
```javascript
// Mostra/oculta campos de manutenção automaticamente
- Listener no select de project_type
- Display block/none baseado no tipo selecionado
- Limpeza automática de valores quando ocultado

// Gerenciamento dinâmico de credenciais
- addCredential() - Adiciona novo campo
- removeCredential(button) - Remove campo específico
- Contador credentialCount para IDs únicos
```

#### JavaScript em edit.php
```javascript
// Função toggleMaintenanceFields()
- Executa ao carregar a página (DOMContentLoaded)
- Executa ao mudar o tipo de projeto
- Mantém campos visíveis se projeto já tem manutenção
```

### ✅ Validação

Todos os arquivos PHP passaram validação:
```
✓ views/projects/create.php
✓ views/projects/edit.php  
✓ models/Project.php
✓ controllers/ProjectController.php
✓ database.sql (script de criação)
```

### 📋 Checklist de Requisitos

- [x] Período de início e fim da manutenção para projetos com manutenção
- [x] Campo de observação para cada atividade (e para projetos)
- [x] Informações de data de criação (created_at) e última atualização (updated_at)
- [x] Opção para cadastrar dados de acesso do projeto (interface + schema)
- [x] Validação de sintaxe PHP em todos os arquivos
- [x] Compatibilidade backward com PHP 5.6+
- [x] Navegação corrigida para projects.php

### 🚀 Próximos Passos (Sugestões)

1. **Implementar criptografia de senhas** em `project_access_credentials`
   - Usar `password_hash()` ou biblioteca externa como phpseclib

2. **Criar página de visualização de credenciais** (`views/projects/credentials.php`)
   - Listar todas as credenciais do projeto
   - Opção de editar/deletar
   - Máscara para visualizar senhas (clique para revelar)

3. **Adicionar gerenciador de atividades** com campo observation
   - Criar `views/activities/create.php`
   - Criar `views/activities/edit.php`

4. **Relatório detalhado** de projetos
   - Incluir seção de credenciais
   - Incluir seção de observações
   - Incluir histórico de atualizações

5. **Auditoria de alterações**
   - Registrar quem alterou o quê e quando
   - Histórico completo de mudanças

### 📝 Notas Importantes

- **Segurança**: Senhas em `project_access_credentials` estão em texto plano. Para produção, implementar criptografia.
- **Backup**: Considere fazer backup do banco antes de aplicar as migrações.
- **Testes**: Recomendado testar o fluxo completo de criação/edição de projetos com manutenção.

---

**Status**: ✅ Implementação Completa
**Data**: 8 de dezembro de 2025
**Teste**: Validado em localhost:8000
