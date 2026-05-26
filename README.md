# 🍔 FoodExpress

Plataforma web moderna de delivery de comida — Angular SSR + PHP REST API, hospedada exclusivamente no **Render**.

---

## 🏗️ Stack Tecnológico

| Camada | Tecnologia |
|---|---|
| **Frontend** | Angular 21 (SSR) + Tailwind CSS |
| **Backend** | PHP 8.x — REST API pura |
| **Base de Dados** | PostgreSQL (Neon) |
| **Email** | Brevo Transactional API v3 |
| **Hosting** | Render (frontend + backend) |

---

## 🚀 Deploy no Render

O projeto usa `render.yaml` na raiz para configurar ambos os serviços:

| Serviço | Tipo | Porta |
|---|---|---|
| `foodexpress-api` | Web Service (PHP) | 10000 |
| `foodexpress-frontend` | Web Service (Node.js SSR) | 4000 |

### Variáveis de Ambiente — Render Dashboard

Configura manualmente no painel do Render em cada serviço:

**`foodexpress-api` (Backend PHP):**
```
DB_HOST=<neon_host>
DB_NAME=<neon_db>
DB_USER=<neon_user>
DB_PASSWORD=<neon_password>
JWT_SECRET=<segredo_forte>
BREVO_API_KEY=<chave_api_brevo>
BREVO_FROM_EMAIL=noreply@seudominio.com
FRONTEND_URL=https://foodexpress-frontend.onrender.com
```

**`foodexpress-frontend` (Node.js SSR):**
```
PORT=4000
NODE_ENV=production
```

---

## 💻 Desenvolvimento Local

### 1. Backend PHP

```bash
cd backend
php -S localhost:10000 ../backend/router.php
```

### 2. Frontend Angular

```bash
npm install
ng serve
```

Abre `http://localhost:4200` no browser.

---

## 📧 Email (Brevo)

O sistema envia emails transacionais via **Brevo API v3**:

- ✅ **Boas-vindas** — após registo de novo utilizador
- 🔑 **Recuperação de senha** — link de reset com validade de 1h
- 📦 **Confirmação de pedido** — resumo e link de rastreio

Para configurar:
1. Cria conta em [brevo.com](https://brevo.com)
2. Vai a **Settings → API Keys** e cria uma chave
3. Verifica o email remetente em **Senders & IPs**
4. Adiciona `BREVO_API_KEY` e `BREVO_FROM_EMAIL` nas env vars do Render

---

## 📁 Estrutura do Projeto

```
FoodExpress/
├── backend/              # PHP REST API
│   ├── api/              # Endpoints (auth, products, orders, ...)
│   ├── config/           # CORS, Database
│   ├── controllers/      # Lógica de negócio
│   ├── helpers/          # JWT, Response, Env loader
│   ├── services/         # EmailService (Brevo)
│   └── index.php         # Entry point
├── src/                  # Angular frontend
│   ├── app/              # Componentes, páginas, serviços
│   ├── environments/     # Config prod/dev
│   └── server.ts         # SSR Express server
└── render.yaml           # Configuração de deploy Render
```

---

## 🔒 Segurança

- Autenticação JWT (stateless)
- Senhas com bcrypt (cost 12)
- CORS restrito ao domínio do Render
- HTTPS obrigatório em produção
- Variáveis sensíveis via env vars (nunca no código)
