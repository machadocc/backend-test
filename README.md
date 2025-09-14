# Pessoas & Contatos
Aplicação para gerenciamento de **Pessoas e Contatos**, feito com PHP.

## Tecnologias
- Backend
  - [PHP 8.2](https://www.php.net)
  - [Doctrine ORM](https://www.doctrine-project.org)
- Frontend
  - HTML, CSS e JavaScript puro
- Banco de Dados
  - [PostgreSQL 15](https://www.postgresql.org)
- Infraestrutura
  - [Docker](https://www.docker.com)
  - [Docker Compose](https://docs.docker.com/compose/)

## Requisitos
- Docker  
- Docker Compose  

## Configuração da Aplicação

### Variáveis de ambiente
Copiar o arquivo `.env` com base no `.env.example` e preencher as variáveis:
```sh
cp .env.example .env
```

### App
Subir os containers:
```sh
docker compose up -d --build
```

Instalar dependências:
```sh
docker exec php-doctrine composer install
```

Criação do schema do BD:
```sh
docker exec php-doctrine php bin/doctrine.php orm:schema-tool:create
```

## Disponibilidade
Após a configuração, a aplicação estará disponível em:
```sh
http://localhost:8000
```

### Rotas principais
- `/pessoa` → CRUD de Pessoas  
- `/contato` → CRUD de Contatos  

## Estrutura de Pastas
```
/src
  /Controller   → Controladores da aplicação
  /Model        → Entidades e Enums
  /Repository   → Repositórios Doctrine
  /View         → Layouts e formulários

/config         → Configurações (doctrine, bootstrap)
/public         → Arquivos públicos (index.php, CSS, JS)
/bin            → Executável do Doctrine CLI
```




