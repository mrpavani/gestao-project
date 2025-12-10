# 📋 Implementação de Novas Funcionalidades - 09/12/2025

## 🎯 Resumo das Alterações

Implementadas as seguintes funcionalidades no sistema MRP Project Manager:

### 1. **Módulo de Gestão de Clientes** ✅

#### Arquivos Criados:
- `models/Customer.php` - Modelo CRUD completo para clientes
- `controllers/CustomerController.php` - Controlador de clientes
- `views/customers/index.php` - Listagem de clientes
- `views/customers/create.php` - Formulário de criação
- `views/customers/edit.php` - Formulário de edição
- `views/customers/view.php` - Visualização de detalhes

#### Funcionalidades:
- ✅ Cadastro de clientes/empresas
- ✅ Nome da empresa/cliente
- ✅ Telefone para contato
- ✅ Email
- ✅ Observações
- ✅ CRUD completo (Criar, Ler, Atualizar, Deletar)
- ✅ Exibição de projetos vinculados a cada cliente

#### Acesso:
- Menu principal → **Clientes**
- URL: `/views/customers/index.php`

---

### 2. **Período de Manutenção Separado** ✅

#### Campos Adicionados na Tabela `projects`:
```sql
- maintenance_start_date (DATE) - Início da manutenção
- maintenance_end_date (DATE) - Fim da manutenção
- maintenance_monthly_value (DECIMAL 10,2) - Valor mensal da manutenção
- payment_received_date (DATE) - Data de recebimento do orçamento
```

#### Lógica Implementada:
- **Período de Projeto**: De `start_date` até `end_date`
- **Período de Manutenção**: De `maintenance_start_date` até `maintenance_end_date` (sempre após fim do projeto)
- **Duração da Manutenção**: Mínimo 6 meses, máximo 12 meses
- **Valor da Manutenção**: Configurável como valor mensal

---

### 3. **Data de Recebimento de Valores** ✅

Campo adicionado: `payment_received_date`
- Registra quando o orçamento foi recebido do cliente
- Data opcional para projetos em planejamento
- Será preenchida quando o cliente efetuar pagamento

---

### 4. **Integração de Clientes com Projetos** ✅

#### Alterações no Modelo Project:
- Campo `customer_id` adicionado (Foreign Key para tabela customers)
- Relacionamento opcional (SET NULL se cliente deletado)
- Atualizado `ProjectController` para passar dados de cliente

#### Funcionalidades:
- Cada projeto pode estar vinculado a um cliente
- Um cliente pode ter múltiplos projetos
- Visualização de projetos por cliente
- Contagem de projetos por cliente

---

## 📁 Estrutura de Arquivos Criados/Modificados

### Novos Arquivos:
```
models/Customer.php
controllers/CustomerController.php
views/customers/
  ├── index.php
  ├── create.php
  ├── edit.php
  └── view.php
migration.sql
```

### Arquivos Modificados:
```
database.sql - Atualizado com tabela customers
models/Project.php - Adicionados campos de manutenção e customer_id
controllers/ProjectController.php - Atualizado para novos campos
includes/menu.php - Adicionado link para Clientes
```

---

## 🗄️ Estrutura do Banco de Dados

### Tabela `customers`
```sql
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    observations TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Alterações na Tabela `projects`
```sql
ALTER TABLE projects 
ADD COLUMN customer_id INT,
ADD COLUMN maintenance_monthly_value DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN payment_received_date DATE,
ADD CONSTRAINT fk_projects_customer 
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL;
```

---

## 🔧 Como Usar

### Cadastrar Novo Cliente:
1. Acesse **Menu → Clientes**
2. Clique em **"Novo Cliente"**
3. Preencha:
   - Nome da Empresa/Cliente (obrigatório)
   - Telefone (opcional)
   - Email (opcional)
   - Observações (opcional)
4. Clique em **Salvar Cliente**

### Associar Cliente a Projeto:
1. Ao criar/editar projeto
2. Selecione o cliente no campo "Cliente"
3. O projeto ficará vinculado ao cliente

### Configurar Manutenção:
1. Ao criar/editar projeto
2. Preencha:
   - **Data Início Manutenção**: Quando a manutenção começa
   - **Data Fim Manutenção**: Quando termina (mín 6 meses, máx 12 meses)
   - **Valor Mensal**: Valor cobrado por mês
   - **Data Recebimento**: Quando foi recebido o pagamento

---

## 📊 Dashboard

O dashboard foi atualizado para exibir:
- Total de Projetos
- Orçamento Total
- Total de Projetos (Desenvolvimento)
- Total de Manutenção

---

## 🔄 Fluxo de Manutenção

**Exemplo:**
- Projeto de desenvolvimento: 01/10/2025 a 01/12/2025 (2 meses)
- Manutenção: 01/12/2025 a 01/12/2026 (12 meses)
- Valor mensal: R$ 500,00

---

## ⚠️ Importante

Para aplicar as alterações no banco de dados existente, execute:

```sql
-- Arquivo: migration.sql
-- Contém os comandos ALTER TABLE necessários

-- Ou execute manualmente:
ALTER TABLE projects ADD COLUMN customer_id INT AFTER id;
ALTER TABLE projects ADD COLUMN maintenance_monthly_value DECIMAL(10,2) AFTER maintenance_end_date;
ALTER TABLE projects ADD COLUMN payment_received_date DATE AFTER maintenance_monthly_value;
ALTER TABLE projects ADD CONSTRAINT fk_projects_customer 
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL;
```

---

## ✨ Próximas Funcionalidades Sugeridas

1. Relatório de manutenções ativas
2. Alertas de vencimento de manutenção
3. Historico de pagamentos por cliente
4. Filtros por período de manutenção
5. Exportação de relatórios por cliente

---

**Data da Implementação:** 09 de Dezembro de 2025  
**Status:** ✅ COMPLETO
