# Sistema de Gerenciamento de Biblioteca

Um sistema completo para gestão de acervo literário, empréstimos e usuários de biblioteca, desenvolvido com Laravel 11, Inertia.js e Vue.js 3.

![Screenshot do Sistema](https://i.imgur.com/2r6hnYd.png) 

## Validações inteligêntes
![Screenshot do Sistema](https://i.imgur.com/uLdIFI2.png) 

## Funcionalidades Principais

### 📚 Gestão de Livros
- Cadastro completo de livros com:
  - Título, autor e descrição
  - Categorias/Gêneros
  - Upload de capa (formato PNG)
- Controle de cópias físicas:
  - Cadastro de múltiplas cópias com números de registro únicos
  - Status de disponibilidade em tempo real
  - Exclusão individual de cópias

### 👥 Gestão de Usuários
- Cadastro de usuários da biblioteca com:
  - Nome completo
  - CPF único
  - E-mail e dados de contato
- Histórico de empréstimos por usuário
- Busca inteligente de usuários (Em desenvolvidmento)

### 🔄 Gestão de Empréstimos
- Realização de empréstimos com:
  - Seleção múltipla de cópias
  - Datas de empréstimo e devolução
- Controle automático de status:
  - Empréstimo ativo
  - Devolução com atualização de estoque
  - Atrasos com destaque visual
- Visualização em tempo real de:
  - Cópias disponíveis para empréstimo
  - Histórico completo de movimentações (Em desenvolvimento)

### 📊 Funcionalidades Especiais
- Interface intuitiva com:
  - DataTables interativos
  - Modais contextualizados
  - Filtros inteligentes (Em desenvolvimento)
- Notificações visuais com SweetAlert2
- Validações de dados em tempo real

## Tecnologias Utilizadas

### Backend
- **Laravel 11** - Framework PHP
- **MySQL** - Banco de dados relacional
- **Eloquent ORM** - Mapeamento objeto-relacional
- **Sanctum** - Autenticação de API

### Frontend
- **Vue.js 3** - Framework JavaScript
- **Inertia.js** - Integração frontend/backend
- **Tailwind CSS** - Estilização utilitária
- **Vue Select/Multiselect** - Componentes avançados
- **DataTables** - Tabelas interativas

### Ferramentas
- **Composer** - Gerenciamento de dependências PHP
- **NPM** - Gerenciamento de pacotes JavaScript
- **Vite** - Bundler e build tool

## Instalação 
Versão do PHP 8.2
- Sevidor NGINX ou Apache da sua escolha
- Recomendo fortemente baixar o HERB => https://herd.laravel.com/windows
  Herd é um ambiente de desenvolvimento nativo e rápido para Laravel e PHP para Windows. 
  Ele inclui tudo o que você precisa para começar a desenvolver com Laravel, incluindo PHP e nginx.
- Depois de instalar o Herd, você está pronto para começar a desenvolver com Laravel.
- No Herb escolha versão do PHP 8.2 e pronto. 
- Eu acho melhor que o XAMPP ou WAMP SERVER.

1. Clone o repositório:

2. Instale as dependências:
```bash
composer install
npm install
```
3. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```
4. Configure o banco de dados no .env, banco recomendado Mysql:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```
5. Execute as migrations após ter criado o banco de dados e configurado as variáveis de ambiente no .env:
```bash
php artisan migrate
```
6. Compile os assets:
```bash
npm run build
npm run dev
```
7. Inicialize o servidor do laravel:
```bash
php artisan serve
```
8.Acesse o sistema:
http://localhost:8000
http://localhost:portaquevoceusa

Ao acessar o sistema você será redirecionado para tela de login, clique em Registre-se e crie sua conta, após isso será redirecionado para tela de gerenciamento de usuários, cadastre os usuários da biblioteca para que eles possam solicitar os empréstimos dos livros, depois acesse a tela de Livros e cadastre os livros e cópias dos livros, pois são as cópias que possui números de registros e elas que são emprestadas aos usuários, próximo passo efetue o empréstimo na tela de Empréstimos.
