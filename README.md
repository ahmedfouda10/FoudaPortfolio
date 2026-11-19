# Ahmed Fouda — Portfolio

[![CI](https://github.com/ahmedfouda10/FoudaPortfolio/actions/workflows/ci.yml/badge.svg)](https://github.com/ahmedfouda10/FoudaPortfolio/actions/workflows/ci.yml)
![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)
![Laravel 13](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![Filament 5](https://img.shields.io/badge/Filament-5-FDAE4B)
![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)

Source code of my personal portfolio: a server-rendered Laravel application with a
Filament admin panel, where every piece of public content is editable from the dashboard.

**Live site:** `TODO: add URL once deployed`

> 🚧 Work in progress. This README grows with the project. Screenshots, architecture notes
> and deployment docs land in later milestones.

## Tech stack

| Layer            | Choice                                                    |
|------------------|-----------------------------------------------------------|
| Language         | PHP 8.4 (`declare(strict_types=1)` everywhere)            |
| Framework        | Laravel 13                                                |
| Admin panel      | Filament 5                                                |
| Public frontend  | Blade, Livewire 4, Alpine.js, Tailwind CSS v4 (Vite)      |
| Database         | MySQL 8.4                                                 |
| Tests            | Pest 5 (incl. architecture tests)                         |
| Code quality     | Laravel Pint, Larastan (PHPStan level 8)                  |
| Local env        | Laravel Sail (Docker): app, MySQL, Mailpit                |
| CI               | GitHub Actions                                            |

## Local setup (Laravel Sail)

Requirements: Docker, and PHP 8.4 + Composer on the host for the first install
(or use the [Sail-without-PHP](https://laravel.com/docs/sail#installing-composer-dependencies-for-existing-projects) one-liner).

```bash
git clone https://github.com/ahmedfouda10/FoudaPortfolio.git portfolio
cd portfolio

composer install
cp .env.example .env            # then set ADMIN_EMAIL / ADMIN_PASSWORD

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
./vendor/bin/sail artisan queue:work   # contact-form emails are queued
```

| Service      | URL                          |
|--------------|------------------------------|
| Website      | http://localhost             |
| Admin panel  | http://localhost/admin       |
| Mailpit      | http://localhost:8025        |

Ports already in use on your machine? Override `APP_PORT`, `FORWARD_DB_PORT`,
`FORWARD_MAILPIT_PORT` and `FORWARD_MAILPIT_DASHBOARD_PORT` in `.env`.

> Tip: `alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'`

## Quality checks

```bash
sail composer lint         # fix code style with Pint
sail composer test:lint    # check code style (what CI runs)
sail composer test:types   # Larastan
sail artisan test          # Pest
sail composer test         # all of the above
```

Tests use an in-memory SQLite database locally for speed. CI runs the same suite
against MySQL 8.4 to match production.

## License

Released under the [MIT License](LICENSE).
