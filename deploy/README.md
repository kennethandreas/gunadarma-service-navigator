# Deployment files (InfinityFree + PythonAnywhere, no credit card)

These files are prepared for the shared-hosting deployment path. Everything
here is ready to use as-is except the values marked `CHANGE-ME`, which only
exist after you've created your InfinityFree and PythonAnywhere accounts.

## What's in this folder

- **`htdocs-index.php`** — goes to InfinityFree, renamed to `index.php`,
  inside `htdocs/`. Do not touch `public/index.php` in the main project —
  that one stays as-is for local development.
- **`.env.production.example`** — copy this, fill in the `CHANGE-ME`
  values from your InfinityFree control panel (database) and PythonAnywhere
  (AI service URL), then rename the copy to `.env` before uploading it
  into `laravel_app/`. `APP_KEY` is already filled in — don't run
  `key:generate` again, that would invalidate it.
- **`pythonanywhere_wsgi.py`** — paste this content into the WSGI
  configuration file PythonAnywhere generates on the Web tab. Replace
  `<username>` with your actual PythonAnywhere username first.

## What you still need to do (steps I can't do for you)

1. Create the InfinityFree and PythonAnywhere accounts.
2. On PythonAnywhere: clone the repo in a Bash console, `pip3 install --user
   -r requirements.txt`, set up the web app, paste in `pythonanywhere_wsgi.py`.
3. On InfinityFree: create the MySQL database, note its host/db/user/password.
4. Locally: `composer install --no-dev --optimize-autoloader`, `npm install`,
   `npm run build` (these need PHP/Composer/Node on your machine — none of
   that is available in my sandbox, so this step has to happen on your side).
5. Locally: point a temporary `.env` at a local MySQL (XAMPP/Laragon), run
   `php artisan migrate --seed`, then `mysqldump` that database to a `.sql`
   file and import it via InfinityFree's phpMyAdmin.
6. Upload via FTP per the folder split described in `htdocs-index.php`'s
   comment header.

Full narrative version of these steps is in the chat where this was
requested — this folder just removes the parts that were pure typing/config
so you only have to do the parts that require clicking through account
dashboards, which I have no access to.
