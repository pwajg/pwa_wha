## CI/CD básico con GitHub Actions (PWA de tesis)

Este proyecto usa un workflow unificado para CI/CD en `.github/workflows/ci.yml` que:

- Ejecuta CI en `push` y `pull_request` hacia `main` y `dev`.
- Frontend: instala, corre tests (si existen), build y despliega a GitHub Pages (solo en `main`).
- Backend (Laravel): instala dependencias, corre PHPUnit (si existe) y, opcionalmente, dispara despliegue a Render con un Deploy Hook (solo en `main`).

### Estructura esperada del repo

- `frontend/` (Vite → build a `dist/` por defecto)
- `backend/` (Laravel)

Si cambias los nombres, ajusta las variables `FRONTEND_DIR`, `BACKEND_DIR` y `FRONTEND_BUILD_DIR` en el workflow.

---

### Ramas sugeridas

- `main`: producción (despliegues automáticos)
- `dev`: integración y pruebas (CI sin despliegue)
- feature branches: `feature/<algo>` → PR contra `dev` → merge a `main` cuando esté listo para prod

---

### Secrets y configuración requerida

1) GitHub Pages (frontend)

- En Settings → Pages, elige "Build and deployment: GitHub Actions".
- El workflow ya sube el artefacto y usa `actions/deploy-pages` en `main`.

2) Render (backend)

- Crea tu servicio en Render (Web Service para Laravel).
- En Render → Service → Settings → "Deploy Hooks" genera un Deploy Hook URL.
- En GitHub → Settings → Secrets and variables → Actions → "New repository secret" agrega:
  - `RENDER_DEPLOY_HOOK_URL`: URL del deploy hook de Render (no requiere token adicional).
- El workflow lo invoca con `curl` solo en pushes a `main`.

> Nota: Render tiene plan gratuito adecuado para entorno académico; la app puede suspenderse en inactividad y reiniciarse al primer acceso.

---

### Variables de entorno (recomendado crear ejemplos)

No se suben `.env` reales. Crea archivos de ejemplo para facilitar onboarding:

Frontend (`frontend/.env.example`):

```
VITE_API_BASE_URL=http://localhost:8000
VITE_APP_NAME=PWA-WHA
```

Backend Laravel (`backend/.env.example`):

```
APP_NAME="PWA-WHA"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pwa_wha
DB_USERNAME=root
DB_PASSWORD=

FRONTEND_URL=http://localhost:5173

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="PWA-WHA"

JWT_SECRET=
```

---

### Cómo probar localmente antes del deploy

Frontend:

```
cd frontend
npm ci
npm test        # si existe
npm run build
```

Backend (Laravel):

```
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed   # si tienes DB local
./vendor/bin/phpunit         # si hay tests
```

Si estos comandos funcionan en local, el workflow debería pasar.

---

### Ampliaciones futuras

- Añadir matrices de versiones (Node/PHP) y linting.
- Caching del build del frontend (Vite) con `actions/cache`.
- Artefactos del backend (por ejemplo, subir coverage o empaquetados).
- Despliegues de preview para PR (Pages o Render Review Apps).


