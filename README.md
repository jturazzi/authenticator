<p align="center">
  <img src="public/favicon.svg" width="80" alt="Authenticator Logo">
</p>

<h1 align="center">Authenticator</h1>

<p align="center">
  A self-hosted TOTP two-factor authentication manager with Microsoft 365 SSO.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-red?logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Vue.js-3-green?logo=vuedotjs" alt="Vue 3">
  <img src="https://img.shields.io/badge/PHP-8.3+-blue?logo=php" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Docker-Ready-blue?logo=docker" alt="Docker">
  <img src="https://img.shields.io/badge/PWA-Supported-purple" alt="PWA">
</p>

---

> 🇫🇷 [Lire en français](README-fr.md)

## Features

- **Microsoft 365 SSO** — Login via Azure AD / Microsoft OAuth
- **TOTP Management** — Add accounts manually or by scanning QR codes
- **QR Code Scanner** — Scan otpauth:// URIs directly from the camera
- **Admin Panel** — Manage users, roles, and TOTP accounts
- **Lock / Unlock** — Prevent accidental deletion of accounts
- **PWA** — Installable as a mobile/desktop app
- **Dark Mode** — Automatic or manual toggle
- **Multilingual** — French and English (FR/EN)
- **SQLite** — Zero-config database, persisted via Docker volume

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Vue 3, Inertia.js, Tailwind CSS 4 |
| Auth | Microsoft OAuth via Laravel Socialite |
| TOTP | PragmaRX Google2FA, chillerlan/php-qrcode |
| Server | FrankenPHP (Caddy) |
| Database | SQLite |
| Container | Docker (multi-stage build) |

## Quick Start (Docker)

### Prerequisites

- Docker & Docker Compose
- A Microsoft Azure AD app registration ([guide](#microsoft-azure-setup))

### 1. Create a `docker-compose.yml`

```yaml
services:
  authenticator:
    image: ghcr.io/jturazzi/authenticator:latest
    container_name: authenticator
    restart: unless-stopped
    ports:
      - "8080:8080"
    volumes:
      - storage-data:/var/www/html/storage
    environment:
      APP_URL: https://auth.example.com
      APP_KEY: ""
      MICROSOFT_CLIENT_ID: "your-client-id"
      MICROSOFT_CLIENT_SECRET: "your-client-secret"
      MICROSOFT_REDIRECT_URI: "https://auth.example.com/auth/microsoft/callback"
      MICROSOFT_TENANT_ID: "your-tenant-id"
    command: ["frankenphp", "run"]
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:8080/up"]
      interval: 10s
      timeout: 5s
      retries: 3
      start_period: 60s

volumes:
  storage-data:
    driver: local
```

### 2. Configure environment variables

| Variable | Description | Example |
|---|---|---|
| `APP_URL` | Public URL of the application | `https://auth.example.com` |
| `APP_KEY` | Laravel encryption key (generate below) | `base64:...` |
| `MICROSOFT_CLIENT_ID` | Azure AD Application (client) ID | `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx` |
| `MICROSOFT_CLIENT_SECRET` | Azure AD Client secret value | `xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` |
| `MICROSOFT_REDIRECT_URI` | OAuth callback URL | `https://auth.example.com/auth/microsoft/callback` |
| `MICROSOFT_TENANT_ID` | Azure AD Directory (tenant) ID | `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx` |

> **Important**: `MICROSOFT_REDIRECT_URI` must match exactly the redirect URI configured in Azure AD.

### 3. Generate APP_KEY

```bash
docker run --rm ghcr.io/jturazzi/authenticator:latest php artisan key:generate --show
```

Copy the output and paste it as the `APP_KEY` value in your `docker-compose.yml`.

### 4. Start

```bash
docker compose up -d
```

The first user to log in is automatically assigned the **admin** role.

## Microsoft Azure Setup

1. Go to [Azure Portal](https://portal.azure.com) → **Azure Active Directory** → **App registrations** → **New registration**
2. **Name**: `Authenticator` (or your choice)
3. **Supported account types**: Accounts in this organizational directory only (Single tenant)
4. **Redirect URI**: Platform `Web` → `https://auth.example.com/auth/microsoft/callback`
5. After creation, note the **Application (client) ID** and **Directory (tenant) ID**
6. Go to **Certificates & secrets** → **New client secret** → copy the **Value**
7. Go to **API permissions** → ensure `User.Read` (Microsoft Graph) is granted

## Reverse Proxy

The application listens on port **8080** (HTTP). Place it behind a reverse proxy for HTTPS.

<details>
<summary>Nginx example</summary>

```nginx
server {
    listen 443 ssl http2;
    server_name auth.example.com;

    ssl_certificate     /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

</details>

<details>
<summary>Traefik labels example</summary>

```yaml
services:
  authenticator:
    # ...
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.auth.rule=Host(`auth.example.com`)"
      - "traefik.http.routers.auth.tls.certresolver=letsencrypt"
      - "traefik.http.services.auth.loadbalancer.server.port=8080"
```

</details>

## CI/CD (GitHub Actions)

The Docker image is automatically built and pushed to **GitHub Container Registry** (`ghcr.io`) on every push to `main` or version tag.

Create `.github/workflows/docker.yml`:

```yaml
name: Build & Push Docker Image

on:
  push:
    branches: [main]
    tags: ["v*"]

jobs:
  build:
    runs-on: ubuntu-latest
    permissions:
      contents: read
      packages: write

    steps:
      - uses: actions/checkout@v4

      - uses: docker/login-action@v3
        with:
          registry: ghcr.io
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}

      - uses: docker/metadata-action@v5
        id: meta
        with:
          images: ghcr.io/${{ github.repository }}
          tags: |
            type=ref,event=branch
            type=semver,pattern={{version}}
            type=sha,prefix=
            type=raw,value=latest,enable={{is_default_branch}}

      - uses: docker/build-push-action@v6
        with:
          context: .
          push: true
          tags: ${{ steps.meta.outputs.tags }}
          labels: ${{ steps.meta.outputs.labels }}
```

## Update

```bash
docker compose pull
docker compose up -d
```

## Local Development

```bash
git clone https://github.com/jturazzi/authenticator.git
cd authenticator
cp .env.example .env
# Fill in MICROSOFT_* variables in .env
composer setup    # Install PHP & Node deps, build assets, migrate
composer dev      # Start dev server (PHP + Vite + queue + logs)
```

## License

MIT — Created by **Jérémy TURAZZI**
