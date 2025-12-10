# 🚀 Guia Rápido - Sistema de Gastos

## Acesso ao Sistema

### 1. Acessar a Listagem de Projetos
```
http://localhost:8000/projects.php
```

### 2. Ver Detalhes de um Projeto
```
Clicar no botão [👁 Ver] na listagem
OU
http://localhost:8000/views/projects/view.php?id=1
```

### 3. Gerenciar Gastos do Projeto
```
Clicar no botão [💰 Gerenciar Gastos] na listagem
OU
Clicar em [Gerenciar Gastos] na página de detalhes
OU
http://localhost:8000/views/expenses/index.php?project_id=1
```

---

## Como Registrar um Gasto

### Passo 1: Acessar Gerenciar Gastos
- Vá para a listagem de projetos
- Clique em **[💰 Gerenciar Gastos]**

### Passo 2: Preencher o Formulário
```
┌─────────────────────────────────┐
│  REGISTRAR NOVO GASTO           │
├─────────────────────────────────┤
│ Tipo de Gasto: [dropdown ▼]    │
│ Valor (R$): [0,00]              │
│ Data: [08/12/2025]              │
│ Descrição: [textarea]           │
└─────────────────────────────────┘
```

**Campos:**
- **Tipo de Gasto**: Material, Mão de Obra, Software, Infraestrutura, Outro
- **Valor**: Digite o valor em reais (Ex: 1500.50)
- **Data**: Selecione a data do gasto
- **Descrição**: (Opcional) Detalhe do gasto

### Passo 3: Clicar em "Registrar Gasto"
- O gasto será adicionado à lista
- O total do projeto será atualizado automaticamente

---

## Visualizar Gastos

### Tabela de Gastos
```
┌──────────┬────────────┬─────────────────┬────────────┐
│  Data    │  Tipo      │  Descrição      │  Valor     │
├──────────┼────────────┼─────────────────┼────────────┤
│ 08/12/25 │ Material   │ Tinta e tinta   │ R$ 250.00  │
│ 07/12/25 │ Mão Obra   │ Desenvolvedor   │ R$ 1500.00 │
│ 06/12/25 │ Software   │ Licença Adobe   │ R$ 800.00  │
└──────────┴────────────┴─────────────────┴────────────┘
```

**Informações Exibidas:**
- Data do gasto
- Tipo de gasto
- Descrição detalhada
- Valor em reais
- Botão de ação (remover)

---

## Remover um Gasto

### Passo 1: Localizar o Gasto
- Procure na tabela de gastos

### Passo 2: Clicar no Botão Remover
- Clique no botão **[🗑️ Remover]** (lixeira)

### Passo 3: Confirmar Exclusão
- Uma janela de confirmação aparecerá
- Clique em **OK** para confirmar
- O gasto será removido
- O total do projeto será recalculado

---

## Acompanhar o Orçamento

### Cards de Resumo
```
┌──────────────────┬──────────────────┬──────────────────┐
│ Orçamento Total  │ Gastos           │ Saldo Disponível │
├──────────────────┼──────────────────┼──────────────────┤
│ R$ 10.000,00     │ R$ 4.500,00      │ R$ 5.500,00      │
└──────────────────┴──────────────────┴──────────────────┘
```

### Barra de Progresso
```
Percentual Utilizado: 45%
████████░░░░░░░░░░░░ 45%

Cores:
🟢 Verde (< 50%)  - Seguro
🟡 Amarelo (50-80%) - Atenção
🔴 Vermelho (> 80%) - Crítico
```

---

## Tipos de Gastos Disponíveis

### 1. Material 🔧
Materiais e suprimentos físicos
```
Exemplos:
- Tinta
- Materiais de construção
- Equipamentos
- Suprimentos de escritório
```

### 2. Mão de Obra 👨‍💼
Custos com recursos humanos
```
Exemplos:
- Salários
- Honorários
- Freelance
- Consultoria
```

### 3. Software/Licença 💻
Licenças, software, subscriptions
```
Exemplos:
- Licenças Adobe
- Hosting
- Domínio
- Subscriptions
```

### 4. Infraestrutura 🌐
Hosting, servidores, domain
```
Exemplos:
- Servidor dedicado
- CDN
- Backup
- SSL Certificate
```

### 5. Outro 📦
Demais tipos de gastos
```
Exemplos:
- Transporte
- Alimentação em reunião
- Outros custos
```

---

## Integração com Projetos

### Atualização Automática
```
Quando você registra um gasto:
1. Valor é salvo na tabela project_costs
2. Total de gastos é calculado
3. Campo current_spent do projeto é atualizado
4. Barra de progresso do orçamento é recalculada
```

### Campo "Gastos" nos Projetos
```
Em "Ver Projeto" você vê:
- Orçamento Total
- Gastos Registrados (atualizado automaticamente)
- Saldo Disponível
- Barra de utilização
```

---

## Dicas de Uso

### ✅ Boas Práticas
1. **Registre gastos regularmente** - Evite atrasos
2. **Use descrições claras** - Facilita auditoria
3. **Categorize corretamente** - Melhor relatório
4. **Acompanhe o orçamento** - Evite surpresas

### ⚠️ Cuidado
1. **Remoção é permanente** - Não há "Desfazer"
2. **Confirme antes de remover** - Sistema pede confirmação
3. **Valores devem ser positivos** - Sistema valida entrada
4. **Data não pode ser no futuro** - Use datas passadas ou atual

---

## Fluxograma de Uso

```
┌─────────────────────────────────┐
│    Acessar Sistema              │
│    (projects.php)               │
└────────────┬────────────────────┘
             │
             ▼
┌─────────────────────────────────┐
│   Listar Todos os Projetos      │
│   ✏️ Editar                      │
│   👁 Ver                         │
│   💰 Gerenciar Gastos           │
└────────────┬────────────────────┘
             │
             ▼
┌─────────────────────────────────┐
│   Página de Gastos              │
│   [Resumo Financeiro]           │
│   [Barra de Progresso]          │
│   [Formulário Novo Gasto]       │
│   [Lista de Gastos]             │
└────────────┬────────────────────┘
             │
    ┌────────┼────────┐
    │        │        │
    ▼        ▼        ▼
┌──────┐ ┌──────┐ ┌──────┐
│Add  │ │View  │ │Delete│
│Gasto│ │List  │ │Gasto │
└──────┘ └──────┘ └──────┘
```

---

## Mensagens do Sistema

### ✅ Sucesso
```
✓ Gasto registrado com sucesso!
✓ Gasto removido com sucesso!
```

### ❌ Erro
```
✗ Erro ao registrar gasto.
✗ Erro ao remover gasto.
✗ Projeto não especificado.
```

### ℹ️ Informação
```
ℹ Nenhum gasto registrado ainda.
ℹ Preencha todos os campos obrigatórios.
```

---

## Atalhos Úteis

```
Página de Projetos:
  [Ver]           → Detalhes do projeto
  [Editar]        → Modificar projeto
  [Gerenciar]     → Gastos do projeto

Página de Gastos:
  [Registrar]     → Novo gasto
  [Remover]       → Deletar gasto
  [Voltar]        → Lista de projetos
```

---

## Suporte

### Problemas Comuns

**P: Não consigo acessar a página de gastos**
```
R: Certifique-se de que:
1. Você está acessando um projeto válido
2. O ID do projeto está na URL (project_id=X)
3. O servidor está rodando (localhost:8000)
```

**P: Os gastos não aparecem na lista**
```
R: Verifique:
1. Se o gasto foi registrado (aguarde a página)
2. Se está no projeto correto
3. Se o banco de dados está conectado
```

**P: Não consigo remover um gasto**
```
R: Tente:
1. Confirmar a exclusão na janela de aviso
2. Verificar se o navegador permite JavaScript
3. Recarregar a página (F5)
```

---

## Documentação Adicional

Para mais informações, consulte:
- `ALTERACOES_CSS_GASTOS.md` - Detalhes técnicos
- `IMPLEMENTACAO_COMPLETA.md` - Visão geral do projeto
- `VISUAL_RESUMO.md` - Resumo visual das mudanças

---

*Guia criado em 8 de dezembro de 2025*
*Versão: 1.0*
