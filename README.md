# Desafio Técnico Full Stack - Vue.js + Laravel

Aplicação web full stack para gerenciamento de usuários e produtos, construída para o case técnico de Desenvolvedor(a) Laravel e Vue.js.

O projeto foi estruturado com foco em:

- cumprimento dos requisitos obrigatórios do desafio
- organização de código e separação de responsabilidades
- validações consistentes no backend
- API REST em JSON com status HTTP apropriados
- ambiente reproduzível via Docker

## Sumário

- Visão geral
- Stack e arquitetura
- Auditoria dos requisitos do desafio
- Endpoints da API
- Como executar localmente
- Como executar com Docker
- Testes automatizados
- SQL do desafio
- Checklist de entrega para impressionar

## Visão geral

### Funcionalidades implementadas

- Autenticação por token com Laravel Sanctum
- CRUD de usuários com validações de nome, CPF, e-mail e senha
- CRUD de produtos com relacionamento 1:N com usuários
- Dashboard com listagem e ações de usuários
- Tela de produtos com vínculo ao usuário
- Relatório SQL em rota dedicada
- Consultas SQL extras em arquivo separado

### Estrutura principal

- frontend: aplicação Vue 3 + Vuetify
- backend: API Laravel + PostgreSQL
- docker-compose: orquestra frontend, backend e banco

## Stack e arquitetura

### Frontend

- Vue 3
- TypeScript
- Vuetify 3
- Pinia
- Vue Router
- Axios

### Backend

- PHP 8.4
- Laravel 11
- PostgreSQL 15
- Laravel Sanctum
- Form Requests para validação
- PHPUnit para testes

### Infra

- Docker e Docker Compose
- Variáveis sensíveis centralizadas no arquivo .env da raiz
- Backend com startup script que aguarda o banco e roda migrations e seeders

## Auditoria dos requisitos do desafio

### Requisitos obrigatórios

1. CRUD de Usuários: Atendido
2. Campos obrigatórios de usuário (nome, cpf válido, email único e válido, senha mínima): Atendido
3. CRUD de Produtos: Atendido
4. Campos de produto (nome, preco positivo, descricao opcional): Atendido
5. Relacionamento usuário para produtos (1:N): Atendido
6. Validação no backend com mensagens claras: Atendido
7. Respostas JSON com status HTTP apropriados: Atendido
8. Uso de migrations, seeders e Eloquent: Atendido
9. Uso de Axios no frontend: Atendido

### Requisitos opcionais (diferenciais)

1. Docker com docker-compose (app e db): Atendido
2. Testes automatizados com PHPUnit: Atendido
3. Autenticação básica: Atendido (Sanctum)
4. Paginação e filtros em usuários: Atendido no backend
5. Avaliação SQL opcional:
   - Rota GET /relatorio-sql: Atendida
   - Arquivo backend/database/consultas.sql com queries: Atendido

## Endpoints da API

### Públicos

- POST /api/login
- POST /api/users

### Protegidos por token

- POST /api/logout
- GET /api/me
- GET /api/users
- GET /api/users/{id}
- PUT /api/users/{id}
- DELETE /api/users/{id}
- GET /api/products
- POST /api/products
- GET /api/products/{id}
- PUT /api/products/{id}
- DELETE /api/products/{id}
- GET /api/relatorio-sql

## Como executar localmente (sem Docker)

### Pré-requisitos

- PHP 8+
- Composer
- Node 20+
- PostgreSQL 15+

### Passo a passo

1. Clonar o repositório
2. Configurar backend/.env com dados do PostgreSQL local
3. No backend, instalar dependências com composer install
4. No backend, rodar migrations e seeders
5. No backend, iniciar servidor Laravel na porta 8000
6. No frontend, instalar dependências com npm install
7. No frontend, iniciar Vite na porta 5173

## Como executar com Docker

### Pré-requisitos

- Docker Desktop
- Docker Compose

### Passo a passo recomendado

1. Clonar o repositório
2. Criar o arquivo .env na raiz com base em .env.example
3. Ajustar as variáveis da raiz:
   - DB_DATABASE
   - DB_USERNAME
   - DB_PASSWORD
   - DB_PORT
4. Subir ambiente:

   docker-compose up -d --build

5. Aguardar os containers iniciarem
6. Acessar:
   - Frontend: http://localhost:5173
   - Backend API: http://localhost:8000/api

### Comandos úteis

- Parar ambiente: docker-compose down
- Parar e remover volumes: docker-compose down -v
- Ver logs backend: docker logs -f desafio_backend
- Ver logs frontend: docker logs -f desafio_frontend
- Ver logs banco: docker logs -f desafio_db

### Usuário padrão de seed

- email: admin@admin.com
- senha: 01010101

## Testes automatizados

Testes de feature implementados para os cenários centrais:

- criação de usuário com sucesso
- bloqueio de CPF duplicado
- proteção de rota sem autenticação
- criação de produto com usuário autenticado

Executar testes no backend:

php artisan test

## SQL do desafio

### Rota de relatório

- GET /api/relatorio-sql
- Implementada com SQL puro usando DB::select
- Retorna usuários com agregações de produtos e média de preço

### Arquivo de consultas extras

- backend/database/consultas.sql contém:
  - Query A: usuários com mais produtos
  - Query B: produto mais caro por usuário
  - Query C: quantidade de produtos por faixa de preço

## Troubleshooting rápido

1. Erro 500 no login
   - conferir DB_HOST no backend como db quando estiver em Docker
   - garantir que migrations e seeders rodaram

2. Frontend não autentica
   - validar se backend está na porta 8000
   - conferir se token foi salvo no localStorage

3. Banco não conecta
   - validar variáveis de .env da raiz
   - reiniciar stack com docker-compose down -v e docker-compose up -d --build

---
