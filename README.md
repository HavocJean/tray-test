# Tray Test — Full Stack (Laravel API + Vue)

Monorepo com **API Laravel** (`backend/`), **frontend Vue** (`frontend/`) e **Docker** (PHP-FPM, Nginx, MySQL, Redis).

---

## Pré-requisitos

- Docker e Docker Compose
- Conta no [Google Cloud Console](https://console.cloud.google.com/) para credenciais OAuth (tipo *Web application*)

---

## Setup rápido

Na raiz do repositório:

```bash
git clone <seu-repositorio>
cd tray-test

docker compose build
docker compose up -d
```

### 1. Ambiente Laravel (`backend/.env`)

```bash
cp backend/.env.example backend/.env
```

Ajuste pelo menos:

| Variável | Observação |
|----------|------------|
| `APP_KEY` | Gerada no passo abaixo |
| `DB_*` | No Docker, use `DB_HOST=mysql`. Usuário/senha padrão do `docker-compose`: **`user` / `user`** (igual ao serviço MySQL). Se no `.env.example` estiver outro valor, alinhe ao compose. |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` | Credenciais OAuth |
| `GOOGLE_REDIRECT_URI` | Deve coincidir com o Google Console (veja abaixo) |

### 2. Dependências e app key

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### 3. Banco de dados

```bash
docker compose exec app php artisan migrate
```

---

## Subir a API

- **HTTP:** `http://localhost:8000` (Nginx → Laravel em `public/`)
- **Health check:** `GET http://localhost:8000/up`

---

## Rodar o Frontend (Vue)

No diretório `frontend/`:

```bash
cd frontend
npm install
npm run dev
```

- App local: `http://localhost:5173`
- O frontend deve apontar para a API em `http://localhost:8000`

---

## Fila (Redis) e e-mail após cadastro

O envio de e-mail de “cadastro concluído” roda em **fila** (`QUEUE_CONNECTION=redis`).

**Opção A — worker:**

```bash
docker compose up -d queue
```

---

## Google OAuth

1. No Google Cloud Console: **APIs e serviços** → **Credenciais** → criar ID do cliente **OAuth 2.0** (aplicativo Web).
2. **URIs de redirecionamento autorizados** (exemplo local):

   `http://localhost:8000/api/oauth/google/callback`

3. Copie **Client ID** e **Client Secret** para `backend/.env`.
4. Garanta que `GOOGLE_REDIRECT_URI` no `.env` seja **exatamente** a mesma URL cadastrada.

Biblioteca usada: [`google/apiclient`](https://github.com/googleapis/google-api-php-client) (PHP).

---

## Fluxo da API (backend)

| Etapa | O que fazer |
|-------|-------------|
| 1 | `GET /api/oauth/google/url` → resposta JSON com `url` para abrir no navegador |
| 2 | Usuário faz login no Google; o Google redireciona para o **callback** da API |
| 3 | `GET /api/oauth/google/callback?code=...` → API troca o `code` por token, persiste dados do usuário e devolve JSON com `token` (UUID opaco para o front) |
| 4 | Front redireciona para a tela de cadastro e envia `PUT /api/users/complete` com `token`, `name`, `cpf`, `birth_date` |
| 5 | API dispara job na fila para buscar e-mail via Google (token salvo) e enviar e-mail de conclusão de cadastro |
| 6 | `GET /api/users?name=&cpf=&per_page=` → lista os usuarios com paginacao e filtros |

---

## Teste de carga (opcional)

Para popular usuarios/registros e testar a listagem de usuários:

```bash
# opcional: LOAD_TEST_USERS=5000 no .env
docker compose exec app php artisan db:seed --class=LoadTestUserSeeder
```

---

## Comandos úteis

```bash
# Logs do worker de fila (container queue)
docker compose logs -f queue

# Shell no PHP
docker compose exec app bash

# Artisan
docker compose exec app php artisan <comando>
```

---

## Estrutura do repositório

```
tray-test/
├── backend/          # API Laravel
├── frontend/         # VueJS
├── docker/           # Dockerfile PHP, Nginx
├── docker-compose.yml
└── README.md
```


---

## Decisões resumidas

- **Token opaco (`api_token`)** após OAuth em vez de expor ID interno ao front. (Ideal seria utilizar um JWT token por exemplo)
- **Service + Repository** para regras e persistência.
- **Fila Redis** para e-mail pós-cadastro, para envio assíncrono.
- **Filtros** com índices em `name` e `cpf` e paginação obrigatória para suportar grande volume de dados.
- **Login** criado uma fake página de login apenas para ficar mais visual, o que foi desenvolvido é apenas o login com o google
