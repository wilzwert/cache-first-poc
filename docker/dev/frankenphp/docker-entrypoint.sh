#!/bin/sh
set -e

echo "🚀 Starting container as UID=$(id -u) GID=$(id -g)"

# --- Docker socket (Testcontainers) ---
if [ -S /var/run/docker.sock ]; then
    echo "Adjusting permissions on /var/run/docker.sock"
    chown ${UID}:${DOCKER_GID} /var/run/docker.sock || true
fi

# --- Composer install si nécessaire ---
if [ ! -d vendor ] || [ -z "$(ls -A vendor 2>/dev/null)" ]; then
    echo "Installing PHP dependencies via Composer..."
     composer install --optimize-autoloader --prefer-dist --no-progress --no-interaction 2>&1
fi

# --- Wait for database if DATABASE_URL present ---
#if grep -q ^DATABASE_URL= .env; then
#    echo "Waiting for database..."
#    ATTEMPTS_LEFT_TO_REACH_DATABASE=60
#    until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(php bin/console dbal:run-sql -q "SELECT 1" 2>&1); do
#        if [ $? -eq 255 ]; then
#            ATTEMPTS_LEFT_TO_REACH_DATABASE=0
#            break
#        fi
#        sleep 1
#        ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
#        echo "$ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left..."
#    done
#
#    if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
#        echo "Database not reachable:"
#        echo "$DATABASE_ERROR"
#        exit 1
#    fi
#
#    # --- Migrations ---
#    if [ "$(find ./migrations -iname '*.php' -print -quit)" ]; then
#        php bin/console doctrine:migrations:migrate --no-interaction --all-or-nothing
#    fi
#fi

# --- Execute the original command ---
exec docker-php-entrypoint "$@"
