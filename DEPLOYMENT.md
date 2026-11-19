# Deployment

The portfolio runs on a single Linux server (Ubuntu 24.04 is assumed below) with
Nginx, PHP-FPM, MySQL, a queue worker managed by Supervisor and a cron entry for
the scheduler. No Forge, Vapor or containers are required in production.

## Server requirements

| Component | Version / notes |
|---|---|
| PHP (CLI + FPM) | **8.4** |
| PHP extensions | `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd` (with **WebP** support), `intl` (required by Filament), `mbstring`, `openssl`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `zip` |
| Composer | 2.x |
| MySQL | 8.0 or newer (8.4 is used in development and CI) |
| Node.js + npm | **22 LTS**, only to build the front-end assets during deploys |
| Web server | Nginx (example config in [`deploy/nginx/portfolio.conf`](deploy/nginx/portfolio.conf)) |
| Process manager | Supervisor (example config in [`deploy/supervisor/portfolio-worker.conf`](deploy/supervisor/portfolio-worker.conf)) |
| HTTPS | Let's Encrypt via Certbot (or any certificate) |

Check GD's WebP support with `php -r "var_dump(gd_info()['WebP Support']);"`.

## First-time setup

```bash
# 1. Get the code (as the deploy user, e.g. www-data or a dedicated user)
sudo mkdir -p /var/www/portfolio && sudo chown "$USER":www-data /var/www/portfolio
git clone https://github.com/ahmedfouda10/FoudaPortfolio.git /var/www/portfolio
cd /var/www/portfolio

# 2. Environment
cp .env.example .env
php artisan key:generate
```

Edit `.env` for production:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com
LOG_LEVEL=warning

DB_HOST=127.0.0.1
DB_DATABASE=portfolio
DB_USERNAME=portfolio
DB_PASSWORD=<strong password>

SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=<smtp host>
MAIL_PORT=587
MAIL_USERNAME=<smtp user>
MAIL_PASSWORD=<smtp password>
MAIL_FROM_ADDRESS=hello@example.com

ADMIN_NAME="Your Name"
ADMIN_EMAIL=<the address you log in with>
ADMIN_PASSWORD=<strong password>
ADMIN_REQUIRE_MFA=true

# Optional: pre-fill the private contact details in Site settings
CONTACT_EMAIL=
CONTACT_PHONE=
CONTACT_WHATSAPP=

# Only when the server sits behind a load balancer / Cloudflare
TRUSTED_PROXIES=
```

```bash
# 3. Install, build, migrate and seed the content + admin user
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build
php artisan migrate --force --seed
php artisan storage:link
php artisan optimize && php artisan filament:optimize

# 4. Permissions: the web server writes to storage/ and bootstrap/cache/
sudo chown -R "$USER":www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache
```

After the first login at `https://example.com/admin`, Filament asks you to set up
an authenticator app (MFA). Store the recovery codes somewhere safe.

## Web server

Copy [`deploy/nginx/portfolio.conf`](deploy/nginx/portfolio.conf), replace the
domain and paths, enable it and add a certificate:

```bash
sudo cp deploy/nginx/portfolio.conf /etc/nginx/sites-available/portfolio
sudo ln -s /etc/nginx/sites-available/portfolio /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d example.com -d www.example.com
```

The config enables gzip and long-lived caching for `/build` (fingerprinted Vite
assets and self-hosted fonts) and `/storage` (uploads and their WebP variants).

## Queue worker

Contact-form emails and image variants are queued. Run the worker under Supervisor:

```bash
sudo cp deploy/supervisor/portfolio-worker.conf /etc/supervisor/conf.d/
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start "portfolio-worker:*"
```

## Scheduler

Add one cron entry for the deploy user (`crontab -e`):

```cron
* * * * * cd /var/www/portfolio && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks are declared in [`routes/console.php`](routes/console.php).

## Deploying updates

```bash
cd /var/www/portfolio && ./deploy.sh        # or: ./deploy.sh some-branch
```

[`deploy.sh`](deploy.sh) puts the site in maintenance mode, pulls the branch
(fast-forward only), installs Composer dependencies without dev packages, builds
the assets, runs migrations, caches config/routes/views/events and Filament
components, restarts the queue workers and always brings the site back up, even
if a step fails.

## Useful commands

| Command | Purpose |
|---|---|
| `php artisan images:variants` | Rebuild the responsive WebP variants of every uploaded image |
| `php artisan db:seed --class=AdminUserSeeder --force` | Re-create the admin user / reset its password from `.env` |
| `php artisan optimize:clear` | Clear all framework caches (e.g. after editing `.env`) |
| `php artisan queue:failed` | Inspect failed jobs (e.g. SMTP problems) |

## Backups

Back up the database and `storage/app` (uploads and the private CV), for example
with a nightly `mysqldump` and an off-site copy of both.
