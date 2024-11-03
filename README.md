# Comercialize - Sistema de Gestão de Contas Domésticas

## Descrição

O **Comercialize - Sistema de Gestão de Contas Domésticas** é uma aplicação desenvolvida para auxiliar os usuários a gerenciar suas finanças pessoais de forma eficiente. O sistema permite o controle de contas a pagar e a receber, o registro de compras feitas com cartão de crédito, a visualização de relatórios financeiros detalhados e o controle de acesso seguro por meio de autenticação de usuários.

## Principais Funcionalidades

- **Módulo de Contas a Pagar**: Registre despesas futuras e receba notificações sobre vencimentos.
- **Módulo de Contas a Receber**: Registre receitas e crie receitas recorrentes.
- **Módulo de Compras com Cartão de Crédito**: Registre compras e controle de parcelas e limites de crédito.
- **Relatórios Financeiros**:
  - Despesas mensais por categoria (tabela e gráfico de barras).
  - Saldo entre contas a pagar e a receber.
  - Transações de cartão de crédito.
  - Evolução do saldo ao longo do tempo (gráfico de séries temporais).
- **Exportação de Relatórios**: Geração de relatórios em PDF.
- **Autenticação de Usuário**: Página de login e controle de sessão com suporte para múltiplos usuários.

## Tecnologias Utilizadas

### Back-End:

- **Laravel 11.9**
- **PHP 8.2.6**
- **PostgreSQL** (banco de dados)
- **DOMPDF** (para geração de PDFs)

### Front-End:

- **Vue.js** (integrado com Laravel via Inertia.js)
- **Inertia.js** (integração entre Vue e Laravel)
- **Tailwind CSS** (para estilização)
- **Vuetify** (componentes de UI)

## Requisitos

- **PHP 8.2.6 ou superior**
- **Composer**
- **Node.js e npm**
- **PostgreSQL**
- **Docker** (opcional, para uso com Laravel Sail)

## Instalação e Configuração

### Instalando Localmente (sem Docker)

1. Clone o repositório:

   ```bash
   git clone <URL do repositório>
   cd prova-php-vue-junior-02287-2024-021.900.226-65/contabilize-app
   ```

2. Instale as dependências do Composer:

   ```bash
   composer install
   ```

3. Instale as dependências do Node.js:

   ```bash
   npm install
   ```

4. Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente:

   ```bash
   cp .env.example .env
   ```

5. Gere a chave da aplicação:

   ```bash
   php artisan key:generate
   ```

6. Configure o banco de dados no arquivo `.env` e rode as migrações e seeders:

   ```bash
   php artisan migrate --seed
   ```

7. Compile os ativos front-end:

   ```bash
   npm run build
   ```

8. Inicie o servidor de desenvolvimento:
   ```bash
   php artisan serve
   ```

### Deploy Usando Laravel Sail (Docker)

1. **Clone o repositório**:

   ```bash
   git clone <URL do repositório>
   cd prova-php-vue-junior-02287-2024-021.900.226-65/contabilize-app
   ```

2. **Inicie os containers do Laravel Sail**:

   ```bash
   ./vendor/bin/sail up -d
   ```

3. **Acesse o container e instale as dependências do Composer**:

   ```bash
   ./vendor/bin/sail composer install
   ```

4. **Instale as dependências do Node.js**:

   ```bash
   ./vendor/bin/sail npm install
   ```

5. **Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente**:

   ```bash
   cp .env.example .env
   ```

6. **Gere a chave da aplicação**:

   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

7. **Rode as migrações e seeders**:

   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

8. **Compile os ativos front-end**:
   ```bash
   ./vendor/bin/sail npm run build
   ```

## Inicialização de Filas e Agendamento de Cron Jobs

### Inicializando o Worker de Filas

Para processar as filas de trabalho no Laravel, execute o seguinte comando:

   ```bash
   php artisan queue:work
   ```

## Configuração de Cron Jobs

Para garantir que as tarefas agendadas sejam executadas automaticamente, é necessário configurar o cron job do sistema operacional. Adicione a seguinte linha ao cron (no Linux ou macOS):
   ```bash
   * * * * * cd /caminho/para/sua/aplicacao && php artisan schedule:run >> /dev/null 2>&1
   ```

Essa configuração faz com que o Laravel verifique a cada minuto se há alguma tarefa agendada para ser executada.

### Observações

- **Notificações de Vencimento**: O sistema inclui a funcionalidade de notificar os usuários sobre parcelas e contas a vencer. Isso depende do comando de filas para garantir que as notificações sejam enviadas.
- **Tarefas Agendadas**: O agendamento de tarefas inclui a verificação de receitas recorrentes e a notificação de parcelas atrasadas. Certifique-se de que o cron job esteja configurado para executar essas verificações automaticamente.

## Como Usar

- **Acesse a página de login** (`/login`) para autenticar-se.
- A conta de administrador padrão é `test@example.com:password`.
- **Gerencie contas a pagar e a receber** navegando pelos módulos.
- **Registre compras com cartão de crédito** e acompanhe o limite.
- **Visualize relatórios financeiros** e faça download dos PDFs através das rotas configuradas.

## Contribuição

Se você deseja contribuir para este projeto, sinta-se à vontade para fazer um fork e enviar um pull request.
