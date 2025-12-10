# 💡 Exemplos Práticos de Uso

## Exemplo 1: Criar Projeto com Manutenção Mensal

### Cenário
Você contratou uma empresa para:
- Desenvolver um site de e-commerce
- Realizar manutenção mensal por 12 meses

### Passo a Passo

#### 1. Acessar Formulário
```
Navegação: Projetos → Novo Projeto
URL: /views/projects/create.php
```

#### 2. Preencher Formulário
```
Nome do Projeto:        "E-commerce MRP Store"
Tipo do Projeto:        "Desenvolver Site e Manutenção" ← Selecionar isso!
Descrição:              "Plataforma e-commerce com integração pagamento"

Observações:            "Cliente prefere Bootstrap 5, sem jQuery"

Data de Início:         "01/12/2025"
Data de Término:        "31/01/2026"

[Campos de Manutenção aparecem automaticamente!]
Data de Início Manutenção:  "01/02/2026"
Data de Término Manutenção: "31/12/2026"

Orçamento Total:        "15000.00"
Status:                 "Planejamento"
```

#### 3. Adicionar Credenciais (Opcional)
Clique: **[+ Adicionar Credencial]**

**Credencial 1 - Acesso FTP:**
```
Tipo de Acesso:  "FTP"
Servidor/URL:    "ftp.hostingmrp.com"
Usuário:         "mrp_ecommerce"
Senha:           "senhaSegura123#"
Porta:           "21"
Notas:           "Acesso ao servidor de arquivos - alterar senhas a cada 6 meses"
```

Clique: **[+ Adicionar Outro Acesso]**

**Credencial 2 - Banco de Dados:**
```
Tipo de Acesso:  "MySQL Database"
Servidor/URL:    "db.hostingmrp.com"
Usuário:         "mrp_ecom_user"
Senha:           "dbPassword456#"
Porta:           "3306"
Notas:           "Banco principal - backup diário recomendado"
```

Clique: **[+ Adicionar Outro Acesso]**

**Credencial 3 - Painel Administrativo:**
```
Tipo de Acesso:  "CPanel"
Servidor/URL:    "cpanel.hostingmrp.com"
Usuário:         "mrpadmin"
Senha:           "cpanelPass789#"
Porta:           "2083"
Notas:           "Painel de controle da hospedagem - acesso root"
```

#### 4. Salvar Projeto
Clique: **[Salvar Projeto]**

**Resultado**: 
```
✅ Projeto criado com sucesso!
   Redirecionado para: projects.php
```

---

## Exemplo 2: Editar Projeto Existente

### Cenário
O cliente mudou a data de manutenção de 12 para 18 meses.

### Passo a Passo

#### 1. Acessar Lista de Projetos
```
URL: /projects.php
Procurar por: "E-commerce MRP Store"
Clique no ícone: ✏️ (Editar)
```

#### 2. Modificar Datas de Manutenção
```
Data de Início Manutenção:  "01/02/2026" → "01/02/2026" (sem mudança)
Data de Término Manutenção: "31/12/2026" → "30/06/2027" ← Alterar!
```

#### 3. Observar Informações do Projeto
```
ℹ️ Informações do Projeto:
   Criado em: 08/12/2025 14:30
   Última atualização: 08/12/2025 14:32 ← Atualizado automaticamente!
```

#### 4. Salvar Alterações
Clique: **[Salvar Alterações]**

**Resultado**:
```
✅ Projeto atualizado com sucesso!
   Timestamp atualizado para: 08/12/2025 14:35
```

---

## Exemplo 3: Projeto Apenas de Manutenção

### Cenário
Você já tem um site funcionando e precisa contratar manutenção mensal.

### Formulário
```
Nome do Projeto:        "Manutenção - Portal RH"
Tipo do Projeto:        "Manutenção" ← Neste tipo!
Descrição:              "Manutenção preventiva do portal de RH da empresa"

Observações:            "Suporte via Slack - máximo 4h de resposta"

Data de Início:         "01/01/2026"
Data de Término:        "31/12/2026"

[Campos de Manutenção aparecem!]
Data de Início Manutenção:  "01/01/2026"
Data de Término Manutenção: "31/12/2026"

Orçamento Total:        "5000.00" (mensal: ~416/mês)
Status:                 "Em Andamento"
```

---

## Exemplo 4: Projeto Sem Manutenção

### Cenário
Desenvolver um landing page com design único (sem manutenção posterior).

### Formulário
```
Nome do Projeto:        "Landing Page - Campanha Verão"
Tipo do Projeto:        "Desenvolver Apenas o Site" ← Neste tipo!
Descrição:              "Landing page responsiva para campanha de verão"

Observações:            "Válida por 3 meses, após isso será descontinuada"

Data de Início:         "01/12/2025"
Data de Término:        "15/12/2025"

[Campos de Manutenção NÃO aparecem! ✓]

Orçamento Total:        "2500.00"
Status:                 "Em Andamento"

Credenciais: (não precisa)
```

---

## Exemplo 5: Atualizar Observações

### Cenário
Durante a execução do projeto, você descobre informações importantes sobre a estrutura.

### Passos
1. Ir para edit.php do projeto
2. Rolar até "Observações do Projeto"
3. Adicionar nota:
   ```
   "⚠️ IMPORTANTE: 
    - Servidor usa PHP 7.4, não PHP 8.0
    - Banco de dados é PostgreSQL, não MySQL
    - SSL certificado vence em 15/03/2026
    - Fazer backup antes de atualizar dependências"
   ```
4. Salvar alterações

**Resultado**: Nota fica registrada permanentemente no projeto!

---

## Exemplo 6: Visualizar Histórico de Alterações

### Cenário
Verificar quando foi feita a última alteração no projeto.

### No Edit.php:
```
ℹ️ Informações do Projeto:
   Criado em: 08/12/2025 14:30         ← Criação original
   Última atualização: 08/12/2025 16:47 ← Última mudança
```

**Útil para:**
- Saber quando o cliente pediu última alteração
- Rastrear quando os dados foram atualizados
- Manter histórico de mudanças
- Auditoria (quem fez a mudança será adicionado depois)

---

## Exemplo 7: Gerenciar Múltiplas Credenciais

### Cenário
Um projeto grande com múltiplos acessos necessários:

```
Projeto: "Sistema ERP Corporativo"

📝 Credenciais:
├── 1️⃣ FTP Principal
│   ├── Servidor: ftp.servidor.com
│   ├── Usuário: admin_ftp
│   ├── Porta: 21
│   └── Notas: "Acesso geral aos arquivos"
│
├── 2️⃣ SSH/Terminal
│   ├── Servidor: ssh.servidor.com
│   ├── Usuário: root
│   ├── Porta: 22
│   └── Notas: "Acesso via terminal, usar autenticação key-based"
│
├── 3️⃣ Banco de Dados
│   ├── Servidor: db.servidor.com
│   ├── Usuário: erp_user
│   ├── Porta: 3306
│   └── Notas: "Backup automático às 2h da manhã"
│
├── 4️⃣ CPanel/Hospedagem
│   ├── Servidor: cpanel.servidor.com
│   ├── Usuário: cpanel_admin
│   ├── Porta: 2083
│   └── Notas: "Painel de controle - acesso administrativo"
│
├── 5️⃣ Servidor Aplicação
│   ├── Servidor: app.servidor.com
│   ├── Usuário: deploy_user
│   ├── Porta: 22
│   └── Notas: "Deploy automatizado via CI/CD"
│
└── 6️⃣ Acesso Remoto
    ├── Servidor: rdp.servidor.com
    ├── Usuário: remoteadmin
    ├── Porta: 3389
    └── Notas: "RDP Windows - usar VPN para segurança"
```

**Como adicionar tudo**:
1. Preencher 1️⃣ no formulário
2. Clicar [+ Adicionar Outro Acesso] 5 vezes
3. Preencher cada um dos 5 restantes
4. Salvar projeto

---

## Exemplo 8: Workflow Completo de Projeto

### Cronograma Real

```
📅 Projeto: "App Mobile para Delivery"

DIA 1 (08/12/2025):
└─ Criar Projeto
   Status: Planejamento
   Observações: "Reunião com cliente marcada - requisitos em anexo"
   Credenciais: GitHub, Firebase, API Server
   created_at: 08/12/2025 10:00

DIA 8 (15/12/2025):
└─ Editar Projeto
   Status: Em Andamento
   Observações: "Desenvolvimento iniciado - backend 40% concluído"
   updated_at: 15/12/2025 14:30

DIA 21 (28/12/2025):
└─ Editar Projeto
   Status: Em Andamento
   Observações: "Frontend 80% - iniciando testes de integração"
   updated_at: 28/12/2025 16:45

DIA 30 (06/01/2026):
└─ Editar Projeto
   Status: Concluído
   Observações: "App aprovado pelo cliente, deploy para produção realizado"
   Manutenção: 07/01/2026 - 06/07/2026 (6 meses suporte)
   updated_at: 06/01/2026 17:20
```

---

## 🎯 Melhores Práticas

### 1. Observações
```
✅ BOM:
"⚠️ CRÍTICO: Usar PHP 7.4+
 - Atualizar Composer antes de fazer deploy
 - Rodar migrations: php artisan migrate
 - Limpar cache: php artisan cache:clear"

❌ RUIM:
"algo"
```

### 2. Credenciais
```
✅ BOM:
- Título claro: "FTP - Arquivos do Site"
- Notas descritivas: "Alterar senha a cada 6 meses"
- Porta correta: 21 (FTP), 22 (SSH), 3306 (MySQL)

❌ RUIM:
- Título vago: "Acesso"
- Sem notas: vazio
- Porta incorreta: usar padrão quando possível
```

### 3. Datas de Manutenção
```
✅ BOM:
- Período claro: 01/02/2026 - 31/01/2027 (1 ano)
- Observação: "Manutenção preventiva mensal, 4 horas por mês"
- Status: Marcado como "Em Andamento" durante período

❌ RUIM:
- Datas estranhas: 01/02/2026 - 02/02/2026 (1 dia?)
- Sem observação: não sabe o que esperar
- Status incorreto: "Cancelado" mas com manutenção ativa
```

---

## 📞 Cenários de Erro e Como Corrigir

### Erro: Campos de Manutenção não aparecem
```
❌ Problema: Selecionou "Desenvolver Apenas o Site"
✅ Solução: Selecionar "Desenvolver Site e Manutenção" ou "Manutenção"
```

### Erro: Credenciais desapareceram
```
❌ Problema: Navegou para outra página sem salvar
✅ Solução: Sempre clicar [Salvar Projeto] para persistir dados
```

### Erro: Senha visível em texto plano
```
❌ Aviso: Senhas não estão criptografadas
✅ Solução: Implementar criptografia em produção
        Usar: password_hash($senha, PASSWORD_BCRYPT)
```

---

**Última atualização**: 8 de dezembro de 2025  
**Exemplos**: 8 cenários práticos inclusos
**Pronto para usar**: ✅ Sim!
