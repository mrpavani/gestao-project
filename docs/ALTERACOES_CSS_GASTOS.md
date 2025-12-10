# Alterações - CSS e Sistema de Gastos

## Data de Implementação
8 de dezembro de 2025

## Resumo das Alterações

### 1. Separação do CSS

O arquivo de estilos customizados `assets/css/style.css` foi adicionado a todas as páginas do sistema:

#### Arquivos Atualizados:
- ✅ `views/projects/create.php` - Adicionado link CSS
- ✅ `views/projects/edit.php` - Adicionado link CSS
- ✅ `views/activities/create.php` - Adicionado link CSS

#### Link CSS Padrão:
```html
<link href="../../assets/css/style.css" rel="stylesheet">
```

---

### 2. Sistema de Cadastro de Gastos/Despesas

#### Novos Arquivos Criados:

**a) `models/Expense.php`**
- Classe Model para gerenciar despesas do projeto
- Métodos implementados:
  - `create()` - Registrar novo gasto
  - `readByProjectId()` - Listar gastos por projeto
  - `getTotal()` - Calcular total de gastos do projeto
  - `update()` - Atualizar informações do gasto
  - `delete()` - Remover gasto
  - `getById()` - Obter detalhes de um gasto específico

**b) `controllers/ExpenseController.php`**
- Controller para lógica de negócio de despesas
- Métodos implementados:
  - `create($data)` - Criar novo gasto e atualizar total do projeto
  - `getByProjectId($projectId)` - Recuperar gastos do projeto
  - `getTotalByProjectId($projectId)` - Obter total de gastos
  - `update($data)` - Atualizar gasto
  - `delete($id, $projectId)` - Remover gasto
  - `updateProjectSpent($projectId)` - Sincronizar total com projeto

**c) `views/expenses/index.php`**
- Interface completa para gerenciar gastos
- Funcionalidades:
  - Visualizar orçamento total, gastos e saldo disponível
  - Barra de progresso de utilização do orçamento
  - Formulário para registrar novos gastos
  - Tabela com listagem de gastos por data
  - Opção de remover gastos
  - Avisos visuais sobre status orçamentário

**d) `views/projects/view.php`**
- Página de visualização detalhada do projeto
- Informações exibidas:
  - Dados gerais do projeto (descrição, status, datas)
  - Cronograma (data início, término, duração)
  - Informações financeiras (orçamento, gastos, saldo)
  - Gráfico de utilização do orçamento
  - Link para "Gerenciar Gastos"

#### Estrutura da Tabela de Gastos:
```sql
CREATE TABLE project_costs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    cost_type ENUM('material', 'mao_de_obra', 'software', 'infraestrutura', 'outro'),
    description VARCHAR(255),
    amount DECIMAL(10,2),
    cost_date DATE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);
```

#### Tipos de Gastos Disponíveis:
1. **Material** - Materiais e suprimentos
2. **Mão de Obra** - Custos com recursos humanos
3. **Software/Licença** - Licenças e software
4. **Infraestrutura** - Hosting, servidores, etc.
5. **Outro** - Demais gastos

---

### 3. Estilos CSS Adicionados

Novos estilos foram adicionados ao `assets/css/style.css` para melhorar a visualização:

```css
/* Estilos para Gastos/Despesas */
.expense-card { }           /* Card individual de gasto */
.expense-type-badge { }     /* Badge do tipo de gasto */
.progress-bar-expense { }   /* Barra de progresso com gradiente */
.expense-summary { }        /* Grid de resumo financeiro */
.budget-info { }            /* Container de informações orçamentárias */
.budget-warning { }         /* Aviso de orçamento em alerta */
.budget-danger { }          /* Alerta crítico de orçamento */
.budget-safe { }            /* Status seguro de orçamento */
```

---

### 4. Fluxo de Navegação

```
Dashboard (index.php)
    └── Projetos (projects.php)
        ├── Ver Projeto (views/projects/view.php)
        │   └── Gerenciar Gastos (views/expenses/index.php)
        ├── Editar Projeto (views/projects/edit.php)
        └── Criar Projeto (views/projects/create.php)
```

---

### 5. Recursos Implementados

#### Gerenciamento de Gastos:
✅ Registrar novo gasto com:
- Tipo (Material, Mão de Obra, Software, Infraestrutura, Outro)
- Descrição detalhada
- Valor em reais
- Data do gasto

✅ Visualizar:
- Lista de todos os gastos do projeto
- Total de gastos registrados
- Orçamento total disponível
- Saldo restante
- Percentual de utilização do orçamento

✅ Gerenciar:
- Atualizar informações de gastos (apenas exclusão implementada por enquanto)
- Remover gastos com confirmação
- Atualizar automaticamente o total do projeto

✅ Relatórios Visuais:
- Barra de progresso colorida (Verde < 50% | Amarelo 50-80% | Vermelho > 80%)
- Cards informativos com resumo financeiro
- Avisos de status orçamentário

---

### 6. Validação

✅ **models/Expense.php** - Sem erros de sintaxe
✅ **controllers/ExpenseController.php** - Sem erros de sintaxe
✅ **views/expenses/index.php** - Sem erros de sintaxe
✅ **views/projects/view.php** - Sem erros de sintaxe
✅ **views/projects/create.php** - CSS adicionado
✅ **views/projects/edit.php** - CSS adicionado
✅ **views/activities/create.php** - CSS adicionado

---

### 7. Como Usar

#### Registrar um Novo Gasto:
1. Acesse a página do projeto
2. Clique em "Gerenciar Gastos"
3. Preencha o formulário com:
   - Tipo de gasto
   - Descrição
   - Valor
   - Data
4. Clique em "Registrar Gasto"

#### Visualizar Gastos:
- A tabela exibe todos os gastos do projeto
- Ordenados por data (mais recentes primeiro)
- Mostra tipo, descrição e valor de cada gasto

#### Remover um Gasto:
1. Localize o gasto na tabela
2. Clique no botão "Remover" (ícone lixeira)
3. Confirme a exclusão
4. O total do projeto será atualizado automaticamente

---

### 8. Integração com Sistema Existente

O novo sistema de gastos integra-se perfeitamente com:
- ✅ Sistema de Projetos existente
- ✅ Tabela `project_costs` já existente no banco
- ✅ Campo `current_spent` da tabela `projects`
- ✅ Autenticação e sessões
- ✅ Estilos Bootstrap 5.1.3

---

### 9. Observações Importantes

1. O orçamento é atualizado automaticamente quando:
   - Um gasto é registrado
   - Um gasto é atualizado
   - Um gasto é removido

2. Conversão de valores:
   - Todos os valores monetários são armazenados em DECIMAL(10,2)
   - Exibidos formatados em Real Brasileiro (R$)

3. Segurança:
   - Todos os inputs são sanitizados com `htmlspecialchars()` e `strip_tags()`
   - Preparadas statements são utilizadas para evitar SQL injection
   - Confirmação antes de deletar

---

### 10. Próximas Melhorias Sugeridas

- [ ] Editar gastos existentes
- [ ] Exportar relatório de gastos em PDF
- [ ] Gráfico de despesas por tipo
- [ ] Filtros avançados de gastos
- [ ] Aviso quando orçamento está próximo do limite
- [ ] Histórico de alterações de gastos
- [ ] Análise de gastos por período
