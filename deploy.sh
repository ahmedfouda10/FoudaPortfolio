#!/usr/bin/env bash
#
# Deploys the portfolio on a single Linux server (no Forge / Vapor needed).
#
# Usage (from the project directory, as the user that owns it):
#   ./deploy.sh            # deploys origin/main
#   ./deploy.sh my-branch  # deploys another branch
#
# Overridable binaries: PHP_BIN, COMPOSER_BIN, NPM_BIN.

set -Eeuo pipefail

BRANCH="${1:-main}"
PHP="${PHP_BIN:-php}"
COMPOSER="${COMPOSER_BIN:-composer}"
NPM="${NPM_BIN:-npm}"

cd "$(dirname "$(readlink -f "$0")")"

step() { printf '\n\033[1;34m→ %s\033[0m\n' "$1"; }

step "Entering maintenance mode"
"$PHP" artisan down --retry=15 --refresh=15 || true

# Whatever happens next, bring the site back up.
trap '"$PHP" artisan up' EXIT

step "Pulling ${BRANCH}"
git fetch --prune origin
git checkout "${BRANCH}"
git pull --ff-only origin "${BRANCH}"

step "Installing PHP dependencies"
"$COMPOSER" install --no-dev --optimize-autoloader --no-interaction --prefer-dist

step "Building front-end assets"
"$NPM" ci --no-audit --no-fund
"$NPM" run build

step "Running database migrations"
"$PHP" artisan migrate --force

step "Linking storage and caching config, routes, views, events and Filament components"
"$PHP" artisan storage:link
"$PHP" artisan optimize
"$PHP" artisan filament:optimize

step "Restarting queue workers (Supervisor starts them again with the new code)"
"$PHP" artisan queue:restart

step "Deployed $(git rev-parse --short HEAD) from ${BRANCH}"
