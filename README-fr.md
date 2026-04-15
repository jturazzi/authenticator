<p align="center">
  <img src="public/favicon.svg" width="80" alt="Authenticator Logo">
</p>

<h1 align="center">Authenticator</h1>

<p align="center">
  Gestionnaire de codes TOTP (authentification à double facteur) auto-hébergé avec SSO Microsoft 365.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-red?logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Vue.js-3-green?logo=vuedotjs" alt="Vue 3">
  <img src="https://img.shields.io/badge/PHP-8.3+-blue?logo=php" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Docker-Ready-blue?logo=docker" alt="Docker">
  <img src="https://img.shields.io/badge/PWA-Supported-purple" alt="PWA">
</p>

---

> 🇬🇧 [Read in English](README.md)

## Fonctionnalités

- **SSO Microsoft 365** — Connexion via Azure AD / Microsoft OAuth
- **Gestion TOTP** — Ajout de comptes manuellement ou par scan de QR codes
- **Scanner QR Code** — Scan des URIs otpauth:// directement depuis la caméra
- **Panel Admin** — Gestion des utilisateurs, rôles et comptes TOTP
- **Verrouillage / Déverrouillage** — Protection contre la suppression accidentelle
- **PWA** — Installable comme application mobile/desktop
- **Mode Sombre** — Basculement automatique ou manuel
- **Multilingue** — Français et Anglais (FR/EN)
- **SQLite** — Base de données sans configuration, persistée via volume Docker

## Stack Technique

| Couche | Technologie |
|---|---|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Vue 3, Inertia.js, Tailwind CSS 4 |
| Auth | Microsoft OAuth via Laravel Socialite |
| TOTP | PragmaRX Google2FA, chillerlan/php-qrcode |
| Serveur | FrankenPHP (Caddy) |
| Base de données | SQLite |
| Conteneur | Docker (build multi-stage) |

## Démarrage Rapide (Docker)

### Prérequis

- Docker & Docker Compose
- Une inscription d'application Azure AD ([guide](#configuration-microsoft-azure))

### 1. Créer un `docker-compose.yml`

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

### 2. Configurer les variables d'environnement

| Variable | Description | Exemple |
|---|---|---|
| `APP_URL` | URL publique de l'application | `https://auth.example.com` |
| `APP_KEY` | Clé de chiffrement Laravel (générer ci-dessous) | `base64:...` |
| `MICROSOFT_CLIENT_ID` | ID d'application (client) Azure AD | `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx` |
| `MICROSOFT_CLIENT_SECRET` | Valeur du secret client Azure AD | `xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` |
| `MICROSOFT_REDIRECT_URI` | URL de callback OAuth | `https://auth.example.com/auth/microsoft/callback` |
| `MICROSOFT_TENANT_ID` | ID de l'annuaire (tenant) Azure AD | `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx` |

> **Important** : `MICROSOFT_REDIRECT_URI` doit correspondre exactement à l'URI de redirection configurée dans Azure AD.

### 3. Générer la APP_KEY

```bash
docker run --rm ghcr.io/jturazzi/authenticator:latest php artisan key:generate --show
```

Copiez la sortie et collez-la comme valeur de `APP_KEY` dans votre `docker-compose.yml`.

### 4. Démarrer

```bash
docker compose up -d
```

Le premier utilisateur à se connecter reçoit automatiquement le rôle **administrateur**.

## Configuration Microsoft Azure

1. Aller sur le [Portail Azure](https://portal.azure.com) → **Azure Active Directory** → **Inscriptions d'applications** → **Nouvelle inscription**
2. **Nom** : `Authenticator` (ou au choix)
3. **Types de comptes pris en charge** : Comptes dans cet annuaire d'organisation uniquement (Monolocataire)
4. **URI de redirection** : Plateforme `Web` → `https://auth.example.com/auth/microsoft/callback`
5. Après création, noter l'**ID d'application (client)** et l'**ID de l'annuaire (locataire)**
6. Aller dans **Certificats et secrets** → **Nouveau secret client** → copier la **Valeur**
7. Aller dans **Autorisations d'API** → vérifier que `User.Read` (Microsoft Graph) est accordé

## Reverse Proxy

L'application écoute sur le port **8080** (HTTP). Placez-la derrière un reverse proxy pour le HTTPS.

<details>
<summary>Exemple Nginx</summary>

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
<summary>Exemple labels Traefik</summary>

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

L'image Docker est automatiquement construite et poussée sur **GitHub Container Registry** (`ghcr.io`) à chaque push sur `main` ou tag de version.

Créer `.github/workflows/docker.yml` :

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

## Mise à jour

```bash
docker compose pull
docker compose up -d
```

## Développement Local

```bash
git clone https://github.com/jturazzi/authenticator.git
cd authenticator
cp .env.example .env
# Remplir les variables MICROSOFT_* dans .env
composer setup    # Installe les dépendances PHP & Node, build les assets, migre la BDD
composer dev      # Démarre le serveur de dev (PHP + Vite + queue + logs)
```

## Licence

MIT — Créé par **Jérémy TURAZZI**
