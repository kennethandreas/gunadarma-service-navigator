# Deployment (InfinityFree + PythonAnywhere)

Configuration files for a zero-cost deployment: the Laravel application runs on
InfinityFree shared hosting, and the intent-classification API (`ml/api.py`) runs on
PythonAnywhere. The local development setup is not affected by anything in this folder.

| File | Destination | Purpose |
|---|---|---|
| `htdocs-index.php` | `htdocs/index.php` | Front controller for a split layout where the Laravel core lives in `htdocs/laravel_app/` (the host does not allow folders above `htdocs/`). |
| `laravel_app-htaccess` | `htdocs/laravel_app/.htaccess` | Denies all direct HTTP access to the application core (`.env`, `storage/`, `vendor/`, …). |
| `pythonanywhere_wsgi.py` | PythonAnywhere → Web → WSGI configuration file | Exposes the Flask app in `ml/api.py` as a WSGI application. |

## 1. AI service (PythonAnywhere)

1. Clone the repository in a PythonAnywhere Bash console.
2. Install dependencies: `pip3 install --user -r ml/requirements.txt`.
3. Create a web app (manual configuration, Python 3.10+), then paste the contents of
   `pythonanywhere_wsgi.py` into the generated WSGI file and replace `<username>`.
4. Reload the web app and note its URL (e.g. `https://<username>.pythonanywhere.com`).

## 2. Build the Laravel release (local machine)

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

Prepare the database locally, then export it for import on the host:

```bash
php artisan migrate --seed          # against a local MySQL/MariaDB database
mysqldump -u root gunadarma_navigator > gunadarma_navigator.sql
```

## 3. Production environment

Create a production `.env` (start from `.env.example`) and set at least:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<your-site>.infinityfreeapp.com
APP_KEY=base64:...                  # php artisan key:generate --show

DB_HOST=<InfinityFree MySQL host>
DB_DATABASE=<database name>
DB_USERNAME=<database user>
DB_PASSWORD=<database password>

AI_SERVICE_URL=https://<username>.pythonanywhere.com
```

Never commit this file.

## 4. Upload (FTP)

```
htdocs/
├── index.php            ← deploy/htdocs-index.php (renamed)
├── .htaccess, build/, favicon.ico, robots.txt   ← contents of public/
└── laravel_app/
    ├── .htaccess        ← deploy/laravel_app-htaccess (renamed)
    ├── .env             ← production environment file
    └── app/, bootstrap/, config/, database/, resources/, routes/, storage/, vendor/, artisan, composer.json
```

Import `gunadarma_navigator.sql` through phpMyAdmin, then verify:

- the homepage loads and AI search returns a confidence score;
- `https://<your-site>/laravel_app/.env` returns **403 Forbidden**.

If the AI service is unreachable, the application automatically falls back to keyword search.

## Alternative: Docker

The `Dockerfile` in the project root builds a self-contained image (Vite assets + PHP 8.2)
suitable for container platforms such as Render. Supply the same environment variables at runtime.
